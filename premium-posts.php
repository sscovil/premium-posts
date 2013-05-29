<?php
/**
 * Plugin Name: Premium Posts
 * Plugin URI: https://github.com/sscovil/premium-posts
 * Description: Mark posts as "Premium" and display a custom message or ad code.
 * Version: 2.2.1
 * Author: Shaun Scovil
 * Author URI: http://shaunscovil.com
 * Text Domain: premium-posts
 * License: GPL2
 */

// Define file path constants.
define( 'SMS_PREMIUM_POSTS_PATH', plugin_dir_path(__FILE__) );
define( 'SMS_PREMIUM_POSTS_BASE', plugin_basename(__FILE__) );

// Instantiate plugin class.
require_once SMS_PREMIUM_POSTS_PATH . 'lib/class-premium-posts.php';
Premium_Posts::instance();

// Instantiate plugin settings class (wp-admin only).
if ( is_admin() ) {
    require_once SMS_PREMIUM_POSTS_PATH . 'lib/class-premium-posts-settings.php';
    Premium_Posts_Settings::instance();
}

/**
 * Is Premium Post
 *
 * This conditional tag returns true if the current post is marked as Premium, or false if it is not.
 *
 * @param  int  $post The ID of the post to check; use $post->ID when calling from within the loop.
 * @return bool
 */
if ( ! function_exists( 'is_premium_post' ) ) {
    function is_premium_post( $post = null ) {
        return has_term( 'Premium', 'sms_premium_posts', $post );
    }
}

/**
 * Premium Posts
 *
 * This template tag can be used within the loop to display the premium/standard message for a post.
 */
if ( ! function_exists( 'premium_posts' ) ) {
    function premium_posts() {
        echo Premium_Posts::get_post_message();
    }
}