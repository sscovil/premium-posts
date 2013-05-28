=== Premium Posts ===
Contributors: sscovil 
Tags: post, posts, premium, mark, tag, flag, conditional
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 2.1

Mark posts as "Premium" and display a custom message or ad code.


== Description ==

Want to add a special message or ad code to the most popular posts on your blog? This plugin enables you to do just that!

1. Install and activate this plugin.
2. Mark your top posts as "Premium" using a checkbox that has been added to your Post Editor and Quick Edit screens.
3. Go to `WP-Admin > Settings > Reading` and wdit the message you would like to display for premium and/or standard posts.
4. Choose to place this message above or below the post content, or manually place it using the `<?php premium_posts(); ?>` template tag.
5. View your posts to see your message!

This plugin allows publishers to mark posts as "Premium". It also adds the conditional tag `is_premium_post()` for use in your theme template files.  

Place your premium/standard post message wherever you want by using the `<?php premium_posts(); ?>` template tag inside [The Loop](http://codex.wordpress.org/The_Loop "WordPress Codex: The Loop").

You can also test if a specific post is marked as premium by passing the post ID (or post object) as a parameter to the `is_premium_post()` function.

Example:

`<?php if ( is_premium_post( $post_id ) ) { ?>

	<?php get_the_title( $post_id ); ?> is a premium post!

<?php } ?>`


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

Settings for this plugin can be found in `WP-Admin > Settings > Reading` under the heading `Premium Posts`.


== Screenshots ==

1. A `Premium Post` checkbox is added to the publish metabox in the post editor screen.
2. A `Premium Post` column is added to the post admin table.
3. A `Premium Post` checkbox is added to the Quick Edit menu on the post admin table.
4. Plugin options are added to `WP-Admin > Settings > Reading`.


== Changelog ==

= 2.1 =
* Added plugin settings to `WP-Admin > Settings > Reading` for easy management of premium & standard post messages.
* Added `premium_posts()` template tag for manual placement of premium/standard post message.

= 2.0.1 =
* Added term filter for Standard posts in Posts admin table.

= 2.0 =
* Updated version now uses a hidden taxonomy to mark posts as premium.
* Added a column to the `Posts` admin table.
* Added a checkbox to the `Quick Edit` menu.
* Added term filter for premium posts in `Posts` admin table.
* Cleaned up code by moving functions into a singleton class.
* Added a .pot file for localization.

= 1.0 =
* Initial release stored post IDs of premium posts as an array in the options table.