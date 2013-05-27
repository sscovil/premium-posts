=== Premium Posts ===
Contributors: sscovil 
Tags: post, posts, premium, mark, tag, flag, conditional
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 2.0.1

Adds a checkbox to the Edit Post screen that allows you to mark a post as 'Premium'.


== Description ==

This simple plugin allows publishers to mark posts as "Premium". It also adds the conditional tag `is_premium_post()` for use in your theme template files.  

A typical use case would be to display a unique message or ad code when viewing a premium post. For example:  

`<?php if ( is_premium_post() ) { ?>

	This is a premium post!

<?php } else { ?>

	This is NOT a premium post!

<?php } ?>`

You can also test a specific post by passing the post ID or post object as a parameter. For example:

`<?php if ( is_premium_post( 23 ) ) { ?>

	The post: <?php get_the_title( 23 ); ?> is a premium post!

<?php } ?>`

**NOTE:** You must use the conditional tag somewhere in your theme, or this plugin will do nothing.


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

There are no settings or configuration for this plugin.

**NOTE:** You must use the conditional tag somewhere in your theme, or this plugin will do nothing.


== Screenshots ==

1. A `Premium Post` checkbox is added to the publish metabox in the post editor screen.
2. A `Premium Post` column is added to the post admin table.
3. A `Premium Post` checkbox is added to the Quick Edit menu on the post admin table.


== Changelog ==

= 2.0.1 =
* Added term filter for Standard posts in Posts admin table.

= 2.0 =
* Updated version now uses a hidden taxonomy to mark posts as premium.
* Added a column to the post admin table and a checkbox to the Quick Edit menu.
* Added term filter for Premium posts in Posts admin table.
* Cleaned up code by moving functions into a singleton class.
* Added a .pot file for localization.

= 1.0 =
* Initial release stored post IDs of premium posts as an array in the options table.