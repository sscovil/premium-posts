<?php
/**
 * Class Premium_Posts
 *
 * @package premium-posts
 */
class Premium_Posts {

    protected static $instance, $option_name, $settings;

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
        self::$option_name = 'sms_premium_posts';
        self::$settings    = get_option( self::get_option_name(), array() );

        // Load textdomain for localization.
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Register hidden taxonomy used to tag premium posts.
        add_action( 'init', array( $this, 'register_taxonomy' ) );

        // Add checkbox to Edit Post screen.
        add_action( 'post_submitbox_misc_actions', array( $this, 'add_checkbox_to_post_editor' ) );

        // Add taxonomy column to Posts admin table manually, for Quick Edit menu support.
        add_filter( 'manage_posts_columns', array( $this, 'add_column_to_post_table' ) );
        add_action( 'manage_posts_custom_column', array( $this, 'populate_post_table_column' ), 10, 2 );

        // Make 'Standard' taxonomy term filter the posts list in wp-admin.
        add_action( 'pre_get_posts', array( $this, 'standard_term_filter' ) );

        // Add checkbox to Quick Edit screen.
        add_action( 'quick_edit_custom_box', array( $this, 'add_checkbox_to_quick_edit' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_quick_edit_js' ) );

        // Save premium post settings.
        add_action( 'save_post', array( $this, 'save_post' ) );

        // Add premium/standard post message to post content.
        add_filter( 'the_content', array( $this, 'insert_post_message' ) );
    }

    /**
     * Get Option Name
     *
     * @return string Name of the plugin settings array saved to wp_options table.
     */
    public static function get_option_name() {
        return (string) self::$option_name;
    }

    /**
     * Get Settings
     *
     * @param  string $key       A specific option key to return.
     * @return array|bool|string Array of all plugin settings, or a single plugin setting, or false.
     */
    public static function get_settings( $key = null ) {
        $settings = (array) self::$settings;
        $key = esc_attr( $key );

        if ( $key )
            return isset( $settings[$key] ) ? (string) $settings[$key] : false;

        return (array) $settings;
    }

    /**
     * Load Text Domain
     */
    function load_textdomain() {
        load_plugin_textdomain( 'premium-posts', false, SMS_PREMIUM_POSTS_PATH . 'lang/' );
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
        wp_nonce_field( SMS_PREMIUM_POSTS_BASE, 'premium_post_nonce' );
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
                $false = '<a href="' . get_admin_url( null, 'edit.php?sms_premium_posts!=premium' ) . '">' . __( 'Standard', 'premium-posts' ) . '</a>';
                echo $checked ? $true : $false;
                break;
        }
    }

    /**
     * Standard Term Filter
     *
     * Props to @s_ha_dum: http://bit.ly/13cKT6v
     *
     * @param $query
     */
    function standard_term_filter( $query ) {
        if ( ! $query->is_admin || ! isset( $_GET['sms_premium_posts!'] ) )
            return $query;

        if ( 'premium' == $_GET['sms_premium_posts!'] ) {
            $taxquery = array(
                array(
                    'taxonomy' => 'sms_premium_posts',
                    'field'    => 'slug',
                    'terms'    => 'premium',
                    'operator' => 'NOT IN'
                )
            );
            $query->set( 'tax_query', $taxquery );
        }
        return $query;
    }

    /**
     * Add Checkbox to Quick Edit
     *
     * @param $column_name
     * @param $post_type
     */
    function add_checkbox_to_quick_edit( $column_name, $post_type ) {
        if ( 'post' !== $post_type || 'sms_premium_posts' !== $column_name )
            return;

        static $printNonce = true;
        if ( $printNonce ) {
            $printNonce = false;
            wp_nonce_field( SMS_PREMIUM_POSTS_BASE, 'premium_post_nonce' );
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
                plugins_url( '/js/premium-posts.js', dirname(__FILE__) ),
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
        if ( ! wp_verify_nonce( $_POST['premium_post_nonce'], SMS_PREMIUM_POSTS_BASE ) )
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

    /**
     * Insert Post Message
     *
     * @param $content
     * @return mixed
     */
    function insert_post_message( $content ) {
        if ( is_single() ) {
            $message  = self::premium_post_message();
            $position = self::get_settings( 'position' );

            switch( $position ) {
                case 'above':
                    $content = $message . $content;
                    break;
                case 'below':
                    $content = $content . $message;
                    break;
            }
        }

        return $content;
    }

    /**
     * Premium Post Message
     *
     * @return array|bool|string
     */
    public static function premium_post_message() {
        if ( is_premium_post() )
            return self::get_settings( 'premium' );

        return self::get_settings( 'standard' );
    }

}