<?php
/**
 * Frontend logic: asset loading, popup rendering, AJAX verification.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AVPS_Frontend {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_footer', [ $this, 'render_popup' ] );
        add_action( 'wp_ajax_nopriv_avps_verify', [ $this, 'handle_verify' ] );
        add_action( 'wp_ajax_avps_verify', [ $this, 'handle_verify' ] );
    }

    /**
     * Decide whether the popup should be shown on the current request.
     */
    public function should_show_popup(): bool {
        $settings = AVPS_Settings::get_all();

        if ( empty( $settings['enabled'] ) ) {
            return false;
        }

        if ( isset( $_COOKIE['avps_verified'] ) && '1' === $_COOKIE['avps_verified'] ) {
            return false;
        }

        if ( 'entire_site' === $settings['scope'] ) {
            return true;
        }

        // scope = specific
        $pages      = array_map( 'absint', (array) $settings['specific_pages'] );
        $categories = array_map( 'absint', (array) $settings['specific_categories'] );

        if ( $pages && is_page( $pages ) ) {
            return true;
        }

        if ( $categories ) {
            if ( is_category( $categories ) ) {
                return true;
            }

            if ( is_singular( 'post' ) && in_category( $categories ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enqueue CSS/JS only when the popup will be rendered.
     */
    public function enqueue_assets(): void {
        if ( ! $this->should_show_popup() ) {
            return;
        }

        $settings = AVPS_Settings::get_all();

        wp_enqueue_style(
            'avps-frontend',
            AVPS_PLUGIN_URL . 'assets/css/avps-frontend.css',
            [],
            AVPS_VERSION
        );

        wp_enqueue_script(
            'avps-frontend',
            AVPS_PLUGIN_URL . 'assets/js/avps-frontend.js',
            [],
            AVPS_VERSION,
            true
        );

        wp_localize_script( 'avps-frontend', 'avpsData', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'avps_nonce' ),
            'type'    => $settings['verification_type'],
            'i18n'    => [
                'enterDate' => __( 'Please enter your date of birth.', 'age-verification-by-pavel-silinskii' ),
                'reload'    => __( 'Refresh page', 'age-verification-by-pavel-silinskii' ),
                'failed'    => __( 'Verification failed. Please try again.', 'age-verification-by-pavel-silinskii' ),
            ],
        ] );
    }

    /**
     * Output the popup markup in the footer.
     */
    public function render_popup(): void {
        if ( ! $this->should_show_popup() ) {
            return;
        }

        $settings = AVPS_Settings::get_all();

        $style           = 'dark' === $settings['popup_style'] ? 'dark' : 'light';
        $overlay_color   = $settings['overlay_color'];
        $title           = $settings['popup_title'];
        $description     = $settings['popup_description'];
        $button_yes_text = $settings['button_yes_text'];
        $button_no_text  = $settings['button_no_text'];
        $minimum_age     = absint( $settings['minimum_age'] );

        if ( 'birthdate' === $settings['verification_type'] ) {
            require AVPS_PLUGIN_DIR . 'templates/popup-birthdate.php';
        } else {
            require AVPS_PLUGIN_DIR . 'templates/popup-buttons.php';
        }
    }

    /**
     * AJAX handler for age verification.
     */
    public function handle_verify(): void {
        check_ajax_referer( 'avps_nonce', 'nonce' );

        $settings = AVPS_Settings::get_all();

        if ( 'birthdate' === $settings['verification_type'] ) {
            $birthdate = isset( $_POST['birthdate'] ) ? sanitize_text_field( wp_unslash( $_POST['birthdate'] ) ) : '';

            if ( '' === $birthdate || ! $this->is_valid_date( $birthdate ) ) {
                wp_send_json_error( [
                    'action'  => 'error',
                    'message' => __( 'Please enter a valid date of birth.', 'age-verification-by-pavel-silinskii' ),
                ] );
            }

            $age         = $this->calculate_age( $birthdate );
            $minimum_age = absint( $settings['minimum_age'] );

            if ( $age >= $minimum_age ) {
                $this->set_verified_cookie( $settings );
                wp_send_json_success();
            }

            wp_send_json_error( [
                'action'  => 'error',
                'lock'    => true,
                'message' => sprintf(
                    /* translators: %d: minimum age */
                    __( 'Sorry, you must be at least %d years old to enter this site.', 'age-verification-by-pavel-silinskii' ),
                    $minimum_age
                ),
            ] );
        }

        // verification_type = buttons
        $confirmed = isset( $_POST['confirmed'] ) && 'true' === $_POST['confirmed'];

        if ( $confirmed ) {
            $this->set_verified_cookie( $settings );
            wp_send_json_success();
        }

        if ( 'redirect' === $settings['decline_action'] ) {
            wp_send_json_error( [
                'action' => 'redirect',
                'url'    => esc_url_raw( $settings['redirect_url'] ),
            ] );
        }

        wp_send_json_error( [
            'action'  => 'block',
            'message' => wp_kses_post( $settings['blocked_message'] ),
        ] );
    }

    /**
     * Set the verification cookie.
     */
    private function set_verified_cookie( array $settings ): void {
        $duration = max( 1, absint( $settings['cookie_duration'] ) );

        setcookie(
            'avps_verified',
            '1',
            time() + ( DAY_IN_SECONDS * $duration ),
            COOKIEPATH,
            COOKIE_DOMAIN,
            is_ssl(),
            true
        );

        $_COOKIE['avps_verified'] = '1';
    }

    /**
     * Validate a YYYY-MM-DD date string.
     */
    private function is_valid_date( string $date ): bool {
        $parts = explode( '-', $date );

        if ( 3 !== count( $parts ) ) {
            return false;
        }

        [ $year, $month, $day ] = array_map( 'intval', $parts );

        return checkdate( $month, $day, $year );
    }

    /**
     * Calculate full years between the given birth date and today.
     */
    private function calculate_age( string $birthdate ): int {
        try {
            $dob   = new DateTime( $birthdate );
            $today = new DateTime( 'today' );
        } catch ( Exception $e ) {
            return 0;
        }

        if ( $dob > $today ) {
            return 0;
        }

        return (int) $dob->diff( $today )->y;
    }
}
