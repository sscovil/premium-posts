<?php
/**
 * Plugin Name: Premium Posts
 * Plugin URI: https://github.com/sscovil/premium-posts
 * Description: Adds a checkbox to the Edit Post screen that allows you to mark a post as 'Premium'.
 * Version: 2.0
 * Author: Shaun Scovil
 * Author URI: http://shaunscovil.com
 * Text Domain: premium-posts
 * License: GPL2
 */

// Instantiate plugin class.
Premium_Posts::instance();

/**
 * Is Premium Post
 *
 * This conditional tag returns true if the current post is marked as Premium, or false if it is not.
 * Use it somewhere in your theme to display content only for Premium posts. A typical use case would be
 * to display one ad code for premium content and another for standard content.
 *
 * @param  int  $post The ID of the post to check; use $post->ID when calling from within the loop.
 * @return bool
 */
function is_premium_post( $post = null ) {
    return has_term( 'Premium', 'sms_premium_posts', $post );
}

class Premium_Posts {

    protected static $instance;

    /**
     * Singleton Factory
     *
     * @return object
     */
    public static function instance() {
        if ( !isset( self::$instance ) ) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    /**
     * Construct
     */
    protected function __construct() {
        // Load textdomain for localization.
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Register hidden taxonomy used to tag premium posts.
        add_action( 'init', array( $this, 'register_taxonomy' ) );

        // Add checkbox to Post editor.
        add_action( 'post_submitbox_misc_actions', array( $this, 'add_checkbox_to_post_editor' ) );

        // Add checkbox to Quick Edit screen.
        add_filter( 'manage_posts_columns', array( $this, 'add_column_to_post_table' ) );
        add_action( 'manage_posts_custom_column', array( $this, 'populate_post_table_column' ), 10, 2 );
        add_action( 'quick_edit_custom_box', array( $this, 'add_checkbox_to_quick_edit' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_quick_edit_js' ) );

        // Save premium post settings.
        add_action( 'save_post', array( $this, 'save_post' ) );
    }

    /**
     * Load Text Domain
     */
    function load_textdomain() {
        load_plugin_textdomain( 'premium-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }

    /**
     * Register Taxonomy
     */
    function register_taxonomy()  {
        $label = apply_filters( 'premium_post_checkbox_label', __( 'Premium Post', 'premium-posts' ) );
        $args = array(
            'label'             => $label,
            'hierarchical'      => false,
            'public'            => false,
            'show_ui'           => false,
            'show_admin_column' => false,
            'show_in_nav_menus' => false,
            'show_tagcloud'     => false,
            'rewrite'           => false,
        );
        register_taxonomy( 'sms_premium_posts', 'post', $args );
    }

    /**
     * Add Checkbox to Post Editor
     */
    function add_checkbox_to_post_editor() {
        global $post;

        if ( 'post' !== get_post_type( $post ) )
            return;

        // If the post is currently marked premium, the checkbox should be checked.
        $checked = is_object_in_term( $post->ID, 'sms_premium_posts', 'Premium' );

        // Set the label for the checkbox in a way that can be translated.
        $label = apply_filters( 'premium_post_checkbox_label', __( 'Premium Post', 'premium-posts' ) );

        // Echo the form HTML.
        echo '<div class="misc-pub-section misc-pub-section-last">';
        wp_nonce_field( plugin_basename(__FILE__), 'premium_post_nonce' );
        echo
            '<label for="premium_post" class="select-it">' .
            '<input type="checkbox" name="premium_post" id="premium_post" ' .
            checked( $checked, true, false ) . ' /> ' . $label . '</label></div>'
        ;
    }

    /**
     * Add Column to Post Table
     *
     * @param  array $cols
     * @return array
     */
    function add_column_to_post_table( $cols ) {
        $cols['sms_premium_posts'] = apply_filters( 'premium_post_checkbox_label', __( 'Premium Post', 'premium-posts' ) );
        return $cols;
    }

    /**
     * Populate Post Table Column
     *
     * @param $column
     * @param $post_id
     */
    function populate_post_table_column( $column, $post_id ) {
        switch( $column ) {
            case 'sms_premium_posts':
                // If the post is currently marked premium, the checkbox should be checked.
                $checked = is_object_in_term( $post_id, 'sms_premium_posts', 'Premium' );

                // Echo a hidden checkbox that will be added via JavaScript to the Quick Edit menu.
                echo
                    '<input style="visibility: hidden; display: none;" type="checkbox" name="premium_post_placeholder" id="premium_post" ' .
                    checked( $checked, true, false ) . ' readonly="readonly" />'
                ;

                // Display whether the current post is marked as Premium (true) or not (false).
                $true  = '<a href="' . get_admin_url( null, 'edit.php?sms_premium_posts=premium' ) . '">' . __( 'Premium', 'premium-posts' ) . '</a>';
                $false = __( 'Standard', 'premium-posts' );
                echo $checked ? $true : $false;
                break;
        }
    }

    /**
     * Add Checkbox to Quick Edit
     *
     * @param $column_name
     * @param $post_type
     */
    function add_checkbox_to_quick_edit( $column_name, $post_type ) {
        if ( 'sms_premium_posts' !== $column_name )
            return;

        static $printNonce = true;
        if ( $printNonce ) {
            $printNonce = false;
            wp_nonce_field( plugin_basename( __FILE__ ), 'premium_post_nonce' );
        }

        // Set the label for the checkbox in a way that can be translated.
        $label = apply_filters( 'premium_post_checkbox_label', __( 'Premium Post', 'premium-posts' ) );

        // Echo the form HTML.
        echo
            '<fieldset class="inline-edit-col-left"><div class="inline-edit-col">' .
            '<label for="premium_post" class="select-it">' .
            '<input type="checkbox" name="premium_post" id="premium_post" /> ' .
            $label . '</label></div></fieldset>'
        ;
    }

    /**
     * Load Quick Edit JS
     */
    function load_quick_edit_js() {
        global $pagenow;
        if ( $pagenow == 'edit.php' ) {
            wp_register_script(
                'premium-posts-js',
                plugins_url( '/js/premium-posts.js', __FILE__ ),
                array( 'jquery' )
            );
            wp_enqueue_script( 'premium-posts-js' );
        }
    }

    /**
     * Save Post
     *
     * @param  int   $post_id
     * @return mixed
     */
    function save_post( $post_id ) {
        // Autosave? Do nothing.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // User cannot edit post? Do nothing.
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;

        // Saving a post revision? Do nothing.
        if ( false !== wp_is_post_revision( $post_id ) )
            return $post_id;

        // Verify nonce field for security.
        if ( ! wp_verify_nonce( $_POST['premium_post_nonce'], plugin_basename(__FILE__) ) )
            return $post_id;

        // Verify post type and save option.
        if ( 'post' == $_POST['post_type'] ) {

            // If the box is checked tag the post as premium; else remove the tag.
            if ( isset( $_POST['premium_post'] ) ) {
                wp_set_object_terms( $post_id, 'Premium', 'sms_premium_posts' );
            } else {
                wp_set_object_terms( $post_id, null, 'sms_premium_posts' );
            }

        }
        return $post_id;
    }

}