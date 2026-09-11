<?php
/**
 * Admin settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AVPS_Admin {

    const NONCE_ACTION = 'avps_admin_nonce';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_avps_save_settings', [ $this, 'save_settings' ] );
    }

    public function add_menu(): void {
        add_options_page(
            __( 'Age Verification', 'pavel-silinskii-age-verification' ),
            __( 'Age Verification', 'pavel-silinskii-age-verification' ),
            'manage_options',
            'pavel-silinskii-age-verification',
            [ $this, 'render_page' ]
        );
    }

    public function register_settings(): void {
        register_setting(
            'avps_settings_group',
            AVPS_Settings::OPTION_KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [ 'AVPS_Settings', 'sanitize' ],
                'default'           => AVPS_Settings::defaults(),
            ]
        );
    }

    public function enqueue_assets( string $hook ): void {
        if ( 'settings_page_pavel-silinskii-age-verification' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'avps-admin',
            AVPS_PLUGIN_URL . 'assets/css/avps-admin.css',
            [],
            AVPS_VERSION
        );

        wp_enqueue_script(
            'avps-admin',
            AVPS_PLUGIN_URL . 'assets/js/avps-admin.js',
            [],
            AVPS_VERSION,
            true
        );

        wp_localize_script( 'avps-admin', 'avpsAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
            'saved'   => __( 'Settings saved.', 'pavel-silinskii-age-verification' ),
            'error'   => __( 'Could not save settings.', 'pavel-silinskii-age-verification' ),
        ] );
    }

    /**
     * AJAX: persist settings.
     */
    public function save_settings(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied.', 'pavel-silinskii-age-verification' ) ], 403 );
        }

        check_ajax_referer( self::NONCE_ACTION, 'nonce' );

        $data = AVPS_Settings::sanitize( wp_unslash( $_POST ) );

        AVPS_Settings::save( $data );

        wp_send_json_success( [ 'message' => __( 'Settings saved.', 'pavel-silinskii-age-verification' ) ] );
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $s = AVPS_Settings::get_all();
        require AVPS_PLUGIN_DIR . 'admin/views/settings-page.php';
    }
}
