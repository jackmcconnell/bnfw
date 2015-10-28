jQuery(document).ready(function($) {
    function toggle_fields() {
    	var show_fields = $('#show-fields').is(":checked");

    	if ( show_fields ) {
			$('#email, #cc, #bcc').show();
    	} else {
			$('#email, #cc, #bcc').hide();
    	}
    }

    function toggle_users() {
    	if ( $( '#only-post-author' ).is( ':checked' ) ) {
    		$( '#users, #current-user' ).hide();
    	} else {
    		$( '#users, #current-user' ).show();
    	}
    }

	function init() {
		$(".select2").select2();
		toggle_fields();

		if ( 'user-password' === $('#notification').val() || 'new-user' === $('#notification').val() || 'welcome-email' === $('#notification').val() || 'reply-comment' === $('#notification').val() ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #email-formatting, #disable-autop, #current-user, #post-author').hide();
			$('#user-password-msg').show();
		} else if ( 'new-comment' === $('#notification').val() || 'new-trackback' === $('#notification').val() || 'new-pingback' === $('#notification').val() || 'admin-password' === $('#notification').val() || 'admin-user' === $('#notification').val() ) {
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user').show();
			$('#only-post-author').prop( 'checked', false );
			$('#post-author').hide();
			toggle_fields();
			$('#user-password-msg').hide();
		} else {
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			toggle_fields();
			toggle_users();
			$('#user-password-msg').hide();
		}
	}

	init();
    $('#notification').on('change', function() {
		var $this = $(this);
		if ( 'user-password' === $this.val() || 'new-user' === $this.val() || 'welcome-email' === $this.val() || 'reply-comment' === $this.val() ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #email-formatting, #disable-autop, #current-user, #post-author').hide();
			$('#user-password-msg').show();
		} else if ( 'new-comment' === $('#notification').val() || 'new-trackback' === $('#notification').val() || 'new-pingback' === $('#notification').val() || 'admin-password' === $('#notification').val() || 'admin-user' === $('#notification').val() ) {
			$('#post-author').hide();
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user').show();
			$('#user-password-msg').hide();
			toggle_fields();
		} else {
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			$('#user-password-msg').hide();
			toggle_fields();
		}
    });

    $('#show-fields').change(function() {
    	toggle_fields();
    });

    $( '#only-post-author' ).change(function() {
		toggle_users();
	} );

	// send test email
	$( '#test-email' ).click(function() {
		$( '#send-test-email' ).val( 'true' );
	});

	$( '#shortcode-help' ).on( 'click', function() {
		var notification = $( '#notification' ).val(),
			notification_slug = '',
			splited;

		switch( notification ) {
			case 'new-comment':
			case 'new-trackback':
			case 'new-pingback':
			case 'reply-comment':
			case 'user-password':
			case 'admin-password':
			case 'new-user':
			case 'welcome-email':
			case 'admin-user':
			case 'new-post':
			case 'update-post':
			case 'pending-post':
			case 'future-post':
			case 'newterm-category':
			case 'newterm-post_tag':
				notification_slug = notification;
				break;

			default:
				splited = notification.split( '-' );
				switch( splited[0] ) {
					case 'new':
					case 'update':
					case 'pending':
					case 'future':
					case 'comment':
						notification_slug = splited[0] + '-post';
						break;
					case 'newterm':
						notification_slug = 'newterm-category';
						break;
				}

				break;
		}

		$(this).attr( 'href', 'https://betternotificationsforwp.com/shortcodes/?notification=' + notification_slug );
	});
});
