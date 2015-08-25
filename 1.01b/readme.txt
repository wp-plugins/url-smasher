=== URL Smasher ===
Contributors: Rick Hellewell
Donate link: http://cellarweb.com/wordpress-plugins/
Tags: url shortener automatic goo.gl
Requires at least: 4.0.1
Tested up to: 4.3
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically shortens URLs in posts, pages, and comments using goo.gl. 

== Description ==

Automatically - and without any effort on your part - shortens URLs in posts, pages, and comments using goo.gl. Does not require any special shortcodes, buttons, or anything - just enable it, and URLs are smashed when the post/page/comment is saved. Just use your Google API Key (it's free) and off you go!



== Installation ==

This section describes how to install the plugin and get it working.

1. Download the zip file, uncompress, then upload to `/wp-content/plugins/` directory. Or download, then upload/install via the Add Plugin page.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Change settings in Settings, 'URL Smasher Settings' to your requirements.

* Note: do a "Save" on the URL Smasher settings page once after an upgrade to ensure all is well; your settings will be preserved.

== Frequently Asked Questions ==

= Do I have to do anything special to enable this? =

You need to have your own Google API key (they are free, unless you have an extremely active site), and set up the two options on the Settings page.

Get API key from : http://code.google.com/apis/console/

= What settings are available? =

Just three: your Google API Key, and checkboxes to enable URL Smashing for posts/pages or comments.

= What if the URLs aren't smashed? =

That is usually caused by an invalid Google API Key. Or, you have done too many URL Smashes in one day. Check your Google API account for details.

If the URL Smash fails for some reasosn, the URL is not changed.

= What if I don't like how the plugin changes things or there is a problem? = 

You can just deactivate the plugin. Your settings will be saved if you want to reactivate later.

= Does the plugin make changes to the database? = 

The plugin only adds one 'row' to the Options database, using standard WordPress functions. The plugin will read the values as needed, minimizing calls to the database to limit any overhead against the database.

= Does the plugin require anything extra on the client (visitor) browser? = 

Not that we are aware of. The things we do are done through standard WordPress calls. It works with the standard 'jQuery' code that WordPress already includes and uses.

= Where can we go for support if there is a problem or question - or a new feature we think will be nifty? = 

You can use the plugin support page for questions. Or you can contact us directly via the Contact Us page at www.CellarWeb.com . We usually respond within 24 hours (and are usually faster than that).

= How much does the plugin cost? = 

It's free! But there is a place to donate at www.CellarWeb.com, if you are so inclined. (And we will appreciate that inclination!)

= What else do you do? = 

We have a plugin that stops comment spam in it's tracks. Plus a contact template that does the same thing. Our process doesn't rely on things that don't work, like hidden fields, or hard to use Captcha things. You'll find all the details at our FormSpammerTrap site: http://www.FormSpammerTrap.com .

We do lots of WordPress sites: implementation, customization, and more. You can find more info at our business site at www.CellarWeb.com .


== Screenshots ==

1. Shows the URL Smasher settings screen, found on the Settings, 'URL Smasher Settings' screen. (assets/screenshot-1.jpg) (You'll have to enter your own API key.)

== Changelog ==

= 1.01a =
* released 22 Aug 2015
* fixed formatting errors in the readme.txt file.
* updated the screenshot .
* changed the icon/graphic on the settings page

= 1.01 =
* Tested and Certified for WordPress 4.3 (21 Aug 2015)
* Changed top graphic for Settings page
* Added icon for WordPress plugin page

= 1.00 =
* Initial release ((10 Aug 2015)


== Upgrade Notice ==

= 1.01a =
* released 22 Aug 2015
* fixed formatting errors in the readme.txt file.
* updated the screenshot .
* changed the icon/graphic on the settings page

= 1.01 =
* Tested and Certified for WordPress 4.3 (21 Aug 2015)
* Changed top graphic for Settings page
* Added icon for WordPress plugin page

= 1.00 =
* Initial release (10 Aug 2015)


