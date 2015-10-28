=== Better Notifications for WordPress ===
Contributors: voltronik
Tags: notifications, email, mail, alerts, roles, user, users, admin, HTML, plain, wp_mail, shortcode, customize, post, page, updated, pending review, scheduled, category, tag, term, custom post type, comment, akismet, trackback, pingback, lost password, welcome, new user, bulk, notice, trigger, CC, BCC, from, author
Requires at least: 3.5
Tested up to: 4.3.1
Stable tag: 1.3.5
License: GPLv2 or later

Send customisable emails to your users for different WordPress notifications.

== Description ==

> Recently updated to be even easier to use!

Better Notifications for WordPress allows you to generate custom email notifications and send them to user roles (including custom roles) or individual users for all kinds of things happening on your WordPress website. Emails are sent out via your WordPress website (using `wp_mail`) but can be sent via SMTP using an appropriate 3rd party plugin should you wish.

https://www.youtube.com/watch?v=MxPUyRZPJ1Q

= For example: =
You want a user with the editor role (or all users using the Editor role) to be alerted via email when a new post is published and you'd like to customise it to include your branding along with the author's display name and post time - with this plugin, that's easy. 

> A long and handy list of shortcodes you can use is available [here](https://betternotificationsforwp.com/shortcodes/ "Shortcodes for use in Better Notifications for WordPress").

Notifications that are currently available to use are: 

**WordPress Defaults**

* New Comment / Comment Awaiting Moderation
* New Trackback
* New Pingback
* Lost Password (For Admin)
* New User Registration (For Admin)

**Transactional**

* Lost Password (For User)
* New User Registration (For User)
* New User - Welcome Email
* Comment Reply

**Posts / Custom Post Types**

* New Post Published
* Post Updated
* Post Pending Review
* Post Scheduled

**Pages**

* New Page Published
* Page Updated
* Page Pending Review
* Page Scheduled
* Page - New Comment

**Posts**

* New Category
* New Tag

**Custom Post Types**

* New Term

If you'd like to see a notification in the list above, please drop me a line in the forums and I'll see what I can do to add it.

Having problems? Please let me know via this plugin's forum so I can address them and let it act as a source of information for future reference and other users.

Like this plugin? Please leave it a great review! 
Don't think the plugin is worthy of 5 stars? Let me know on the forum and I'll do my best to help. 

> Want to add categories and sub-categories via the WordPress front-end? 
> Try my [Front-end Categories](https://wordpress.org/plugins/front-end-categories/ "Front-end Categories WordPress Plugin") plugin.



== Installation ==

1. Upload the 'bnfw' plugin folder to the '/wp-content/plugins/' directory or install it via your WordPress Admin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the settings in the new 'Notifications' Menu item in the sidebar to configure the plugin.
4. (Optional) Install a plugin to use SMTP instead of `wp_mail()`.
5. Test by creating a notification with some [shortcodes](https://betternotificationsforwp.com/shortcodes/ "Shortcodes for use in Better Notifications for WordPress").



== Frequently Asked Questions ==

= What are shortcodes? Where can I use them in this plugin? = 

Shortcodes are little blocks of content inserted by WordPress, contained in square brackets. For example, if you want to add the content of a post to a notification for 'New Post Published', you can use the `[post_content]` shortcode to display this in the email that is sent out. 

Shortcodes can be used in the 'Subject' and 'Message Body' of your notifications, except for a select few (due to a restriction in WordPress).

> A long and handy list of shortcodes you can use is available [here](https://betternotificationsforwp.com/shortcodes/ "Shortcodes for use in Better Notifications for WordPress").

= What are some scenarios this plugin could be used for? =

* Outreach: A blog/news site with hundreds of subscribers and want to use it to alert them of new blog posts. 
* Communication: A small, internal WordPress site and use it to alert staff of new posts or comments.
* Monitor: A website for an awesome new product or service and use it for notifications of pingbacks and trackbacks.
* Security: To receive alerts of password reset requests and their corresponding user.

The possibilities are endless! 

= Does this plugin work with Akismet? =

Yes! There is an option for suppressing comments marked as SPAM by Akismet in the plugin settings.

= What isn't this plugin? =

It's not designed to send out newsletters. There is no send-this-out-on-this-date style functionality included. There are many other great plugins available that you could use for that instead. 

= How do I set-up WordPress to work with this plugin correctly? =

This will very much depend on what notifications you're using the plugin for. Out-of-the-box, this plugin works very well but there are a few tweaks that you will need to ensure 100% compatibility. All the below points refer to this plugin: 

* If you want to use the new comment notifications in BNFW, you need to un-tick 'Email me whenever anyone posts a comment' and 'A comment is held for moderation' under Settings > Discussion. It's ok if you don't do this but you might receive WordPress's own email notifications along with the ones you've configure using BNFW. It also goes without saying that you need to enable comments for your posts if you want the email notifications to come through. 

* If you want to use the 'Comment Reply' transactional email, you need to ensure that comments are only set-up to be 2-levels deep. You can do this by going to Settings > Discussion and changing the option 'Enable threaded (nested) comments 'X' levels deep' to '2'. Please also ensure this option is ticked. 

= Notifications aren't coming through! =

A [very handy answer](http://stackoverflow.com/questions/371/how-do-you-make-sure-email-you-send-programmatically-is-not-automatically-marked) on StackExchange explains what you might need to do to make sure that emails don't make it through to your users SPAM / Junk folders. It's worth going through this and completing as much as you can.

After this, check your Notification settings to make sure all is as it should be, then check your SPAM folder and/or filter at your host. Gmail and certain hosts can mark messages from new websites (or IP addresses) as SPAM so it's worth checking and possibly creating a filter to ensure this doesn't happen in the future.

Many hosts place a limit on the number of emails that can be sent out within an hour so this may also cause some delay in emails arriving. Please check with your host to find out what this limit is. 

= Some of my shortcodes aren't working! =

It's possible you're inserting a shortcode into a notification that cannot use it. For example: the 'New Category' email notification cannot use any of the author or time shortcodes as WordPress only stores the category name, category slug and category description in the database. It's also worth checking the spelling and underscores in any shortcodes as well as if they are wrapped in square brackets `[]`.

= I press the 'Send Me a Test Email' button but nothing happens! =

Check that you've saved your notification first, then try again. It may take a second for the email to come through. Please also check your email SPAM filter.

= Other emails from WordPress / other plugins are being messed up! =

WordPress, by default, sends all emails in Plain Text. If you'd like to include code or use the WYSIWYG editor as part of Better Notifications for WordPress in your emails, you can change this to HTML using the global setting in Better Notification for WordPress. This can be found under the 'Notifications > Settings' screen. Changing this global email format setting will affect how all emails are sent out from WordPress however, so you may experience formatting issues with emails sent out from other plugins if you change the email format setting in this way. If you do, change this setting to Plain Text. You can also set the email format on a per-notification basis when setting-up a new Notification. The caveat is that WordPress will only either allow setting the email format globally (for all emails) or individually for anything that's non-transactional.

= Custom Post Type 'X' isn't showing in the list of available custom post types =

This is most likely because it's `public` setting is set to `false`. Try changing this and see if it shows up in the list. If the custom post type has been created by a plugin and is set to private (such as [TablePress](https://wordpress.org/plugins/tablepress/ "TablePress")), you'll need to get in touch with the plugin author to see if they'll consider changing it to public instead so that BNFW can send out notifications for it. 

= I'm using the P2 theme and my notifications are coming through twice =

In order to fix a problem with P2 not triggering notifications at all, if you post from the WordPress Admin, it will trigger two notifications: one for the standard settings and one for the additional P2 settings. I recommend posting from the front-end only if you're using P2 in order to trigger just a single notification. 

= Can I translate this plugin? =

Yes, of course! The plugin is completely translation-friendly and if you send me your .po file, i'll make sure to include it in the plugin and credit you on this page.

= Will this plugin work with versions of WordPress less than 3.5? = 

It might do but this is untested. 



== Screenshots ==

1. All Notifications

2. Add New Notification

3. Plugin Settings



== Changelog ==

= 1.3.5 - 9th October 2015 =
* Fixed: A large bug that was causing issues with Password URL shortcodes in the 'New User Registration - For User' and 'Password Reset - For User' notifications.
* Fixed: An issue where the 'disabled wpautop' checkbox was appearing on transactional emails. 
* Fixed: Some labels in the 'All Notifications' screen weren't formatted correctly.
* Fixed: Translations weren't referenced correctly. Auto-translation will be [done by WordPress.org automatically](https://make.wordpress.org/plugins/2015/09/01/plugin-translations-on-wordpress-org/) at some point in the future.

= 1.3.4 - 2nd October 2015 =
* New: Choose automatic or manual paragraph / line breaks in the WYSIWYG editor. This fixes an issue where they were inserted automatically when they weren't desired. The checkbox for this is below the WYSIWYG editor. 
* New: You can now add images to your notifications using the 'Add Media' button above the WYSIWYG editor.
* New: Portuguese Brazil translation for v1.3.3 (props Glayton Caixeta).
* New: BNFW now has it's own [website](https://betternotificationsforwp.com/)!
* New: Buttons have been added linking to the Documentation and Shortcode Help sections of the website next to the WYSIWYG editor.
* New: Support for premium add-ons, coming very soon! More info and sign-up for updates [here](https://wordpress.org/support/topic/add-ons-are-coming-sign-up-for-updates).
* New: Added a video overview of BNFW showing it's features and functionality to the main plugin page and website.
* Fixed: The 'New User Registration - For User' now works again after WordPress 4.3 broke it.
* Fixed: HTML using quotes was being escaped in emails.
* Fixed: The 'Settings' sidebar menu item wasn't highlighting properly.
* I also added all previous release dates to this changelog.
* If you liked this plugin, please feel free to leave an honest [review](https://wordpress.org/support/view/plugin-reviews/bnfw?filter=5#postform). If you didn't or have a problem, please send me a message in the [Support Forum](https://wordpress.org/support/plugin/bnfw).

= 1.3.3 - 22nd August 2015 =
* New: You can now send a notification to the Post Author only, where a notification supports it.
* New: There is now an option in the Settings screen to globally set WordPress to send emails in either HTML or Plain Text. Please read the [FAQ](https://wordpress.org/plugins/bnfw/faq/) for more information about this as there is a small caveat.
* New: Support for WordPress 4.3.
* New: Pending posts that are changed to Published now trigger the 'New Post / Page Notification'.
* Improved: Scheduled notifications now trigger two notifications, one for when they're saved as Scheduled ('Post Scheduled' / 'Page Scheduled') and one for when they're actually published ('New Post Published / New Page Published').
* Improved: Swapped the green tick for a dashicons tick for a slightly more speedier, native-feeling plugin.
* Improved: Reduced the flash of hidden elements when loading the New / Edit Notification screen.
* Fixed: WordPress 4.3 doesn't allow passwords to be automatically created for new users and will instead, send them to a password generator page. The `[password]` shortcode has been replaced with `[password_url]`. `[password]` should still work though so it won't break your existing notifications.
* Fixed: Some output was being showed when WP_DEBUG was enabled.
* Fixed: Removed the 'slug' field when enabled from Screen Options.

= 1.3.2 - 20th July 2015 =
* Fixed: Replaced a deprecated function which might cause a warning to show when `WP_DEBUG` was enabled.

= 1.3.1 - 18th July 2015 =
* Fixed: The P2 theme wasn't triggering new post or comment notifications. 
* Fixed: Sometimes the shortcode help link at the bottom of the notification editor wouldn't link to the help page properly. 

= 1.3 - 2nd July 2015 =
* New: Option to disable notifications going to the user that triggered them.
* New: Comment Reply Notification. This is a transactional notification that will only trigger when replying to the original comment (i.e. Up to 2-levels deep). 
* New: New Shortcode: Update Author. Use `[post_update_author]` in any Post or Page notifications to see which user updated the post. 
* New: Choose between sending the notification as plain text or HTML.
* New: A basic implementation of shortcode help has been added into the plugin. Click the link below the message body editor to see which shortcodes can be used for the currently selected notification.
* Improved: The 'New User Registration' (For Admin & User) and 'Welcome Email' notifications now allow you to use all of the 'User' shortcodes.
* Fixed: Formatting in emails sent from other plugins were being effected by BNFW.
* Fixed: Notifications for Categories, Terms and Tags weren't getting triggered. 
* Fixed: Notifications weren't getting triggered when using the P2 theme (please see the [FAQ](https://wordpress.org/plugins/bnfw/faq/) for more information about this).
* Fixed: Additional Email fields were being shown for transactional notifications that couldn't use them.

= 1.2 - 19th May 2015 =
* New: WYSIWYG Editor for notifications!
* New: From Name, From Email, CC and BCC options are now available to use for each notification!
* New: 'Send Me a Test Email' button. Save your notification first and then send yourself a test email! This will only go to you and not to any of the other users selected in the notification. Shortcodes will not be expanded but will be shown in place. 
* Improved: The User Role / Users drop down box has been unified for simplicity.
* Fixed: Lost Password (For User) notifications were being sent in plain text.
* Fixed: Not all custom post types were showing when setting up a new notification.
* Fixed: Notifications for New Terms not generating notification emails.
* Fixed: Custom Taxonomies missing from columns in the All Notifications screen.
* Fixed: Added a space after lists of tags, categories, and terms.
* Thank you again for all of your support, feedback, and awesome reviews. You people make WordPress great!

= 1.1.5.3 - 21st April 2015 =
* Compatibility with WordPress 4.2.

= 1.1.5.2 - 15th April 2015 =
* Fixed: Custom Post Type Pending Posts not being sent.
* Fixed: Post excerpt not outputting anything. 
* Removed: `[closedpostboxes_page], [rich_editing], and [admin_color]` as was a bit defunct and causing issues. 
* Changed: `[post_author]` now outputs the display name instead of the author ID. 
* Improved: Clarity of custom post type and taxonomy labels. 

= 1.1.5.1 - 13th April 2015 =
* Fix for Custom Post Type notifications not populating shortcodes.
* Fix for Custom Taxonomy terms not sending out notification emails.

= 1.1.5 - 10th April 2015 =
* New Shortcode: [permalink].
* New Notifications: 'Scheduled Posts' and 'Scheduled Pages'.
* New Shortcodes for the above Notifications: [post_scheduled_date] and [post_scheduled_date_gmt].
* New Notification: 'New Page Published'.
* New Notification: 'Page 'Updated'.
* New Notification: 'Page Pending Review'.
* New Notification: 'New Page - Comment'.
* Fixed: A few bugs reported via the forums and some others that i've found - thanks everyone!

= 1.1 - 16th March 2015 =
* Lots of cool new stuff and more to come soon!
* Transactional notifications (notifications intended only for the end user) have been added as a new group in the notifications select box.
* New Notification: Post Pending Review (for all post types).
* New Notification: New User Registration (Transactional).
* New Notification: Password Reset (Transactional).
* New Notification: New User Welcome Email (Transactional).
* Added an option to enable or disable each notification.
* Better support for custom taxonomies and terms.
* Fixed: A few PHP warnings were showing when using `WP_DEBUG`.
* Fixed: HTML tags being stripped from `[post_content]` and `[comment_content]`.
* Removed: Some WordPress Multisite shortcodes that didn't work properly. Proper support for Multisite will come later.
* If you liked this plugin, please leave a review. If you didn't or have a problem, please send me a message in the Support Forum.

= 1.0.2 - 17th January 2015 =
* Bug fix for `[ID]` shortcode not outputting anything.
* Bug fix for `[post_category]` showing as empty.
* Added tags shortcode for use in certain notifications using `[post_tag]`.

= 1.0.1 - 21th December 2014 =
* Bug fix for notifications not being sent to custom user roles or individual users of custom roles.
* Added a 'Notification Type' column to the notifications screen.
* Renamed 'User Roles' column in notifications screen to 'User Roles/Users'.

= 1.0 - 17th December 2014 =
* First major release - we're no longer in beta!
* Total overhaul. The plugin has been completely re-written.
* Auto-importer: Your old notifications will be imported when updating the plugin.
* New 'Generator' for easily creating and editing your notifications.
* More options and notification types as per feedback via the forums - thanks everyone!
* Granular control over choosing either user roles or individual users you'd like to send a notification to.
* Fully translatable.
* Loads of bugfixes and improvements.

= 0.2.1 beta - 4th June 2013 =
* Minor Bugfixes

= 0.2 beta - 8th April 2013 =
* Added an option to suppress SPAM comment notifications.

= 0.1 beta - 25th March 2013 =
* Initial version of the plugin.
* Settings page for configuring notification types for roles.
* Email Templates page for customising emails using HTML and shortcodes.



== Upgrade Notice ==

= Migrating from 0.2.1 to 1.0 =

Version 1 features an auto-importer which should migrate all of your notifications from any previous versions of the plugin to 1.0. 
Please ensure that you check, double check and test your notifications before and after upgrading.