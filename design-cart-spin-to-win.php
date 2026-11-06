<?php
/**
 * Plugin Name:       Design Cart Spin To Win
 * Plugin URI:        https://www.designcart.pl
 * Description:       Kołowrotek rabatowy (spin to win) z popupem, odbiorem kodu na e-mail i panelem ustawień.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 7.1
 * Author:            Paweł Nosko
 * Author URI:        https://www.designcart.pl
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       design-cart-spin-to-win
 * Domain Path:       /languages
 * Company:           Design Cart → https://www.designcart.pl/
 * Code:              design-cart-spintowin
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

define( 'DC_STW_VERSION', '1.0.0' );
define( 'DC_STW_FILE', __FILE__ );
define( 'DC_STW_PATH', plugin_dir_path( __FILE__ ) );
define( 'DC_STW_URL', plugin_dir_url( __FILE__ ) );
define( 'DC_STW_BASENAME', plugin_basename( __FILE__ ) );

require_once DC_STW_PATH . 'includes/class-plugin.php';

add_action(
	'before_woocommerce_init',
	static function (): void {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				DC_STW_FILE,
				true
			);
		}
	}
);

add_action(
	'plugins_loaded',
	static function (): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					if ( ! current_user_can( 'activate_plugins' ) ) {
						return;
					}
					echo '<div class="notice notice-error"><p>';
					echo esc_html__(
						'Design Cart Spin To Win wymaga aktywnej wtyczki WooCommerce.',
						'design-cart-spin-to-win'
					);
					echo '</p></div>';
				}
			);
			return;
		}

		DC_STW_Plugin::instance()->init();
	}
);

register_activation_hook( __FILE__, array( 'DC_STW_Plugin', 'activate' ) );

// Tabela leadów także przy update bez re-aktywacji.
add_action(
	'plugins_loaded',
	static function (): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$db_ver = (string) get_option( 'dc_stw_db_version', '' );
		if ( '1' !== $db_ver ) {
			require_once DC_STW_PATH . 'includes/repository/class-email-lead-repository.php';
			DC_STW_Email_Lead_Repository::install();
			update_option( 'dc_stw_db_version', '1', false );
		}
	},
	5
);
