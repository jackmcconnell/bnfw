=== Customize WordPress Emails and Alerts - Better Notifications for WP ===
Contributors: voltronik, bnfwsupport
Donate link: https://betternotificationsforwp.com/donate/
Tags: notification, email, alert, message, notify
Requires at least: 4.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: "1.9.9"
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Update URI: https://wordpress.org/plugins/bnfw/

Supercharge your WordPress email notifications using a WYSIWYG editor and shortcodes. Default and new notifications available. Add-ons available.



== Description ==

Better Notifications for WP is a simple but powerful plugin for that allows you to customise the email notifications that WordPress sends using a WYSIWYG editor and shortcodes. All of the default WordPress email notifications are available to customise as well as lots of new ones. You can choose to send notifications to individual users, multiple users, all users in a user role, multiple roles - you name it! You can also power-up your notifications with [Premium Add-ons](https://betternotificationsforwp.com/downloads/). Emails are sent out via your WordPress website (using `wp_mail`) but can be sent via SMTP using a  3rd party plugin should you wish.
If you want to let users create their own email notifications/subscriptions/alerts, check out my other plugin: [Content Notify](https://contentnotify.com).

Here's a quick walkthrough of the plugin in action:
[youtube https://www.youtube.com/watch?v=MxPUyRZPJ1Q]

= An Example: =
You want all the users in the Editor role to be notified via email when a new post is published and you'd like to customise it to include your logo along with the author's name and date / time it was published - with this plugin, that's easy.

> A handy list of shortcodes you can use is available [here](https://betternotificationsforwp.com/documentation/notifications/shortcodes/ "Shortcodes for use in Better Notifications for WP").

= Premium Add-ons =
Power-up your notifications using add-ons:

[Notification Add-on Bundles](https://betternotificationsforwp.com/add-on-bundles/) - Everything you need for your WordPress notifications. Save big when you buy an add-on bundle. Instant access.

[Subscriptions (GDPR)](https://betternotificationsforwp.com/downloads/subscriptions-gdpr/) - Allow users to manage their subscriptions for BNFW notifications.
If you want to let users create their own email notifications/subscriptions/alerts, check out my other plugin: [Content Notify](https://contentnotify.com).

[Conditional Notifications](https://betternotificationsforwp.com/downloads/conditional-notifications/) - Limit certain notifications depending on which categories, tags, post formats, or terms you choose.

[Custom Fields](https://betternotificationsforwp.com/downloads/custom-fields/) - Provides a number of new shortcodes allowing you to include data from custom fields and custom user fields created using [ACF](https://wordpress.org/plugins/advanced-custom-fields/).

[Send to Any Email](https://betternotificationsforwp.com/downloads/send-to-any-email/) - Send notifications to non-WordPress Users.

[Digest](https://betternotificationsforwp.com/downloads/digest/) - Group multiple notifications into a single digest notification.

[Global Override](https://betternotificationsforwp.com/downloads/per-post-override/) - Override some of the settings of notifications directly when editing a post, page, or custom post type.

[Reminders](https://betternotificationsforwp.com/downloads/update-reminder/) - Send a reminder to your users and/or user roles when a post, page, or custom post type hasn't been updated after a set amount of time. Also send a notification when a user hasn't logged in for a set amount of time.

[Multisite](https://betternotificationsforwp.com/downloads/multisite/) - Adds new notifications and shortcodes for WordPress Multisite to Better Notifications for WP.

[Profile Builder](https://betternotificationsforwp.com/downloads/profile-builder/) - Adds compatibility and new notifications and shortcodes for Profile Builder Free, Hobbyist, and Pro plugins to Better Notifications for WP.

...and more coming soon!

= Notifications =
The notifications that are currently available to use are:

**Admin**

* New User Registration - For Admin
* User Lost Password - For Admin
* Password Changed - For Admin
* User Email Changed - For Admin
* User Role Changed - For Admin
* User Logged In - For Admin
* WordPress Core Automatic Background Updates
* Privacy – Confirm Action: Export Data Request – For Admin
* Privacy – Confirm Action: Erase Data Request – For Admin

**Transactional**

* New User Registration - For User
* New User - Post-registration Email
* User Lost Password - For User
* Password Changed - For User
* User Email Changed Confirmation - For User
* User Email Changed - For User
* User Role Changed - For User
* User Logged In - For User
* Comment Reply
* Privacy – Confirm Action: Export Data Request – For User
* Privacy – Confirm Action: Erase Data Request – For User
* Privacy – Data Export – For User
* Privacy – Data Erased – For User
* Profile Builder – Approval Request for Admin ([Profile Builder Add-on](https://betternotificationsforwp.com/downloads/profile-builder/))
* Profile Builder – Email Confirmation ([Profile Builder Add-on](https://betternotificationsforwp.com/downloads/profile-builder/))
* Profile Builder – Account Approved ([Profile Builder Add-on](https://betternotificationsforwp.com/downloads/profile-builder/))
* Profile Builder – Account Unapproved ([Profile Builder Add-on](https://betternotificationsforwp.com/downloads/profile-builder/))
* User Login Reminder ([Reminders Add-on](https://betternotificationsforwp.com/downloads/custom-fields/))

**Posts / Custom Post Types**

* New Post Published
* Post Updated
* Post Pending Review
* New Private Post
* Post Scheduled
* Published Post Moved to Trash
* New Comment
* New Comment Awaiting Moderation
* Post - Comment Approved
* Post - Custom Field Updated ([Custom Fields Add-on](https://betternotificationsforwp.com/downloads/custom-fields/))
* Post - Update Reminder ([Reminders Add-on](https://betternotificationsforwp.com/downloads/update-reminder/))
* New Trackback
* New Pingback

**Pages**

* New Page Published
* Page Updated
* Page Pending Review
* New Private Page
* Page Scheduled
* Page - New Comment
* Page - New Comment Awaiting Moderation
* Page - Comment Approved
* Page - Comment Reply
* Page - Custom Field Updated ([Custom Fields Add-on](https://betternotificationsforwp.com/downloads/custom-fields/))
* Page - Update Reminder ([Reminders Add-on](https://betternotificationsforwp.com/downloads/update-reminder/))

**Media**

* New Media Published
* Media Updated
* Media - New Comment
* Media - New Comment Awaiting Moderation
* Media - Comment Approved
* Media - Comment Reply

**Posts**

* New Category
* New Tag

**Custom Post Types**

* New Term

**Multisite ([Multisite Add-on](https://betternotificationsforwp.com/downloads/multisite/))**

* New Site Activated or Created - For Network Admin
* New User Created - For Network Admin
* Network Admin Email Change Attempted - For New Network Admin
* Network Admin Email Changed - For Old Network Admin
* New Site Activated or Created - For Site Admin
* Site Deleted - For Site Admin
* Site Admin Email Change Attempted - For New Site Admin
* Site Admin Email Changed - For Old Site Admin
* New Site Activated - For User
* New User Created - For User
* New User Invited to Site - For User

If you'd like to see a notification in the list above, please drop me a line in the forum and I'll see what I can do to add it.

If you want to let users create their own email notifications/subscriptions/alerts, check out my other plugin: [Content Notify](https://contentnotify.com).

> Like this plugin? Please leave it [a great review](https://wordpress.org/support/plugin/bnfw/reviews/?rate=5#new-post)! Don't think the plugin is worthy of 5 stars? Having problems? Let me know in the [forum](https://wordpress.org/support/plugin/bnfw/) and I'll do my best to help.

> DISCLAIMER
> This is an incredibly useful and highly rated plugin however, it's possible that overuse or abuse of this plugin could upset your users by sending them too many notifications. By downloading / installing / using this plugin, you take full responsibility of the management / quantity / types of notifications that are sent out from it including abiding by any SPAM laws in your country / operating areas, dealing with / responding to subscription / unsubscription requests, complaints, and so on. I accept no liability or responsibility for SPAM or abuse of this plugin from the user(s) of or anyone that may receive notifications as a result of the use of this plugin. I'm trusting you, please don't abuse your users.



== Installation ==

1. Upload the 'bnfw' plugin folder to the '/wp-content/plugins/' directory or install it via your WordPress Admin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the settings in the new 'Notifications' Menu item in the sidebar to configure the plugin.
4. (Optional) Read our article on [how to improve email delivery in WordPress](https://betternotificationsforwp.com/guides/how-to-improve-email-delivery-wordpress/).
5. Test by creating a notification with some [shortcodes](https://betternotificationsforwp.com/documentation/notifications/shortcodes/ "Shortcodes for use in Better Notifications for WP").



== Frequently Asked Questions ==

> Full Documentation for BNFW can be found [here](https://betternotificationsforwp.com/documentation/ "Documentation for Better Notifications for WP").

= Is this plugin compatible with the new Block Editor (Gutenberg) in WordPress 5? =

Yes and no. Most notifications work but full support can't be guaranteed for all notifications at the moment. Full support is on the roadmap but for the time being, I'd recommend using the [Classic Editor plugin](https://wordpress.org/plugins/classic-editor/) with BNFW.

= What are shortcodes? Where can I use them in this plugin? =

Shortcodes are text in square brackets that output content of some kind. For example, if you want to add the content of a post to a notification for 'New Post Published', you can use the `[post_content]` shortcode to display this in the email that is sent out.

Shortcodes can be used in the 'Subject' and 'Message Body' of your notifications, except for a select few (due to a restriction in WordPress).

> A handy list of shortcodes you can use is available [here](https://betternotificationsforwp.com/documentation/notifications/shortcodes/ "Shortcodes for use in Better Notifications for WP").

= What are some scenarios this plugin could be used for? =

* Outreach: A blog/news site with hundreds of subscribers and want to use it to alert them of new blog posts.
* Communication: A small, internal WordPress site and use it to alert staff of new posts or comments.
* Monitor: A website for an awesome new product or service and use it for notifications of pingbacks and trackbacks.
* Security: To receive alerts of password reset requests and their corresponding user.

The possibilities are endless!

= Does this plugin work with anti-spam plugins, such as Akismet? =

Yes! There is an option for suppressing comments marked as SPAM in the plugin settings.

= Does this plugin and the add-ons work with Multisite? =

Yes! Full support for WordPress Multisite was added in 1.6.13, alongside the release of the [Multisite Add-on](https://betternotificationsforwp.com/downloads/multisite/).

= Can users create their own email notifications? =

Not using BNFW. If you want to let users create their own email notifications/subscriptions/alerts, check out my other plugin: [Content Notify](https://contentnotify.com).

= Notifications aren't coming through! =

First of all, follow [this article](https://betternotificationsforwp.com/how-can-i-receive-the-best-support/) about how to find out what might be causing the problem. Additionally, please take a look at [this help document](https://betternotificationsforwp.com/documentation/getting-started/how-to-improve-email-delivery/) to see how you might improve email delivery when using Better Notifications for WP.

Many hosts place a limit on the number of emails that can be sent out within an hour so this may also cause some delay in emails arriving. [This article](https://support.mailpoet.com/knowledgebase/lists-of-hosts-and-their-sending-limits/) has a fairly extensive list of hosts and their corresponding email rate limits that's worth checking out. Alternatively, please check with your host directly to find out what your limit is.

If you're still having problems, please drop me a line in the [Free Support Forums](https://wordpress.org/support/plugin/bnfw) and I'll do my best to help.

= Some of my shortcodes aren't working! =

It's possible you're inserting a shortcode into a notification that cannot use it. For example: the 'New Category' email notification cannot use any of the author or time shortcodes as WordPress only stores the category name, category slug and category description in the database by default. It's also worth checking the spelling and underscores in any shortcodes as well as if they are wrapped in square brackets `[]`.

= Other emails from WordPress / other plugins are being messed up! =

WordPress, by default, sends all emails in Plain Text. If you'd like to include code or use the WYSIWYG editor as part of Better Notifications for WP in your emails, you can change this to HTML using the global setting in Better Notification for WordPress. This can be found under the 'Notifications > Settings' screen. Changing this global email format setting will affect how all emails are sent out from WordPress however, so you may experience formatting issues with emails sent out from other plugins if you change the email format setting in this way. If you do, change this setting to Plain Text. You can also set the email format on a per-notification basis when setting-up a new Notification. The caveat is that WordPress will only either allow setting the email format globally (for all emails) or individually for anything that's non-transactional.

= Custom Post Type 'X' isn't showing in the list of available custom post types =

This is most likely because it's `public` setting is set to `false`. Try changing this and see if it shows up in the list. If the custom post type has been created by a plugin and is set to private (such as [TablePress](https://wordpress.org/plugins/tablepress/ "TablePress")), you'll need to get in touch with the plugin author to see if they'll consider changing it to public instead so that BNFW can send out notifications for it.

= Can I translate this plugin? =

Yes, of course! The plugin is completely translation-friendly and if you send me your .po file, I'll make sure to include it in the plugin and credit you in the changelog.

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the Better Notification for WP plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/bnfw). The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.



== Screenshots ==

1. All Notifications

2. Add New / Edit Notification



== Changelog ==

= 1.9.9 - 26th August 2025 =
* Fixed: The `[email_change_confirmation_link]` shortcode link now respects being overridden correctly.
* Fixed: Escaping in various shortcodes.
* Fixed: Issue where non-English dates added via shortcodes still showed in English.
* Fixed: Issue where a category or taxonomy term associated with a notification had been deleted.
* Added: Support for the upcoming Multi-language Add-on.

= 1.9.8 - 5th November 2024 =
* Added: Support for the new 'Send Notification Only Once' feature in the [Digest add-on](https://betternotificationsforwp.com/downloads/digest/).
* Fixed: Various PHP warnings.

= 1.9.7 - 16th September 2024 =
* Resolved: Due to user feedback, I've downgraded the PHP requirements for the plugin to PHP 7.4.

= 1.9.6 - 1st July 2024 =
* Added: You can now use `[user_ip_address]` and `[email_user_ip_address]` in notifications that can use the User shortcodes.
* Fixed: An issue with the [Global Override add-on](https://betternotificationsforwp.com/downloads/per-post-override/) where notifications couldn't be overridden when using the Classic Editor.

= 1.9.5 - 13th June 2024 =
* Fixed: An issue with the [Global Override add-on](https://betternotificationsforwp.com/downloads/per-post-override/) where notifications couldn't be overridden when using the Block Editor.

= 1.9.4 – 17th April 2024 =
* This is a large release containing lots of bug fixes (both big and small), compatibility fixes, and updates with the most recent versions of WordPress. This includes:
* PHP 8.0, 8.1, and 8.2 compatibility.
* Various performance improvements.
* Added: Some post notifications, such as Post Updated, can now be triggered via a Quick Action.
* Fixed: Subject of Email Changed notification when triggered using WooCommerce.
* Fixed: Some notifications weren't using the correct subjects.
* Fixed: BNFW was stripping some additional HTML from the notification than what WordPress allows.
* Various fixes for the [Custom Fields add-on](https://betternotificationsforwp.com/downloads/custom-fields/).
* Various fixes for the [Conditional Notifications add-on](https://betternotificationsforwp.com/downloads/conditional-notifications/).
* Various fixes for the [Subscriptions add-on](https://betternotificationsforwp.com/downloads/subscriptions-gdpr/).
* Various fixes for the [Global Override add-on](https://betternotificationsforwp.com/downloads/per-post-override/).
* Various fixes for the [Digest add-on](https://betternotificationsforwp.com/downloads/digest/).
* Various fixes for the [Multisite add-on](https://betternotificationsforwp.com/downloads/multisite/).

= 1.9.3 – 16th May 2023 =
* IMPORTANT! It is recommended that you update this plugin to the latest version.
* Fixed: A minor security issue relating to enabling/disabling notifications.

= 1.9.2 – 30th January 2023 =
* Fixed: An issue where the plugin was using the site language but not the user's language for translations

= 1.9.1 - 20th September 2022 =
* Fixed: A fatal error was shown when using PHP 7 and sending admin notifications.
* Fixed: Notifications weren't being sent out when using a shortcode in the Send To field when using the [Send to Any Email add-on](https://betternotificationsforwp.com/downloads/send-to-any-email/).

= 1.9 - 6th September 2022 =
* This is large bug fix and minor feature release.
* New: Notifications - You can now send notification when posts/pages/custom post types/media items are sent to the Trash.
* New: Shortcodes - You can now use `[password_url_raw]` and `[login_url_raw]` in the 'New User Registration - For User' notification to output these URLs as plain text (without the link wrapped around them).
* New: Shortcode - `[user_ip_address]` can be used in any notification that supports the `[user_]` shortcode group and will show the IP address of the user who triggered the notification.
* Added: The `email_user` and `user_` shortcodes are now available to use in the 'User Email Changed Confirmation - For User' notification.
* Added: Support for the 'Send To' field in the 'User Login Reminder' notification (part of the [Reminders add-on](https://betternotificationsforwp.com/downloads/update-reminder/) and [Conditional Notifications](https://betternotificationsforwp.com/downloads/conditional-notifications/)).
* Improved: Full PHP 8.0 compatibility.
* Improved: The 'New Comment' notifications now work with the `wp_new_comment`, `wp_insert_comment`, and `rest_insert_comment` hooks.
* Fixed: All BNFW screens are now translated using the language the user has set as opposed to the site language.
* Fixed: The 'New Private Post' notification wasn't being triggered.
* Fixed: 'Email Changed' notifications were sending in plain text instead of HTML, where set.
* Fixed: PHP Fatal Error when sending notifications to new users who are assigned multiple user roles (props to @intuitart for the fix).
* Fixed: PHP Fatal Error when disabling multiple notifications at the same time.
* Fixed: PHP Notice showing on the User Profile screen when the [Subscriptions add-on](https://betternotificationsforwp.com/downloads/subscriptions-gdpr/) was enabled.
* Fixed: Notifications weren't always showing their 'Notification Type' on the 'All Notifications' screen.

The rest of the changelog can be found [here](https://betternotificationsforwp.com/changelog/).

== Upgrade Notice ==

= Migrating from 0.2.1 to 1.0 =

Version 1 features an auto-importer which should migrate all of your notifications from any previous versions of the plugin to 1.0.
Please ensure that you check, double check and test your notifications before and after upgrading.
