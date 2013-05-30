=== Premium Posts ===

Contributors: sscovil 
Tags: post, posts, premium, mark, tag, flag, conditional
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 2.3

Mark posts as "Premium" and display a custom message or ad code.


== Description ==

Want to add a special message or ad code to your most popular blog posts? This plugin enables you to do just that!

1. Install and activate the plugin via `WP-Admin > Plugins`.
2. Mark your top posts as "Premium" using a checkbox in the Post Editor.
3. Go to `WP-Admin > Settings > Reading` and edit your premium post message.
4. Choose to place your message above or below the post content, or place it manually using the `<?php premium_posts(); ?>` template tag inside [The Loop](http://codex.wordpress.org/The_Loop "WordPress Codex: The Loop").

This plugin also adds the conditional tag `is_premium_post()` for use in your theme template files. You can use it to test if a specific post is marked as premium by passing the post ID (or post object) as a parameter.

Example:

`<?php if ( is_premium_post( $post_id ) ) { ?>

	<?php echo get_the_title( $post_id ); ?> is a premium post!

<?php } ?>`


== Installation ==

1. Install and activate the plugin via `WP-Admin > Plugins`.
2. Mark your top posts as "Premium" using a checkbox in the Post Editor.
3. Go to `WP-Admin > Settings > Reading` and edit your premium post message.
4. Choose to place your message above or below the post content, or place it manually using the `<?php premium_posts(); ?>` template tag inside [The Loop](http://codex.wordpress.org/The_Loop "WordPress Codex: The Loop").


== Screenshots ==

1. A `Premium Post` checkbox is added to the publish metabox in the post editor screen.
2. A `Premium Post` column is added to the post admin table.
3. A `Premium Post` checkbox is added to the Quick Edit menu on the post admin table.
4. Plugin options are added to `WP-Admin > Settings > Reading`.


== Changelog ==

= 2.3 =
* Security update: Added data sanitization to plugin settings displayed in form fields on settings page.

= 2.2.2 =
* Bug fix: resolved undefined index error in `WP-Admin > Posts > Add New`.

= 2.2.1 =
* Bug fix: save_post() method was throwing an error in the QuickPress dashboard widget.
* Renamed premium_post_message() method to the more semantic get_post_message().
* Clarified some inline documentation.

= 2.2 =
* Security update: Added settings field sanitization callback.

= 2.1 =
* Added plugin settings to `WP-Admin > Settings > Reading` for easy management of premium & standard post messages.
* Added `premium_posts()` template tag for manual placement of premium/standard post message.

= 2.0.1 =
* Added term filter for Standard posts in Posts admin table.

= 2.0 =
* Updated version uses a hidden taxonomy to mark posts as premium.
* Added a column to the `Posts` admin table.
* Added a checkbox to the `Quick Edit` menu.
* Added term filter for premium posts in `Posts` admin table.
* Cleaned up code by moving functions into a singleton class.
* Added a .pot file for localization.

= 1.0 =
* Initial release stored post IDs of premium posts as an array in the options table.