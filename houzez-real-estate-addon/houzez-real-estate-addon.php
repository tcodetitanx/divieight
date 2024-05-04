<?php
/**
 * Plugin file.
 *
 * @package HRE_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly. test
}
/**
 * Plugin Name:     Houzez Real Estate Addon
 * Plugin URI:      https://pereere.com/wordpress-plugins/houzez-real-estate-addon
 * Description:     This plugin acts as an addon to the Houzez theme and woocommerce, specifically for this site <a href="https://divieight.com/">divieight.com</a>.  It allows the admin to create an Agent by approving the forms submitted by agents. It allows users purchase membership and then be able to apply to properties.
 * Author:          Pereere Codes (mpereere@gmail.com)
 * Author URI:      https:://pereere.com
 * Text Domain:     hre-addon
 * Domain Path:     /i18n/languages/
 * Version:         7.0.1
 * Requires at least: 5.3
 * Requires PHP:    7.4
 *
 * @package         HRE_Addon
 *
 * Woo: 12345:342928dfsfhsIEJF9402J2U498JF98J498
 * WC requires at least: 6.2.2
 * WC tested up to: 6.2.2
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/class-initializer.php';
require_once __DIR__ . '/i18n/client-translations.php';

use HRE_Addon\Initializer;

// Test to see if WooCommerce is active (including network activated).
$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

$woo_is_active = in_array( $plugin_path, wp_get_active_and_valid_plugins(), true );

// If WooCommerce is not active, display an admin notice and then return.
if ( ! ( $woo_is_active ) ) {
	add_action(
		'admin_notices',
		static function () {
			// translators: Message to display when WooCommerce is not active.
			$woocommerce_needed = __( '<code>Houzez Real Estate Addon</code> requires <code>WooCommerce</code> to be installed and activated.', 'hre-addon' );
			$esc_woocommerce    = wp_kses(
				$woocommerce_needed,
				array(
					'code' => array(),
				)
			);
			?>
            <div class="notice notice-error is-dismissible">
                <h2>
					<?php
					// translators: Tittle Message to display when WooCommerce is not active.
					echo esc_attr__( 'Beauty and Loyalty Point', 'hre-addon' );
					?>
                </h2>
                <p>
					<?php
					echo wp_kses(
						$woocommerce_needed,
						array(
							'code' => array(),
						)
					);
					?>
                </p>
            </div>
			<?php
		}
	);

	return;

}


$initializer = Initializer::get_instance();
register_activation_hook( __FILE__, array( $initializer, 'on_activate' ) );
register_deactivation_hook( __FILE__, array( $initializer, 'on_deactivate' ) );
register_uninstall_hook( __FILE__, array( Initializer::class, 'on_uninstall' ) );



