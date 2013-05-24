<?php
/**
 * Plugin Name: Premium Posts
 * Plugin URI: https://github.com/sscovil/premium-posts
 * Description: Adds a checkbox to the post publish metabox to mark a post as 'Premium' content.
 * Version: 1.0
 * Author: Shaun Scovil
 * Author URI: http://shaunscovil.com
 * Text Domain: premium-posts
 * License: GPL2
 */

/**
 * Premium Posts Init
 */
function sms_premium_posts_init() {
    load_plugin_textdomain( 'premium-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'sms_premium_posts_init' );

/**
 * Add Premium Post Checkbox
 *
 * This function adds a checkbox in the 'Publish' meta box of the Post editor to toggle Premium Post status.
 */
function sms_add_premium_post_checkbox() {
    global $post;

    // Get the current Premium Posts array from the options table.
    $premium_posts = get_option( 'sms_premium_posts', array() );

    if ( 'post' == get_post_type( $post ) ) {

        // If the post is currently marked premium, the checkbox should be checked.
        $checked = in_array( $post->ID, $premium_posts ) ? true : false;

        // Set the label for the checkbox in a way that can be translated.
        $label = apply_filters( 'premium_post_checkbox_label', __( 'Premium Post', 'premium-posts' ) );

        // Echo the form HTML.
        echo '<div class="misc-pub-section misc-pub-section-last">';
        wp_nonce_field( plugin_basename(__FILE__), 'premium_post_nonce' );
        echo
            '<input type="checkbox" name="premium_post" id="premium_post" ' . checked( $checked, true, false ) . ' />' .
            '<label for="premium_post" class="select-it"> ' . $label . '</label></div>'
        ;
    }
}
add_action( 'post_submitbox_misc_actions', 'sms_add_premium_post_checkbox' );

/**
 * Update Premium Posts Array
 *
 * This function adds/removes the current post ID to/from the Premium Posts array when the post is saved.
 *
 * @param $post_id
 * @return mixed
 */
function sms_update_premium_posts_array( $post_id ) {
    // Autosave, do nothing
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // AJAX? Not used here
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        return $post_id;

    // Check user permissions
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;

    // Return if it's a post revision
    if ( false !== wp_is_post_revision( $post_id ) )
        return $post_id;

    // Verify nonce field
    if ( ! wp_verify_nonce( $_POST['premium_post_nonce'], plugin_basename(__FILE__) ) )
        return $post_id;

    // Verify post type and save option
    if ( 'post' == $_POST['post_type'] ) {

        // Get the value of the checkbox from the post editor.
        $checked = $_POST['premium_post'];

        // Get the current Premium Posts array from the options table.
        $premium_posts = get_option( 'sms_premium_posts', array() );

        // If the box is checked and the current post ID is not already in the Premium Posts array, add it.
        if ( 'on' == $checked && ! in_array( $post_id, $premium_posts ) ) {
            $premium_posts[] = $post_id;

        // Otherwise, remove the current post ID from the Premium Posts array.
        } elseif ( 'on' !== $checked ) {
            $key = array_search( $post_id, $premium_posts );
            if ( false !== $key ) unset( $premium_posts[$key] );
        }

        // Update the Premium Posts array in the options table.
        update_option( 'sms_premium_posts', $premium_posts );
    }

    return $post_id;
}
add_action( 'save_post', 'sms_update_premium_posts_array' );

/**
 * Is Premium Post
 *
 * This function returns true if the current post is marked as Premium. It will only return true when viewing as a
 * single post (i.e. is_single() == true). Use this conditional tag in your single.php template file. A typical use
 * case would be to display a 'premium' ad code if true, or a 'standard' ad code if false.
 *
 * @return bool
 */
function is_premium_post() {
    global $wp_query;

    // Get the current Premium Posts array from the options table.
    $premium_posts = get_option( 'sms_premium_posts', array() );

    if ( isset( $wp_query ) && is_single() && in_array( $wp_query->post->ID, $premium_posts ) )
        return true;

    return false;
}