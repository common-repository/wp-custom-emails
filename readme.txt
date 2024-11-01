=== WP Custom Emails ===
Contributors: damian-gora, upsell
Tags: notifications, email, mail, lost password, pingback, trackback, comment, customize
Requires at least: 3.5
Tested up to: 4.4
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily customize WordPress notification emails.

== Description ==

= How it works? =
WP Custom Emails allows you easily customize WordPress notifications, so you can make it prettier and significantly improve user experience. Additionally the plugin gives possibility to complete turn off sending selected emails.

Emails are sent via WordPress *wp_mail()* function, so it is possible to use 3rd part plugin, to authenticate them by SMTP protocol.

= Notifications that are currently available to use are: =
* Lost Password (user)
* Password Changed (admin)
* New User Registration (user)
* New User Registration (admin)
* New Comment (moderator)
* New Comment (post author)
* New Trackback (moderator)
* New Trackback (post author)
* New Pingback (moderator)
* New Pingback (post author)

= More features: =
* Define sender name
* Define sender email
* HTML content type
* WPML compatibility

== Installation ==

1. Install the plugin from within the Dashboard or upload the directory `wp-custom-emails` and all its contents to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -> WP Custom Emails and set your preferences.
4. Enjoy the managage emails.

== Screenshots ==

1. General
2. Menage notifications

== Changelog ==

= 1.2.2 =
* Fixed warning caused by wp_password_change_notification

= 1.2.1 =
* Fixed fatal error when a multisite is enabled

= 1.2 =
* Added text/html content type.
* Fixed undefined variables.

= 1.1 =
* New User Registration (user) updated for WordPress 4.3

= 1.0 =
* First release

== Upgrade Notice ==
