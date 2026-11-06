<?php
/**
 * Frontend — popup Spin To Win.
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue + markup na froncie sklepu.
 */
final class DC_STW_Frontend {

	/**
	 * Rejestracja hooków.
	 */
	public static function init(): void {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render_popup' ), 30 );
	}

	/**
	 * Czy pokazać kołowrotek na bieżącej stronie.
	 */
	public static function should_render(): bool {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		if ( ! DC_STW_General_Settings::should_display_on_current_page() ) {
			return false;
		}

		$prizes = DC_STW_Coupons_Settings::get_wheel_prizes();
		return count( $prizes ) > 0;
	}

	/**
	 * Assety frontowe.
	 */
	public static function enqueue(): void {
		if ( ! self::should_render() ) {
			return;
		}

		$general = DC_STW_General_Settings::get();
		$content = DC_STW_Content_Settings::get();
		$prizes  = DC_STW_Coupons_Settings::get_wheel_prizes();

		wp_enqueue_style(
			'dc-stw-front',
			DC_STW_URL . 'assets/css/spin-to-win.css',
			array(),
			DC_STW_VERSION
		);

		wp_enqueue_script(
			'dc-stw-three',
			DC_STW_URL . 'assets/js/vendor/three.min.js',
			array(),
			'0.128.0',
			true
		);

		wp_enqueue_script(
			'dc-stw-front',
			DC_STW_URL . 'assets/js/spin-to-win.js',
			array( 'dc-stw-three' ),
			DC_STW_VERSION,
			true
		);

		wp_localize_script(
			'dc-stw-front',
			'dcSpinToWin',
			array(
				'prizes'          => $prizes,
				'frequency'       => $general['display_frequency'],
				'triggerClasses'  => $general['trigger_classes'],
				'claimUrl'        => admin_url( 'admin-ajax.php' ),
				'token'           => wp_create_nonce( 'dc_stw_claim' ),
				'ajaxAction'      => 'dc_stw_claim',
				'storageKey'      => 'dc_stw_seen_' . (string) get_current_blog_id(),
				'i18n'            => array(
					'claimSuccess' => $content['claim_success'],
					'claimError'   => $content['claim_error'],
					'rodoError'    => $content['rodo_error'],
				),
			)
		);
	}

	/**
	 * Markup popupu.
	 */
	public static function render_popup(): void {
		if ( ! self::should_render() ) {
			return;
		}

		$content = DC_STW_Content_Settings::get();
		$css     = self::build_css_vars();

		include DC_STW_PATH . 'templates/frontend/popup.php';
	}

	/**
	 * Inline CSS variables (tła + typografia).
	 */
	public static function build_css_vars(): string {
		$general     = DC_STW_General_Settings::get();
		$typography  = DC_STW_Typography_Settings::get();

		$vars = array(
			'--dc-stw-wheel-bg' => (string) $general['wheel_bg'],
			'--dc-stw-claim-bg' => (string) $general['claim_bg'],
		);

		foreach ( $typography as $key => $block ) {
			$vars[ '--dc-stw-' . $key . '-color' ]     = (string) $block['color'];
			$vars[ '--dc-stw-' . $key . '-size' ]      = ( (int) $block['size'] ) . 'px';
			$vars[ '--dc-stw-' . $key . '-weight' ]    = (string) (int) $block['weight'];
			$vars[ '--dc-stw-' . $key . '-transform' ] = ( '1' === (string) $block['uppercase'] ) ? 'uppercase' : 'none';
			if ( ! empty( $block['background'] ) ) {
				$vars[ '--dc-stw-' . $key . '-bg' ] = (string) $block['background'];
			}
		}

		$parts = array();
		foreach ( $vars as $name => $value ) {
			$parts[] = $name . ':' . $value;
		}

		return implode( ';', $parts );
	}
}
