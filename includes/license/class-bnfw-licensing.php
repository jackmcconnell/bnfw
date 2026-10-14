<?php
/**
 * Central licensing bridge for BNFW premium add-ons.
 *
 * Registers each installed premium add-on with the EDD Software Licensing SDK
 * and performs a one-time migration of license keys that were stored by the
 * legacy BNFW_License implementation, so existing customers do not have to
 * re-enter or re-activate their licenses.
 *
 * @since 1.9.9.3
 * @package bnfw
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BNFW_Licensing', false ) ) {
	/**
	 * BNFW_Licensing class.
	 */
	class BNFW_Licensing {

		/**
		 * The EDD store URL that issues the licenses.
		 *
		 * @var string
		 */
		const STORE_URL = 'https://betternotificationsforwp.com/';

		/**
		 * Hook into the SDK registry and the migration routine.
		 *
		 * @since 1.9.9.3
		 */
		public static function init() {
			add_action( 'edd_sl_sdk_registry', array( __CLASS__, 'register_addons' ) );
			add_action( 'admin_init', array( __CLASS__, 'maybe_migrate' ) );

			// Correct the SDK's asset URLs. The SDK derives them from its own file
			// path, which produces a broken URL when the plugin is loaded from a
			// symlink outside wp-content (common in local development). WordPress's
			// plugins_url() resolves symlinked plugin directories correctly.
			add_filter( 'style_loader_src', array( __CLASS__, 'fix_sdk_asset_url' ), 10, 2 );
			add_filter( 'script_loader_src', array( __CLASS__, 'fix_sdk_asset_url' ), 10, 2 );

			// Customise SDK interface strings for BNFW.
			add_filter( 'edd_sl_sdk_translate_string', array( __CLASS__, 'filter_sdk_strings' ), 10, 2 );

			// Show an at-a-glance licence status on each add-on row in the plugins list.
			add_filter( 'plugin_action_links', array( __CLASS__, 'add_license_status' ), 99, 2 );

			// Live-refresh that status after a licence change, without a page reload.
			add_action( 'wp_ajax_bnfw_addon_license_status', array( __CLASS__, 'ajax_license_status' ) );
			add_action( 'admin_footer-plugins.php', array( __CLASS__, 'print_status_refresh_script' ) );

			// Keep the stored licence status honest. The SDK only persists a
			// *successful* check, so a key the store reports as invalid, deleted,
			// disabled or revoked keeps showing its last known (active) status.
			// Re-validate daily and persist the store's authoritative answer.
			add_action( 'bnfw_revalidate_licenses', array( __CLASS__, 'revalidate_licenses' ) );
			if ( ! wp_next_scheduled( 'bnfw_revalidate_licenses' ) ) {
				wp_schedule_event( time(), 'daily', 'bnfw_revalidate_licenses' );
			}
		}

		/**
		 * Re-validate every installed add-on's licence against the store and persist
		 * the authoritative result.
		 *
		 * The SDK's own weekly check bails on any non-success response, so an
		 * invalid, deleted, disabled or revoked key would otherwise keep displaying
		 * its last-known active status. Here we persist whatever definitive answer
		 * the store returns. A transient network failure returns false (not an
		 * object), so in that case the last-known status is deliberately left alone.
		 *
		 * @since 1.10
		 */
		public static function revalidate_licenses() {
			if ( ! class_exists( '\EasyDigitalDownloads\Updater\Requests\API' ) ) {
				return;
			}

			$api = new \EasyDigitalDownloads\Updater\Requests\API( self::STORE_URL );

			foreach ( self::get_addons() as $addon ) {
				$info = self::normalize( $addon );
				if ( ! $info ) {
					continue;
				}

				$key = get_option( $info['slug'] . '_license_key' );
				if ( empty( $key ) ) {
					continue;
				}

				$response = $api->make_request(
					array(
						'edd_action' => 'check_license',
						'license'    => $key,
						'item_id'    => $info['item_id'],
					)
				);

				// Only an authoritative store response (an object with a licence
				// status) updates the stored status; a network failure returns
				// false and is ignored so we never downgrade on a transient blip.
				if ( is_object( $response ) && isset( $response->license ) ) {
					update_option( $info['slug'] . '_license', $response );
				}
			}
		}

		/**
		 * AJAX: return the current licence status indicator HTML for one add-on.
		 *
		 * Used to refresh the plugins-list row after the licence is changed in the
		 * Manage License overlay, so the user does not have to reload the page.
		 *
		 * @since 1.10
		 */
		public static function ajax_license_status() {
			check_ajax_referer( 'bnfw_license_status', 'nonce' );

			if ( ! current_user_can( 'activate_plugins' ) ) {
				wp_send_json_error( '', 403 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';

			$valid = false;
			foreach ( self::get_addons() as $addon ) {
				if ( ! empty( $addon['basename'] ) && dirname( ltrim( $addon['basename'], '/' ) ) === $slug ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				wp_send_json_error();
			}

			wp_send_json_success( self::get_license_status_indicator( $slug ) );
		}

		/**
		 * Print the inline script that refreshes a row's licence status after the
		 * Manage License overlay closes. Inlined (no enqueued file) so it is immune
		 * to the SDK's asset-URL quirks on symlinked installs.
		 *
		 * @since 1.10
		 */
		public static function print_status_refresh_script() {
			$nonce = wp_create_nonce( 'bnfw_license_status' );
			?>
			<script>
			( function () {
				var nonce    = <?php echo wp_json_encode( $nonce ); ?>;
				var lastSlug = null;

				// Remember which add-on's Manage License control was opened.
				document.addEventListener( 'click', function ( e ) {
					var trigger = e.target.closest( '.edd-sdk__notice__trigger--ajax' );
					if ( trigger ) {
						lastSlug = trigger.getAttribute( 'data-slug' );
					}
				} );

				// When the overlay closes, refresh that row's status from the server.
				document.addEventListener( 'edd_sdk_notice_dismiss', function () {
					if ( ! lastSlug || typeof window.ajaxurl === 'undefined' ) {
						return;
					}
					var slug = lastSlug;
					var el   = document.querySelector( '.bnfw-license-status[data-slug="' + slug + '"]' );
					if ( ! el ) {
						return;
					}
					fetch( window.ajaxurl, {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: new URLSearchParams( { action: 'bnfw_addon_license_status', slug: slug, nonce: nonce } ),
					} )
						.then( function ( r ) { return r.json(); } )
						.then( function ( res ) {
							if ( res && res.success ) {
								el.innerHTML = res.data;
							}
						} )
						.catch( function () {} );
				} );
			} )();
			</script>
			<?php
		}

		/**
		 * Append a licence status indicator to an add-on's row on the plugins list.
		 *
		 * Lets users see whether an add-on's licence is active, expired, inactive or
		 * missing without opening the "Manage License" control.
		 *
		 * @since 1.10
		 *
		 * @param string[] $actions     The plugin's action links.
		 * @param string   $plugin_file The plugin file, relative to the plugins dir.
		 *
		 * @return string[] The (possibly augmented) action links.
		 */
		public static function add_license_status( $actions, $plugin_file ) {
			static $basenames = null;
			if ( null === $basenames ) {
				$basenames = array();
				foreach ( self::get_addons() as $addon ) {
					if ( ! empty( $addon['basename'] ) ) {
						$basenames[ ltrim( $addon['basename'], '/' ) ] = true;
					}
				}
			}

			if ( empty( $basenames[ $plugin_file ] ) ) {
				return $actions;
			}

			// The SDK derives its option names from the add-on's folder (slug).
			$slug = dirname( $plugin_file );

			// Wrapped with the slug so our JS can refresh it in place after the
			// licence is activated/deactivated in the Manage License overlay.
			$actions['bnfw_license_status'] = sprintf(
				'<span class="bnfw-license-status" data-slug="%1$s">%2$s</span>',
				esc_attr( $slug ),
				self::get_license_status_indicator( $slug )
			);

			return $actions;
		}

		/**
		 * Build the coloured licence status label for an add-on.
		 *
		 * @since 1.10
		 *
		 * @param string $slug The add-on slug (its plugin folder name).
		 *
		 * @return string HTML for a non-link status indicator.
		 */
		private static function get_license_status_indicator( $slug ) {
			$key = get_option( $slug . '_license_key' );

			if ( empty( $key ) ) {
				return self::status_span( __( 'No licence key', 'bnfw' ), '#b32d2e' );
			}

			$license = get_option( $slug . '_license' );
			$status  = ( is_object( $license ) && ! empty( $license->license ) ) ? $license->license : '';

			// A licence whose stored expiry date has passed is expired, even if the
			// cached status (from the last store check) still reads otherwise.
			if ( 'expired' !== $status && is_object( $license ) && ! empty( $license->expires ) && 'lifetime' !== $license->expires ) {
				$expires_ts = is_numeric( $license->expires ) ? (int) $license->expires : strtotime( $license->expires );
				if ( $expires_ts && $expires_ts < time() ) {
					$status = 'expired';
				}
			}

			switch ( $status ) {
				case 'valid':
					return self::status_span( __( 'Licence active', 'bnfw' ), '#007017' );
				case 'expired':
					return self::status_span( __( 'Licence expired', 'bnfw' ), '#b32d2e' );
				case 'disabled':
				case 'revoked':
					return self::status_span( __( 'Licence disabled', 'bnfw' ), '#b32d2e' );
				case 'no_activations_left':
					return self::status_span( __( 'No activations left', 'bnfw' ), '#996800' );
				case 'inactive':
				case 'site_inactive':
					return self::status_span( __( 'Licence not activated', 'bnfw' ), '#996800' );
				case 'invalid':
				case 'item_name_mismatch':
				case 'key_mismatch':
				case 'missing':
					return self::status_span( __( 'Licence invalid', 'bnfw' ), '#b32d2e' );
				default:
					// A key is saved but the status has not been confirmed yet.
					return self::status_span( __( 'Licence not activated', 'bnfw' ), '#996800' );
			}
		}

		/**
		 * Render a non-link, coloured status label for the plugins list row.
		 *
		 * @since 1.10
		 *
		 * @param string $text  The status text.
		 * @param string $color A hex colour for the label and its dot.
		 *
		 * @return string
		 */
		private static function status_span( $text, $color ) {
			return sprintf(
				'<span style="color:%1$s;font-weight:600;" aria-hidden="true">&#9679;</span> <span style="font-weight:600;">%2$s</span>',
				esc_attr( $color ),
				esc_html( $text )
			);
		}

		/**
		 * Override specific EDD SL SDK interface strings.
		 *
		 * @since 1.10
		 *
		 * @param string $text The SDK's default (translated) string.
		 * @param string $key  The string identifier.
		 *
		 * @return string The string to display.
		 */
		public static function filter_sdk_strings( $text, $key ) {
			if ( 'data_tracking_label' === $key ) {
				return __( 'Allow Better Notifications for WP to collect usage data about your site, such as the PHP and WordPress versions.', 'bnfw' );
			}

			return $text;
		}

		/**
		 * Rebuild the EDD SL SDK's enqueued asset URL via plugins_url().
		 *
		 * The SDK enqueues its JS and CSS under the `edd-sdk-notice` handle, deriving
		 * the URL from the SDK directory path. That breaks when this plugin lives at a
		 * symlink pointing outside wp-content. We rebuild the URL from this plugin's
		 * own location using plugins_url(), which understands symlinked plugin dirs.
		 *
		 * @since 1.10
		 *
		 * @param string $src    The asset source URL.
		 * @param string $handle The asset handle.
		 *
		 * @return string The (possibly corrected) source URL.
		 */
		public static function fix_sdk_asset_url( $src, $handle ) {
			if ( 'edd-sdk-notice' !== $handle ) {
				return $src;
			}

			$marker = 'vendor/easy-digital-downloads/edd-sl-sdk/';
			$pos    = strpos( $src, $marker );
			if ( false === $pos ) {
				return $src;
			}

			// The asset path relative to this plugin, plus any trailing query string.
			$relative = substr( $src, $pos );
			$query    = '';
			$q_pos    = strpos( $relative, '?' );
			if ( false !== $q_pos ) {
				$query    = substr( $relative, $q_pos );
				$relative = substr( $relative, 0, $q_pos );
			}

			// dirname( dirname( __DIR__ ) ) is the plugin root (this file lives in
			// includes/license/). plugins_url() maps the symlink realpath back to the
			// real plugin URL.
			$main_file = dirname( dirname( __DIR__ ) ) . '/bnfw.php';

			return plugins_url( $relative, $main_file ) . $query;
		}

		/**
		 * Catalog of premium add-ons.
		 *
		 * Add one entry per premium add-on. Each entry needs:
		 *  - 'basename'         : the add-on's plugin file relative to the plugins
		 *                         directory, e.g. 'bnfw-twilio/bnfw-twilio.php'. The
		 *                         SDK derives the license option names from the
		 *                         add-on's folder name, so this must be accurate.
		 *  - 'item_id'          : the numeric EDD Download ID for the add-on.
		 *  - 'legacy_item_name' : the exact item name that used to be passed to
		 *                         BNFW_License( ... ), used only to locate the old
		 *                         saved key during migration. Omit for add-ons that
		 *                         never shipped under the legacy system.
		 *
		 * The `bnfw_addon_licenses` filter lets add-ons (or site code) append
		 * entries dynamically instead of hard-coding them here.
		 *
		 * @since 1.9.9.3
		 *
		 * @return array[] List of add-on definitions.
		 */
		public static function get_addons() {
			$addons = array(
				array(
					'basename'         => 'bnfw-conditional/bnfw-conditional.php',
					'item_id'          => 37,
					'legacy_item_name' => 'Conditional Notifications Add-on',
				),
				array(
					'basename'         => 'bnfw-custom-fields/bnfw-custom-fields.php',
					'item_id'          => 39,
					'legacy_item_name' => 'Custom Fields Add-on',
				),
				array(
					'basename'         => 'bnfw-digest/bnfw-digest.php',
					'item_id'          => 993780,
					'legacy_item_name' => 'Digest Add-on',
				),
				array(
					'basename'         => 'bnfw-multilanguage/bnfw-multilanguage.php',
					'item_id'          => 1037113,
					'legacy_item_name' => 'Multi-language Add-on',
				),
				array(
					'basename'         => 'bnfw-multisite/bnfw-multisite.php',
					'item_id'          => 574939,
					'legacy_item_name' => 'Multisite Add-on',
				),
				array(
					'basename'         => 'bnfw-per-post-override/bnfw-per-post-override.php',
					'item_id'          => 448,
					'legacy_item_name' => 'Global Override Add-on',
				),
				array(
					'basename'         => 'bnfw-profile-builder/bnfw-profile-builder.php',
					'item_id'          => 924228,
					'legacy_item_name' => 'Profile Builder Add-on',
				),
				array(
					'basename'         => 'bnfw-send-to-any-email/bnfw-send-to-any-email.php',
					'item_id'          => 575004,
					'legacy_item_name' => 'Send to Any Email Add-on',
				),
				array(
					'basename'         => 'bnfw-subscriptions/bnfw-subscriptions.php',
					'item_id'          => 984750,
					'legacy_item_name' => 'Subscriptions Add-on',
				),
				array(
					'basename'         => 'bnfw-update-reminder/bnfw-update-reminder.php',
					'item_id'          => 1080,
					'legacy_item_name' => 'Reminders Add-on',
				),
			);

			/**
			 * Filters the list of BNFW premium add-ons to license via the SDK.
			 *
			 * @since 1.9.9.3
			 *
			 * @param array[] $addons List of add-on definitions.
			 */
			return (array) apply_filters( 'bnfw_addon_licenses', $addons );
		}

		/**
		 * Register every installed add-on with the EDD SL SDK.
		 *
		 * @since 1.9.9.3
		 *
		 * @param object $registry The SDK registry passed by the edd_sl_sdk_registry action.
		 */
		public static function register_addons( $registry ) {
			if ( ! is_object( $registry ) || ! method_exists( $registry, 'register' ) ) {
				return;
			}

			foreach ( self::get_addons() as $addon ) {
				$info = self::normalize( $addon );
				if ( ! $info ) {
					continue;
				}

				try {
					$registry->register(
						array(
							'id'      => $info['slug'],
							'url'     => self::STORE_URL,
							'item_id' => $info['item_id'],
							'version' => $info['version'],
							'file'    => $info['file'],
						)
					);
				} catch ( Exception $e ) {
					// Already registered or invalid definition; skip quietly.
					continue;
				}
			}
		}

		/**
		 * Copy legacy license keys into the SDK's storage, once per add-on.
		 *
		 * Runs in the admin only. For each add-on that has a legacy key but no SDK
		 * key yet, the key is copied and a read-only status check is performed so
		 * the true current status is recorded. Activation is never triggered, so an
		 * expired or otherwise invalid license is reported honestly and never
		 * force-activated.
		 *
		 * @since 1.9.9.3
		 */
		public static function maybe_migrate() {
			if ( ! class_exists( '\EasyDigitalDownloads\Updater\Requests\API' ) ) {
				// SDK not loaded yet; nothing has been marked done, so this retries.
				return;
			}

			$old_licenses = get_option( 'bnfw_licenses' );
			$old_licenses = is_array( $old_licenses ) ? $old_licenses : array();

			foreach ( self::get_addons() as $addon ) {
				$info = self::normalize( $addon );
				if ( ! $info ) {
					continue;
				}
				self::migrate_addon( $info, $old_licenses );
			}
		}

		/**
		 * Normalize a catalog entry into resolved licensing details.
		 *
		 * Returns false when the add-on is not installed or the entry is incomplete,
		 * so uninstalled add-ons are simply skipped (and picked up later if installed).
		 *
		 * @since 1.9.9.3
		 *
		 * @param array $addon Raw catalog entry.
		 *
		 * @return array|false Normalized details, or false to skip.
		 */
		private static function normalize( $addon ) {
			if ( empty( $addon['basename'] ) || empty( $addon['item_id'] ) ) {
				return false;
			}

			$file = trailingslashit( WP_PLUGIN_DIR ) . ltrim( $addon['basename'], '/' );
			if ( ! file_exists( $file ) ) {
				return false; // Add-on not installed on this site.
			}

			// The SDK uses the add-on's folder name as the license slug.
			$slug = dirname( plugin_basename( $file ) );
			if ( '.' === $slug || '' === $slug ) {
				$slug = basename( $file, '.php' );
			}

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$data    = get_plugin_data( $file, false, false );
			$version = ! empty( $data['Version'] ) ? $data['Version'] : '';

			return array(
				'slug'             => $slug,
				'file'             => $file,
				'version'          => $version,
				'item_id'          => (int) $addon['item_id'],
				'legacy_item_name' => isset( $addon['legacy_item_name'] ) ? $addon['legacy_item_name'] : '',
			);
		}

		/**
		 * Migrate a single add-on's license key into the SDK storage.
		 *
		 * @since 1.9.9.3
		 *
		 * @param array $info         Normalized add-on details.
		 * @param array $old_licenses The legacy `bnfw_licenses` option value.
		 */
		private static function migrate_addon( $info, $old_licenses ) {
			$key_option    = $info['slug'] . '_license_key';
			$status_option = $info['slug'] . '_license';
			$done_option   = $info['slug'] . '_bnfw_license_migrated';

			// Migration is strictly one-time. Once it has run for this add-on we
			// must never run again, otherwise a key the user deletes via the
			// licence UI would be resurrected from the legacy option on the next
			// admin request.
			if ( get_option( $done_option ) ) {
				return;
			}

			// If the SDK already holds a key (migrated previously, or entered
			// under the new system), record completion and stop.
			if ( get_option( $key_option ) ) {
				update_option( $done_option, 1 );
				return;
			}

			$legacy_key = self::get_legacy_key( $info['legacy_item_name'], $old_licenses );

			// Mark migration complete regardless of the outcome, so it never
			// re-runs (and never resurrects a deleted key).
			update_option( $done_option, 1 );

			if ( '' === $legacy_key ) {
				return;
			}

			// Preserve the key so the customer never has to re-enter it.
			update_option( $key_option, $legacy_key );

			// Read-only status check. We deliberately use `check_license`, never
			// `activate_license`, so expired/invalid keys are reported truthfully
			// and nothing is silently (re)activated.
			$api      = new \EasyDigitalDownloads\Updater\Requests\API( self::STORE_URL );
			$response = $api->make_request(
				array(
					'edd_action' => 'check_license',
					'license'    => $legacy_key,
					'item_id'    => $info['item_id'],
				)
			);

			if ( is_object( $response ) && isset( $response->license ) ) {
				update_option( $status_option, $response );
			}
		}

		/**
		 * Locate a legacy license key inside the old `bnfw_licenses` option.
		 *
		 * Reproduces the item-shortname scheme used by the old BNFW_License class.
		 *
		 * @since 1.9.9.3
		 *
		 * @param string $item_name    Legacy item name.
		 * @param array  $old_licenses The legacy `bnfw_licenses` option value.
		 *
		 * @return string The key, or an empty string when not found.
		 */
		private static function get_legacy_key( $item_name, $old_licenses ) {
			if ( '' === $item_name || empty( $old_licenses ) ) {
				return '';
			}

			$shortname = 'bnfw_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $item_name ) ) );
			$key       = $shortname . '_license_key';

			return isset( $old_licenses[ $key ] ) ? trim( $old_licenses[ $key ] ) : '';
		}
	}

	// Register hooks during normal operation only. During uninstall the catalog
	// is reused by uninstall.php, which must not re-register hooks or re-schedule
	// the cron.
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		BNFW_Licensing::init();
	}
}
