<?php
/**
 * Settings storage for Age Verification by Pavel Silinskii.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AVPS_Settings {

    const OPTION_KEY = 'avps_settings';

    /**
     * Default settings.
     */
    public static function defaults(): array {
        return [
            'enabled'             => true,
            'verification_type'   => 'buttons',
            'minimum_age'         => 18,
            'scope'               => 'entire_site',
            'specific_pages'      => [],
            'specific_categories' => [],
            'decline_action'      => 'block',
            'redirect_url'        => 'https://google.com',
            'blocked_message'     => __( 'You must be 18 years or older to access this website.', 'age-verification-by-pavel-silinskii' ),
            'cookie_duration'     => 30,
            'popup_title'         => __( 'Age Verification', 'age-verification-by-pavel-silinskii' ),
            'popup_description'   => __( 'This website contains age-restricted content. By entering, you accept our terms and confirm your age is 18 years or older.', 'age-verification-by-pavel-silinskii' ),
            'button_yes_text'     => __( "Yes, I'm 18+", 'age-verification-by-pavel-silinskii' ),
            'button_no_text'      => __( 'No, Exit', 'age-verification-by-pavel-silinskii' ),
            'overlay_color'       => 'rgba(0,0,0,0.85)',
            'popup_style'         => 'light',
        ];
    }

    /**
     * Get all settings merged with defaults.
     */
    public static function get_all(): array {
        $stored = get_option( self::OPTION_KEY, [] );

        if ( ! is_array( $stored ) ) {
            $stored = [];
        }

        return wp_parse_args( $stored, self::defaults() );
    }

    /**
     * Get a single setting.
     */
    public static function get( string $key, $default = null ) {
        $all = self::get_all();

        return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
    }

    /**
     * Save all settings.
     */
    public static function save( array $data ): bool {
        $current = self::get_all();
        $merged  = wp_parse_args( $data, $current );

        return update_option( self::OPTION_KEY, $merged );
    }

    /**
     * Sanitize a raw settings array (from the admin form or the Settings API).
     * Unknown keys are dropped; missing keys fall back to current/defaults.
     */
    public static function sanitize( $raw ): array {
        $raw = is_array( $raw ) ? $raw : [];

        $min_age = isset( $raw['minimum_age'] ) ? absint( $raw['minimum_age'] ) : 18;
        if ( ! in_array( $min_age, [ 14, 16, 18, 21 ], true ) ) {
            $min_age = 18;
        }

        return [
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
    }
}
