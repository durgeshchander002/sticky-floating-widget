<?php
/**
 * Plugin Name: Sticky Floating Widget Generator
 * Description: Easily add floating action buttons like WhatsApp, Call Now, Email, or Custom CTAs to your WordPress site.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL2
 * Text Domain: sticky-widget
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class StickyFloatingWidget {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_footer', [ $this, 'render_widget' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'sticky-widget-style', plugin_dir_url( __FILE__ ) . 'assets/style.css', [], '1.0.0' );
    }

    public function render_widget() {
        $type = esc_attr( get_option( 'sticky_widget_type', 'whatsapp' ) );
        $label = esc_html( get_option( 'sticky_widget_label', 'Chat with us' ) );
        $link = esc_url( get_option( 'sticky_widget_link', 'https://wa.me/1234567890' ) );

        echo '<div class="sticky-floating-widget">';
        echo '<a href="' . $link . '" target="_blank" rel="noopener noreferrer">' . esc_html( $label ) . '</a>';
        echo '</div>';
    }

    public function admin_menu() {
        add_options_page(
            'Sticky Floating Widget',
            'Sticky Widget',
            'manage_options',
            'sticky-floating-widget',
            [ $this, 'settings_page' ]
        );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Sticky Floating Widget Settings', 'sticky-widget' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'sticky_widget_group' );
                do_settings_sections( 'sticky-floating-widget' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting( 'sticky_widget_group', 'sticky_widget_type', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'sticky_widget_group', 'sticky_widget_label', [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'sticky_widget_group', 'sticky_widget_link', [ 'sanitize_callback' => 'esc_url_raw' ] );

        add_settings_section( 'sticky_widget_main', '', null, 'sticky-floating-widget' );

        add_settings_field(
            'sticky_widget_type',
            'Button Type (e.g., WhatsApp, Call, Email)',
            function() {
                $value = esc_attr( get_option( 'sticky_widget_type', 'whatsapp' ) );
                echo '<input type="text" name="sticky_widget_type" value="' . $value . '" class="regular-text">';
            },
            'sticky-floating-widget',
            'sticky_widget_main'
        );

        add_settings_field(
            'sticky_widget_label',
            'Button Label',
            function() {
                $value = esc_attr( get_option( 'sticky_widget_label', 'Chat with us' ) );
                echo '<input type="text" name="sticky_widget_label" value="' . $value . '" class="regular-text">';
            },
            'sticky-floating-widget',
            'sticky_widget_main'
        );

        add_settings_field(
            'sticky_widget_link',
            'Button Link (URL)',
            function() {
                $value = esc_url( get_option( 'sticky_widget_link', '' ) );
                echo '<input type="url" name="sticky_widget_link" value="' . $value . '" class="regular-text">';
            },
            'sticky-floating-widget',
            'sticky_widget_main'
        );
    }
}

StickyFloatingWidget::get_instance();