<?php
/**
 * Bootstrap wtyczki.
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Główna klasa pluginu.
 */
final class DC_STW_Plugin {

	/**
	 * Dozwolone sekcje panelu admina.
	 *
	 * @var list<string>
	 */
	public const SECTIONS = array(
		'dashboard',
		'general',
		'coupons',
		'emails',
		'content',
		'typography',
	);

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Ładowanie klas ustawień / repo.
	 */
	private function load_core(): void {
		require_once DC_STW_PATH . 'includes/settings/class-general-settings.php';
		require_once DC_STW_PATH . 'includes/settings/class-coupons-settings.php';
		require_once DC_STW_PATH . 'includes/settings/class-content-settings.php';
		require_once DC_STW_PATH . 'includes/settings/class-typography-settings.php';
		require_once DC_STW_PATH . 'includes/repository/class-email-lead-repository.php';
	}

	/**
	 * Rejestracja hooków.
	 */
	public function init(): void {
		// Local / off-.org installs: load translations from /languages when present.
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound
		load_plugin_textdomain(
			'design-cart-spin-to-win',
			false,
			dirname( DC_STW_BASENAME ) . '/languages'
		);

		$this->load_core();

		DC_STW_General_Settings::ensure_defaults();
		DC_STW_Coupons_Settings::ensure_defaults();
		DC_STW_Content_Settings::ensure_defaults();
		DC_STW_Typography_Settings::ensure_defaults();

		require_once DC_STW_PATH . 'includes/frontend/class-frontend.php';
		require_once DC_STW_PATH . 'includes/frontend/class-claim.php';
		DC_STW_Frontend::init();
		DC_STW_Claim::init();

		if ( is_admin() ) {
			require_once DC_STW_PATH . 'includes/admin/class-admin.php';
			require_once DC_STW_PATH . 'includes/admin/class-admin-assets.php';

			DC_STW_Admin::init();
			DC_STW_Admin_Assets::init();
		}

		add_filter(
			'plugin_action_links_' . DC_STW_BASENAME,
			array( $this, 'action_links' )
		);
	}

	/**
	 * Link „Ustawienia” na liście wtyczek.
	 *
	 * @param array<string, string> $links Links.
	 * @return array<string, string>
	 */
	public function action_links( array $links ): array {
		$url = admin_url( 'admin.php?page=dc-stw&section=dashboard' );

		$links = array_merge(
			array(
				'settings' => sprintf(
					'<a href="%s">%s</a>',
					esc_url( $url ),
					esc_html__( 'Ustawienia', 'design-cart-spin-to-win' )
				),
			),
			$links
		);

		return $links;
	}

	/**
	 * Aktywacja: opcje + tabela leadów.
	 */
	public static function activate(): void {
		require_once DC_STW_PATH . 'includes/settings/class-general-settings.php';
		require_once DC_STW_PATH . 'includes/settings/class-coupons-settings.php';
		require_once DC_STW_PATH . 'includes/settings/class-content-settings.php';
		require_once DC_STW_PATH . 'includes/settings/class-typography-settings.php';
		require_once DC_STW_PATH . 'includes/repository/class-email-lead-repository.php';

		DC_STW_General_Settings::ensure_defaults();
		DC_STW_Coupons_Settings::ensure_defaults();
		DC_STW_Content_Settings::ensure_defaults();
		DC_STW_Typography_Settings::ensure_defaults();
		DC_STW_Email_Lead_Repository::install();

		update_option( 'dc_stw_installed', DC_STW_VERSION, false );
		update_option( 'dc_stw_db_version', '1', false );
	}
}
