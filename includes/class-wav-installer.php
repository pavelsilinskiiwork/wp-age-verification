<?php
/**
 * Activation / deactivation routines.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WAV_Installer {

    /**
     * Runs on plugin activation. Stores default settings if none exist yet.
     */
    public static function activate(): void {
        if ( ! get_option( WAV_Settings::OPTION_KEY ) ) {
            update_option( WAV_Settings::OPTION_KEY, WAV_Settings::defaults() );
        }
    }

    /**
     * Runs on plugin deactivation. Data is only removed via uninstall.php.
     */
    public static function deactivate(): void {}
}
