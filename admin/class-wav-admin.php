<?php
/**
 * Admin settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WAV_Admin {

    const NONCE_ACTION = 'wav_admin_nonce';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_wav_save_settings', [ $this, 'save_settings' ] );
    }

    public function add_menu(): void {
        add_options_page(
            __( 'Age Verification', 'wp-age-verification' ),
            __( 'Age Verification', 'wp-age-verification' ),
            'manage_options',
            'wp-age-verification',
            [ $this, 'render_page' ]
        );
    }

    public function register_settings(): void {
        register_setting( 'wav_settings_group', WAV_Settings::OPTION_KEY );
    }

    public function enqueue_assets( string $hook ): void {
        if ( 'settings_page_wp-age-verification' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'wav-admin',
            WAV_PLUGIN_URL . 'assets/css/wav-admin.css',
            [],
            WAV_VERSION
        );

        wp_enqueue_script(
            'wav-admin',
            WAV_PLUGIN_URL . 'assets/js/wav-admin.js',
            [],
            WAV_VERSION,
            true
        );

        wp_localize_script( 'wav-admin', 'wavAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
            'saved'   => __( 'Settings saved.', 'wp-age-verification' ),
            'error'   => __( 'Could not save settings.', 'wp-age-verification' ),
        ] );
    }

    /**
     * AJAX: persist settings.
     */
    public function save_settings(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied.', 'wp-age-verification' ) ], 403 );
        }

        check_ajax_referer( self::NONCE_ACTION, 'nonce' );

        $raw = wp_unslash( $_POST );

        $allowed_ages = [ 14, 16, 18, 21 ];
        $min_age      = isset( $raw['minimum_age'] ) ? absint( $raw['minimum_age'] ) : 18;
        if ( ! in_array( $min_age, $allowed_ages, true ) ) {
            $min_age = 18;
        }

        $data = [
            'enabled'             => ! empty( $raw['enabled'] ),
            'verification_type'   => ( isset( $raw['verification_type'] ) && 'birthdate' === $raw['verification_type'] ) ? 'birthdate' : 'buttons',
            'minimum_age'         => $min_age,
            'scope'               => ( isset( $raw['scope'] ) && 'specific' === $raw['scope'] ) ? 'specific' : 'entire_site',
            'specific_pages'      => isset( $raw['specific_pages'] ) ? array_values( array_filter( array_map( 'absint', (array) $raw['specific_pages'] ) ) ) : [],
            'specific_categories' => isset( $raw['specific_categories'] ) ? array_values( array_filter( array_map( 'absint', (array) $raw['specific_categories'] ) ) ) : [],
            'decline_action'      => ( isset( $raw['decline_action'] ) && 'redirect' === $raw['decline_action'] ) ? 'redirect' : 'block',
            'redirect_url'        => isset( $raw['redirect_url'] ) ? esc_url_raw( $raw['redirect_url'] ) : '',
            'blocked_message'     => isset( $raw['blocked_message'] ) ? wp_kses_post( $raw['blocked_message'] ) : '',
            'cookie_duration'     => isset( $raw['cookie_duration'] ) ? max( 1, absint( $raw['cookie_duration'] ) ) : 30,
            'popup_title'         => isset( $raw['popup_title'] ) ? sanitize_text_field( $raw['popup_title'] ) : '',
            'popup_description'   => isset( $raw['popup_description'] ) ? sanitize_textarea_field( $raw['popup_description'] ) : '',
            'button_yes_text'     => isset( $raw['button_yes_text'] ) ? sanitize_text_field( $raw['button_yes_text'] ) : '',
            'button_no_text'      => isset( $raw['button_no_text'] ) ? sanitize_text_field( $raw['button_no_text'] ) : '',
            'overlay_color'       => isset( $raw['overlay_color'] ) ? sanitize_text_field( $raw['overlay_color'] ) : 'rgba(0,0,0,0.85)',
            'popup_style'         => ( isset( $raw['popup_style'] ) && 'dark' === $raw['popup_style'] ) ? 'dark' : 'light',
        ];

        WAV_Settings::save( $data );

        wp_send_json_success( [ 'message' => __( 'Settings saved.', 'wp-age-verification' ) ] );
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $s = WAV_Settings::get_all();
        require WAV_PLUGIN_DIR . 'admin/views/settings-page.php';
    }
}
