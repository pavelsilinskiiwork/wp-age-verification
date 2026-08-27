<?php
/**
 * Activation / deactivation routines.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AVPS_Installer {

    /**
     * Runs on plugin activation. Stores default settings if none exist yet.
     */
    public static function activate(): void {
        if ( ! get_option( AVPS_Settings::OPTION_KEY ) ) {
            update_option( AVPS_Settings::OPTION_KEY, AVPS_Settings::defaults() );
        }
    }

    /**
     * Runs on plugin deactivation. Data is only removed via uninstall.php.
     */
    public static function deactivate(): void {}
}
