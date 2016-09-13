=== Better Notifications for WordPress ===
Contributors: voltronik
Donate link: https://betternotificationsforwp.com/donate/
Tags: notification, email, push, sms, alert, HTML, customize, bulk, trigger, CC, BCC
Requires at least: 3.5
Tested up to: 4.6.1
Stable tag: 1.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send customisable emails to your users for different WordPress notifications.

== Description ==

> New add-ons are now available! [View Add-ons](https://betternotificationsforwp.com/store/)

Better Notifications for WordPress allows you to create custom email notifications and send them to user roles (including custom roles) or individual users for all kinds of things happening on your WordPress website. Emails are sent out via your WordPress website (using `wp_mail`) but can be sent via SMTP using an appropriate 3rd party plugin should you wish.

https://www.youtube.com/watch?v=MxPUyRZPJ1Q

= For example: =
You want a user with the editor role (or all users using the Editor role) to be alerted via email when a new post is published and you'd like to customise it to include your branding along with the author's display name and post time - with this plugin, that's easy. 

If you want to override the notifications that you've set-up on an individual post / page / custom post basis, you may be interested in the [Per-post Override add-on](https://betternotificationsforwp.com/downloads/per-post-override/).

> A handy list of shortcodes you can use is available [here](https://betternotificationsforwp.com/shortcodes/ "Shortcodes for use in Better Notifications for WordPress").

Notifications that are currently available to use are: 

**WordPress Defaults**

* New Comment / Comment Awaiting Moderation
* New Trackback
* New Pingback
* Lost Password - For Admin
* New User Registration - For Admin

**Transactional**

* Lost Password - For User
* New User Registration - For User
* New User - Post-registration Email
* User Role Changed - For Admin
* User Role Changed - For User
* Comment Reply

**Posts / Custom Post Types**

* New Post Published
* Post Updated
* Post Pending Review
* Post Scheduled
* Post - Custom Field Updated ([Custom Fields Add-on](https://betternotificationsforwp.com/downloads/custom-fields/))
* Post - Update Reminder ([Update Reminder Add-on](https://betternotificationsforwp.com/downloads/update-reminder/))

**Pages**

* New Page Published
* Page Updated
* Page Pending Review
* Page Scheduled
* Page - New Comment
* Page - Comment Reply
* Page - Custom Field Updated ([Custom Fields Add-on](https://betternotificationsforwp.com/downloads/custom-fields/))
* Page - Update Reminder ([Update Reminder Add-on](https://betternotificationsforwp.com/downloads/update-reminder/))

**Posts**

* New Category
* New Tag

**Custom Post Types**

* New Term

If you'd like to see a notification in the list above, please drop me a line in the forum and I'll see what I can do to add it.

Like this plugin? Please leave it [a great review](https://wordpress.org/support/view/plugin-reviews/bnfw?rate=5#postform)! 
Don't think the plugin is worthy of 5 stars? Having problems? Let me know in the [forum](https://wordpress.org/support/plugin/bnfw) and I'll do my best to help.

> DISCLAIMER
> This is an incredibly useful and highly rated plugin however, it's possible that overuse or abuse of this plugin could upset your users by sending them too many notifications. By downloading / installing / using this plugin, you take full responsibility of the management / quantity / types of notifications that are sent out from it including abiding by any SPAM laws in your country / operating areas, dealing with / responding to subscription / unsubscription requests, complaints, and so on. I accept no liability or responsibility for SPAM or abuse of this plugin from the user(s) of or anyone that may receive notifications as a result of the use of this plugin. I'm trusting you, please don't abuse your users.



== Installation ==

1. Upload the 'bnfw' plugin folder to the '/wp-content/plugins/' directory or install it via your WordPress Admin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the settings in the new 'Notifications' Menu item in the sidebar to configure the plugin.
4. (Optional) Install a plugin to use SMTP instead of `wp_mail()`. I recommend [Postman SMTP Mailer/Email Log](https://wordpress.org/plugins/postman-smtp/).
5. Test by creating a notification with some [shortcodes](https://betternotificationsforwp.com/shortcodes/ "Shortcodes for use in Better Notifications for WordPress").



== Frequently Asked Questions ==

= What are shortcodes? Where can I use them in this plugin? = 

Shortcodes are little blocks of content contained in square brackets. For example, if you want to add the content of a post to a notification for 'New Post Published', you can use the `[post_content]` shortcode to display this in the email that is sent out. 

Shortcodes can be used in the 'Subject' and 'Message Body' of your notifications, except for a select few (due to a restriction in WordPress).

> A handy list of shortcodes you can use is available [here](https://betternotificationsforwp.com/shortcodes/ "Shortcodes for use in Better Notifications for WordPress").

= What are some scenarios this plugin could be used for? =

* Outreach: A blog/news site with hundreds of subscribers and want to use it to alert them of new blog posts. 
* Communication: A small, internal WordPress site and use it to alert staff of new posts or comments.
* Monitor: A website for an awesome new product or service and use it for notifications of pingbacks and trackbacks.
* Security: To receive alerts of password reset requests and their corresponding user.

The possibilities are endless! 

= Does this plugin work with Akismet? =

Yes! There is an option for suppressing comments marked as SPAM by Akismet in the plugin settings.

= Does this plugin work with Multisite? =

Yes and no - some notifications work, others don't, and some are missing entirely. I do plan on adding full Multisite support at a later date however, this will be in the form of an add-on. 

= What isn't this plugin? =

It's not designed to send out newsletters. There is no send-this-out-on-this-date style functionality included. There are many other great plugins available that you could use for that instead. Additionally, users cannot currently unsubscribe automatically from notifications so you'll have to work out how you manage unsubscribers manually.

= How do I set-up WordPress to work with this plugin correctly? =

This will very much depend on what notifications you're using the plugin for. Out-of-the-box, this plugin works very well but there are a few tweaks that will ensure 100% compatibility. All the below points refer to this plugin: 

* If you want to use the new comment notifications in BNFW, you need to un-tick 'Email me whenever anyone posts a comment' and 'A comment is held for moderation' under Settings > Discussion. It's OK if you don't do this but you might receive WordPress's own email notifications along with the ones you've configure using BNFW. It also goes without saying that you need to enable comments for your posts if you want the email notifications to come through. 

* If you want to use the 'Comment Reply' transactional email, you need to ensure that comments are only set-up to be 2-levels deep. You can do this by going to Settings > Discussion and changing the option 'Enable threaded (nested) comments 'X' levels deep' to '2'. Please also ensure this option is ticked. 

= Notifications aren't coming through! =

First of all, follow [this article](https://betternotificationsforwp.com/how-can-i-receive-the-best-support/) about how to find out what might be causing the problem. Additionally, this [very handy answer](http://stackoverflow.com/questions/371/how-do-you-make-sure-email-you-send-programmatically-is-not-automatically-marked) on Stack Overflow explains what you might need to do to make sure that emails don't make it through to your user's SPAM / Junk folders. It's worth going through this and completing as much as you can.

Many hosts place a limit on the number of emails that can be sent out within an hour so this may also cause some delay in emails arriving. MailPoet has a fairly extensive list of hosts and their corresponding email rate limits that's worth checking out [here](https://support.mailpoet.com/knowledgebase/lists-of-hosts-and-their-sending-limits/). Alternatively, please check with your host directly to find out what your limit is. 

If you're still having problems, please drop me a line in the [Free Support Forums](https://wordpress.org/support/plugin/bnfw) and I'll do my best to help. 

= Some of my shortcodes aren't working! =

It's possible you're inserting a shortcode into a notification that cannot use it. For example: the 'New Category' email notification cannot use any of the author or time shortcodes as WordPress only stores the category name, category slug and category description in the database by default. It's also worth checking the spelling and underscores in any shortcodes as well as if they are wrapped in square brackets `[]`.

= I press the 'Send Me a Test Email' button but nothing happens! =

Check that you've saved your notification first, then try again. It may take a second for the email to come through. Please also check your email SPAM filter.

= Other emails from WordPress / other plugins are being messed up! =

WordPress, by default, sends all emails in Plain Text. If you'd like to include code or use the WYSIWYG editor as part of Better Notifications for WordPress in your emails, you can change this to HTML using the global setting in Better Notification for WordPress. This can be found under the 'Notifications > Settings' screen. Changing this global email format setting will affect how all emails are sent out from WordPress however, so you may experience formatting issues with emails sent out from other plugins if you change the email format setting in this way. If you do, change this setting to Plain Text. You can also set the email format on a per-notification basis when setting-up a new Notification. The caveat is that WordPress will only either allow setting the email format globally (for all emails) or individually for anything that's non-transactional.

= Custom Post Type 'X' isn't showing in the list of available custom post types =

This is most likely because it's `public` setting is set to `false`. Try changing this and see if it shows up in the list. If the custom post type has been created by a plugin and is set to private (such as [TablePress](https://wordpress.org/plugins/tablepress/ "TablePress")), you'll need to get in touch with the plugin author to see if they'll consider changing it to public instead so that BNFW can send out notifications for it. 

= Can I translate this plugin? =

Yes, of course! The plugin is completely translation-friendly and if you send me your .po file, I'll make sure to include it in the plugin and credit you in the changelog.

= Will this plugin work with versions of WordPress less than 3.5? = 

An older version might work but this is untested. A lot of the newer features require WordPress 4.0 and above.



== Screenshots ==

1. All Notifications

2. Add New / Edit Notification

3. Plugin Settings



== Changelog ==

= 1.5.3 - 13th September 2016 =
* Fixed: User Roles in the 'User Roles / Users' admin column were being displayed only in lowercase.
* Fixed: The `[wp_capabilities]` shortcode wasn't outputting properly. It now displays the higher-level capabilities that the user has.

= 1.5.2 - 6th September 2016 =
* Fixed: Custom User Roles were showing 0 users in the 'To' field.
* Added: Generic CSS classes to BNFW admin.
* Updated: German Translation to show English in certain places where translation text breaks the WP Admin UI (props @helmi).
* Full code review and submission to WordPress VIP!

= 1.5.1 - 5th August July 2016 =
* Fixed: 'Text' mode in the WYSIWYG editor didn't show any buttons when the BNFW Per-post Override Add-on was activated.

= 1.5 - 25th July 2016 =
* New: Global Site Shortcodes! Include these in any notification to output the site title (`[global_site_title]`), site tagline (`[global_site_tagline]`), or site URL (`[global_site_url]`).
* New: Global User Shortcodes! Include these in any notification to output the user's first name (`[global_user_firstname]`), user's last name (`[global_user_lastname]`), or user's email address (`[global_user_email]`).
* New: The 'User Role Changed' notification has been split into two transactional notifications - one that can be sent to users and one that can be sent to admins.
* New: Shortcode `[featured_image]`. Outputs the URL for the featured image (if one is available). 
* New: Shortcode `[user_avatar]`. Outputs the User's avatar when used in a capable notification.
* New: Shortcode `[commenter_avatar]`. Outputs the comment author's avatar for comment-based notifications.
* Improved: 'Comment Reply' notifications are now available to use for Pages and Custom Post Types.
* Improved: When sending notifications to user roles in the 'To' field, it will now show how many users are in that role. 
* Improved: The 'Lost Password - For User', 'User Role Changed - For Admin', and 'User Role Changed - For User' notifications now have the option to 'Stop additional paragraph and line break HTML from being inserted into my notifications' via the checkbox below the WYSIWYG editor on the Add New / Edit Notification screen.

= 1.4.1 - 3rd June 2016 =
* Fixed: Multiple emails were being sent for a single notification for a small number of users. After lots of hunting and lots of testing, I'm hoping this should now be fixed.
* Fixed: The 'User Role Changed' notification was broken after the update to WordPress 4.5.
* Fixed: The 'Password Reset - For User' and 'New User - Post-registration Email' notifications were being sent in HTML but with all carriage returns / line breaks stripped out.
* Fixed: User shortcodes for new comments on custom post types weren't being outputted properly.
* Fixed: The 'Notifications' BNFW menu item in the Sidebar in the WordPress Admin was showing for non-admins.
* Added: German Translation (props Michael Schröttle).

= 1.4 - 8th April 2016 =
* New: Shortcode `[post_slug]`. Output the post slug.
* New: Shortcode `[edit_post]`. Outputs the link to edit the post / page / custom post.
* New: Shortcode `[post_parent_permalink]`. Outputs a permalink to the post / page / custom post's parent item.
* New: Shortcode `[author_link]`. Outputs a link to the post / page / custom post's author archive.
* New: You can now add the collection of User Shortcodes to the 'Lost Password - For User' email. 
* New: Support for the 'O2' plugin, when used in conjunction with the 'P2' Theme via a filter. Please see the [documentation](https://betternotificationsforwp.com/documentation/) for details.
* Improved: Users were getting confused with the 'Welcome Email', thinking it operated like that in the 'SB Welcome Email Editor' plugin. The name of this notification has been changed from 'New User - Welcome Email' to 'New User - Post-registration Email' to help differentiate its functionality in BNFW.
* Improved: The screen where you add your license(s) after purchasing any BNFW add-on(s) is now called 'Add-on Licenses', instead of going to 'Settings' and adding them there. 
* Improved: Setting a notification to send to 'the author only' now shows a label in the 'User Roles / Users' column in the 'All Notifications' screen.
* Improved: 'New Comment / Awaiting Moderation', 'New Trackback', and 'New Pingback' notifications now show the 'Send this notification to the Author only' checkbox.
* Improved: If a notification is present but disabled, the default WordPress notification (if there is one), will not be sent. 
* Fixed: New Post Published notifications weren't being sent if you had the Per-post Override add-on installed. 

= 1.3.9.5 - 26th February 2016 =
* Fixed: New Post Published notifications were triggering multiple times due to `auto_draft_to_publish`.

= 1.3.9.4 - 15th February 2016 =
* Filter improvements for the new [Per-post Override add-on](https://betternotificationsforwp.com/downloads/per-post-override/).

= 1.3.9.3 - 12th February 2016 =
* General bug fixes and updates relating to future add-ons and the new [Per-post Override add-on](https://betternotificationsforwp.com/downloads/per-post-override/) which allows you to override your notifications for each post / page / custom post.
* Added: New Post Published notifications now trigger on `auto_draft_to_publish`. This may or may not effect you if you publish through a front-end form or from an app.

= 1.3.9.2 - 29th January 2016 =
* The [Add-on Store](https://betternotificationsforwp.com/store/) is now live! Looking for some extra, premium functionality in your notifications? You might find an add-on for it!
* New: A filter is now available for adding compatibility to themes for creating posts using `wp_insert_post`. Please see the bottom of the [FAQ](https://wordpress.org/plugins/bnfw/faq/) for details.
* Fixed: 'User Role Changed' notifications were being triggered for new users. 
* Fixed: The label for custom taxonomies was showing in the Notification select box even if you didn't have any.
* Fixed: A warning was showing when no 'CC' or 'BCC' details were added but the 'Name' and 'From' details were specified.
* Fixed: A warning was showing after a taxonomy was deleted but a notification existed for it.
* Fixed: Replaced select2 v4 script for full version for better compatibility with other plugins.
* Fixed: The javascript in the plugin is now translatable.
* Added: French translation - props Mygale06.

= 1.3.9.1 - 9th January 2016 =
* Fixed: Missing shortcode link for new 'User Role Changed' notification.
* Fixed: Duplicate 'User Role Changed' listing in the Notification select box.

= 1.3.9 - 9th January 2016 =
* Happy New Year!
* New: Transactional notification for when a User Role Changed. This is sent to the user when their User Role is changed.
* Fixed: Ensure that at least one User or User Role is selected before saving a notification.
* Fixed: select2 v4 update and compatibility with Ultimate Member and ACF Pro.

= 1.3.8 - 16th December 2015 =
* Fixed: Notifications weren't being sent out at all. After lots of testing, I think this should now be resolved.
* Fixed: An issue where quotes in the Subject field were causing part of/all the field content to be removed.
* Improved: If you have more than 100 users on your site, the User Roles / Users select box will show the first 100 users only. You can search through the rest by typing the first character of a username in the select box and it will show you any matching users.

= 1.3.7 - 11th December 2015 =
* Fixed: An issue where EDD_SL_Plugin_Updater class was already declared by another plugin.

= 1.3.6 - 11th December 2015 =
* Merry Christmas to you all!
* New: Password Reset notifications can now be in HTML or Plain Text.
* Improved: The Users / User Role select box now utilises live search instead of showing all users in a long list if you have more than 100 users. This should be a huge performance improvement if your site has a lot of users.
* Updated: select2.js library updated to v4.
* Fixed: Compatibility with password reset notifications in WordPress 4.1.0 and below.
* Fixed: An issue where the 'Tag' label was showing incorrectly in the 'All Notifications' screen.
* I've added a short disclaimer to the [Description Tab](https://wordpress.org/plugins/bnfw/) regarding SPAM and overuse/abuse of this plugin which I recommend a quick read through.

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