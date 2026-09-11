<?php
/**
 * Plugin Name: Pavel Silinskii Age Verification
 * Plugin URI:  https://github.com/pavelsilinskiiwork/wp-age-verification
 * Description: Age verification popup for WordPress. Supports Yes/No and date of birth verification modes.
 * Version:     1.0.0
 * Author:      Pavel Silinskii
 * Author URI:  https://github.com/pavelsilinskiiwork
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pavel-silinskii-age-verification
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'AVPS_VERSION', '1.0.0' );
define( 'AVPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AVPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once AVPS_PLUGIN_DIR . 'includes/class-avps-installer.php';
require_once AVPS_PLUGIN_DIR . 'includes/class-avps-settings.php';
require_once AVPS_PLUGIN_DIR . 'includes/class-avps-frontend.php';
require_once AVPS_PLUGIN_DIR . 'admin/class-avps-admin.php';

register_activation_hook( __FILE__, [ 'AVPS_Installer', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'AVPS_Installer', 'deactivate' ] );

add_action( 'plugins_loaded', function () {
    new AVPS_Frontend();

    if ( is_admin() ) {
        new AVPS_Admin();
    }
} );
