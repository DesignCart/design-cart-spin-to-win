<?php
/**
 * Assety panelu admina (CSS / JS z szablonu DC Interface).
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue stylów i skryptów tylko na stronie Spin To Win.
 */
final class DC_STW_Admin_Assets {

	/**
	 * Rejestracja hooków.
	 */
	public static function init(): void {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * Czy jesteśmy na stronie wtyczki.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function is_plugin_screen( string $hook ): bool {
		return 'toplevel_page_' . DC_STW_Admin::PAGE_SLUG === $hook;
	}

	/**
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue( string $hook ): void {
		if ( ! self::is_plugin_screen( $hook ) ) {
			return;
		}

		$css = DC_STW_URL . 'admin/css/';
		$js  = DC_STW_URL . 'admin/js/';

		wp_enqueue_style(
			'dc-stw-font-awesome',
			DC_STW_URL . 'admin/assets/font-awesome/css/font-awesome.min.css',
			array(),
			'4.7.0'
		);

		wp_enqueue_style(
			'dc-stw-interface',
			$css . 'dc-interface.css',
			array( 'dc-stw-font-awesome' ),
			DC_STW_VERSION
		);

		wp_enqueue_style(
			'dc-stw-dashboard',
			$css . 'dashboard.css',
			array( 'dc-stw-interface' ),
			DC_STW_VERSION
		);

		wp_enqueue_script(
			'dc-stw-colorpicker',
			$js . 'dc-colorpicker.js',
			array(),
			DC_STW_VERSION,
			true
		);

		wp_enqueue_script(
			'dc-stw-dimension',
			$js . 'dc-dimension.js',
			array(),
			DC_STW_VERSION,
			true
		);

		wp_enqueue_script(
			'dc-stw-interface',
			$js . 'dc-interface.js',
			array( 'dc-stw-colorpicker', 'dc-stw-dimension' ),
			DC_STW_VERSION,
			true
		);

		wp_enqueue_script(
			'dc-stw-admin',
			$js . 'admin.js',
			array( 'dc-stw-interface' ),
			DC_STW_VERSION,
			true
		);
	}
}
