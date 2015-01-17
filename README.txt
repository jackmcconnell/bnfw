=== Better Notifications for WordPress ===
Contributors: voltronik
Tags: notifications, email, alerts, roles, users, HTML
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 1.0.2
License: GPLv3

Send customisable HTML emails to your users for different WordPress notifications.

== Description ==

> Recently updated to be even easier to use!

Better Notifications for WordPress allows you to generate custom HTML email notifications and send them to user roles (including custom roles) or individual users for all kinds of things happening on your WordPress website. Emails are sent out via your WordPress website (using `wp_mail`) but can be sent via SMTP using an appropriate 3rd party plugin should you wish.

= For example: =
You want a user with the editor role (or all users using the Editor role) to be alerted via email when a new post is published and you'd like to customise it to include your branding along with the author's display name and post time - with this plugin, that's easy. 

Notifications that are currently available to use are: 

* New category
* New Post
* Post updated
* New comment (and it's corresponding status)
* New trackback
* New pingback
* New user registration (admin)
* Lost password reset (admin)
* New custom taxonomy

If you'd like to see a notification in the list above, please drop me a line in the forums and we'll see what we can do to add it.

A long and handy list of shortcodes you can use is available [here](http://www.voltronik.co.uk/wordpress-plugins/better-notifications-for-wordpress/ "Shortcodes for use in Better Notifications for WordPress").


Having problems? Please let me know via this plugin's forum so we can address them and let it act as a source of information for future reference and other users.

Like this plugin? Please leave it a great review! 
Don't think the plugin is worthy of 5 stars? Let me know on the forum and we'll do our best to help. 

> Want to add categories and sub-categories via the WordPress front-end? 
> Try my [Front-end Categories](https://wordpress.org/plugins/front-end-categories/ "Front-end Categories WordPress Plugin") plugin.



== Installation ==

1. Upload the 'bnfw' plugin folder to the '/wp-content/plugins/' directory or download it via your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the settings in the new 'Notifications' Menu item in the sidebar to configure the plugin.
4. (Optional) Install a plugin to use SMTP instead of wp_mail(). We recommend WP-Mail-SMTP.
5. Test by creating a notification.



== Frequently Asked Questions ==

= What are shortcodes? Where can I use them in this plugin? = 

Shortcodes are little blocks of content inserted by WordPress, contained in square brackets. For example, if you want to add the content of a post to a notification for 'New Post Published', you can use the `[post_content]` shortcode to display this in the email that is sent out. 

Shortcodes can be used in the 'Subject' and 'Message Body' of your notifications.

A long and handy list of shortcodes you can use is available [here](http://www.voltronik.co.uk/wordpress-plugins/better-notifications-for-wordpress/ "Shortcodes for use in Better Notifications for WordPress").

= What are some scenarios this plugin could be used for? =

* Outreach: A blog/news site with hundreds of subscribers and want to use it to alert them of new blog posts. 
* Communication: A small, internal WordPress site and use it to alert staff of new comments.
* Monitor: A website for an awesome new product or service and use it for notifications of pingbacks and trackbacks.
* Security: To receive alerts of password reset requests and their corresponding email address.

The possibilities are endless! 

= Does this plugin work with Akismet? =

Yes! There is an option for suppressing comments marked as SPAM by Akismet in the plugin settings.

= What isn't this plugin? =

It's not designed to send out newsletters. There is no send-this-out-on-this-date style functionality included. There are many other great plugins available that you could use for that instead. 

= How do I set-up WordPress to work with this plugin correctly? =

This will very much depend on what notifications you're using the plugin for. Out-of-the-box, this plugin works very well but there are a few tweaks that you will need to ensure 100% compatibility. All the below points refer to this plugin: 

* If you want to use the new comment notifications in BNFW, you need to switch off 'Email me whenever anyone posts a comment' and 'A comment is held for moderation' under Settings > Discussion. It's ok if you don't do this but you might receive WordPress's own email notifications along with the ones you configure using BNFW too. It also goes without saying that you need to enable comments for your posts if you want the email notifications to come through. 

= Configured emails aren't coming through! =

Check your settings to make sure all is as it should be, then check your spam folder and/or filter at your host. Gmail and certain hosts can mark messages from new websites (or IP addresses) as spam so it's worth checking and possibly creating a filter to ensure this doesn't happen in the future.

Many hosts place a limit on the number of emails that can be sent out within an hour so this may also cause some delay in emails arriving. 

= Some of my shortcodes aren't working! =

It's possible you're inserting a shortcode into a notification that cannot use it. For example: the 'New Category' email notification cannot use any of the author or time shortcodes as WordPress only stores the category name, category slug and category description in the database. It's also worth checking the spelling and hyphens in any shortcodes as well as if they are wrapped in square brackets '[]'.

= Can I translate this plugin? =

Yes, of course! The plugin is completely translation-friendly and if you send me your .po file, i'll make sure to include it in the plugin and credit you on this page.

= Will this plugin work with versions WordPress less than 3.5? = 

It might do but this is untested. 



== Screenshots ==

1. All Notifications

2. Add New Notification

3. Plugin Settings



== Changelog ==

= 1.0.2 =
* Bug fix for [ID] shortcode not outputting anything.
* Bug fix for `[post_category]` showing as empty.
* Added tags shortcode for use in certain notifications using `[post_tag]`.

= 1.0.1 =
* Bug fix for notifications not being sent to custom user roles or individual users of custom roles.
* Added a 'Notification Type' column to the notifications screen.
* Renamed 'User Roles' column in notifications screen to 'User Roles/Users'.

= 1.0 =
* First major release - we're no longer in beta!
* Total overhaul. The plugin has been completely re-written.
* Auto-importer: Your old notifications will be imported when updating the plugin.
* New 'Generator' for easily creating and editing your notifications.
* More options and notification types as per feedback via the forums - thanks everyone!
* Granular control over choosing either user roles or individual users you'd like to send a notification to.
* Fully translatable.
* Loads of bugfixes and improvements.

= 0.2.1 beta =
* Minor Bugfixes

= 0.2 beta =
* Added an option to suppress spam comment notifications.

= 0.1 beta =
* Initial version of the plugin.
* Settings page for configuring notification types for roles.
* Email Templates page for customising emails using HTML and shortcodes.



== Upgrade Notice ==

= Migrating from 0.2.1 to 1.0 =

Version 1 features an auto-importer which should migrate all of your notifications from any previous versions of the plugin to 1.0. 
Please ensure that you check, double check and test your notifications before and after upgrading.