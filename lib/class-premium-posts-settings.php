<?php
/**
 * Class Premium_Posts_Settings
 *
 * @package premium-posts
 */
class Premium_Posts_Settings {

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
        // Add quick link to plugin settings on Plugins admin page.
        add_filter( 'plugin_action_links_' . SMS_PREMIUM_POSTS_BASE, array( $this, 'settings_link' ) );

        // Add plugin settings to WP-Admin > Settings > Reading page.
        add_action( 'admin_init', array( $this, 'plugin_settings' ) );
    }

    /**
     * Settings Link
     *
     * @param  array $links
     * @return array $links
     */
    function settings_link( $links ) {
        $settings_link = '<a href="options-reading.php">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Plugin Settings
     *
     * Registers settings array and adds settings sections and fields using the WordPress Settings API.
     */
    function plugin_settings() {
        $option_name = $option_group = Premium_Posts::get_option_name();
        $option_page = 'reading';

        // Register Plugin Settings
        register_setting( $option_page, $option_name, array( $this, 'sanitize_settings' ) );

        // Add Plugin Settings Section to WP-Admin > Settings > Reading
        add_settings_section(
            $option_group,
            $title = __( 'Premium Posts', 'premium-posts' ),
            $callback = array( $this, 'plugin_settings_message' ),
            $option_page
        );

        // Add Setting: Premium Message
        add_settings_field(
            $option_id = 'premium_message',
            $title = __( 'Premium Message', 'premium-posts' ),
            $callback = array( $this, 'editor' ),
            $option_page,
            $option_group,
            $args = array(
                'id'      => $option_name . '[premium]',
                'key'     => 'premium',
                'default' => ''
            )
        );

        // Add Setting: Standard Message
        add_settings_field(
            $option_id = 'standard_message',
            $title = __( 'Standard Message', 'premium-posts' ),
            $callback = array( $this, 'editor' ),
            $option_page,
            $option_group,
            $args = array(
                'id'      => $option_name . '[standard]',
                'key'     => 'standard',
                'default' => ''
            )
        );

        // Add Setting: Message Position
        add_settings_field(
            $option_id = 'message_position',
            $title = __( 'Message Position', 'premium-posts' ),
            $callback = array( $this, 'radio' ),
            $option_page,
            $option_group,
            $args = array(
                'id'      => $option_name . '[position]',
                'key'     => 'position',
                'options' => array(
                    'above'  => __( 'Above post content', 'premium-posts' ),
                    'below'  => __( 'Below post content', 'premium-posts' ),
                    'manual' => __( 'Manual placement via premium_posts() template tag', 'premium-posts' )
                ),
                'default' => 'above'
            )
        );
    }

    /**
     * Plugin Settings Message
     */
    function plugin_settings_message() {
        $message = __( 'Use the editors below to create special messages that will appear above or below all Premium and Standard posts, respectively. Leave blank to show no message.', 'premium-posts' );
        echo '<span class="description">' . $message . '</span>';
    }

    /**
     * Settings Field: Editor
     *
     * @param array $args Arguments passed via add_settings_field().
     */
    function editor( $args ) {
        $current = Premium_Posts::get_settings( $args['key'] ) ? Premium_Posts::get_settings( $args['key'] ) : $args['default'];
        wp_editor(
            $current,
            $editor_id = $args['id'],
            $settings = array(
                'textarea_rows' => 6
            )
        );
    }

    /**
     * Settings Field: Radio Buttons
     *
     * @param array $args Arguments passed via add_settings_field().
     */
    function radio( $args ) {
        $current = Premium_Posts::get_settings( $args['key'] ) ? Premium_Posts::get_settings( $args['key'] ) : $args['default'];
        foreach( $args['options'] as $value => $label ) {
            printf(
                '<input type="radio" name="%1$s" value="%2$s" %3$s /> %4$s<br />',
                $args['id'],
                $value,
                $checked = checked( $current, $value, $echo = false ),
                $label
            );
        }
    }

    /**
     * Sanitize Settings
     *
     * @param  array $settings Settings as key => value pairs before they are saved to the database.
     * @return array
     */
    function sanitize_settings( $settings ) {
        foreach( $settings as $key => $value ) {
            switch( $key ) {
                case 'premium':
                case 'standard':
                    $settings[$key] = wp_kses_post( $value );
                    break;
                case 'position':
                    $settings[$key] = esc_attr( $value );
                    break;
            }
        }
        return $settings;
    }

}