<?php
/**
 * Plugin Name: WP Age Verification
 * Plugin URI:  https://github.com/pavelsilinskiiwork/wp-age-verification
 * Description: Age verification popup for WordPress. Supports Yes/No and date of birth verification modes.
 * Version:     1.0.0
 * Author:      Pavel Silinskii
 * Author URI:  https://github.com/pavelsilinskiiwork
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-age-verification
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WAV_VERSION', '1.0.0' );
define( 'WAV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WAV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WAV_PLUGIN_DIR . 'includes/class-wav-installer.php';
require_once WAV_PLUGIN_DIR . 'includes/class-wav-settings.php';
require_once WAV_PLUGIN_DIR . 'includes/class-wav-frontend.php';
require_once WAV_PLUGIN_DIR . 'admin/class-wav-admin.php';

register_activation_hook( __FILE__, [ 'WAV_Installer', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'WAV_Installer', 'deactivate' ] );

add_action( 'plugins_loaded', function () {
    load_plugin_textdomain( 'wp-age-verification', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    new WAV_Frontend();

    if ( is_admin() ) {
        new WAV_Admin();
    }
} );
