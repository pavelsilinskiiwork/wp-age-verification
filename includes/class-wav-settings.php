<?php
/**
 * Settings storage for WP Age Verification.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WAV_Settings {

    const OPTION_KEY = 'wav_settings';

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
            'blocked_message'     => __( 'You must be 18 years or older to access this website.', 'wp-age-verification' ),
            'cookie_duration'     => 30,
            'popup_title'         => __( 'Age Verification', 'wp-age-verification' ),
            'popup_description'   => __( 'This website contains age-restricted content. By entering, you accept our terms and confirm your age is 18 years or older.', 'wp-age-verification' ),
            'button_yes_text'     => __( "Yes, I'm 18+", 'wp-age-verification' ),
            'button_no_text'      => __( 'No, Exit', 'wp-age-verification' ),
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
}
