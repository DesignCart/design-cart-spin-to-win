<?php
/**
 * Panel admina — menu, routing sekcji, render widoków.
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin UI controller.
 */
final class DC_STW_Admin {

	public const PAGE_SLUG = 'dc-stw';

	/**
	 * Rejestracja hooków.
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_post_dc_stw_save_general', array( __CLASS__, 'save_general' ) );
		add_action( 'admin_post_dc_stw_save_coupons', array( __CLASS__, 'save_coupons' ) );
		add_action( 'admin_post_dc_stw_save_content', array( __CLASS__, 'save_content' ) );
		add_action( 'admin_post_dc_stw_save_typography', array( __CLASS__, 'save_typography' ) );
		add_action( 'admin_post_dc_stw_export_leads', array( __CLASS__, 'export_leads' ) );
	}

	/**
	 * Menu w panelu WooCommerce / top-level.
	 */
	public static function register_menu(): void {
		add_menu_page(
			__( 'Design Cart Spin To Win', 'design-cart-spin-to-win' ),
			__( 'Spin To Win', 'design-cart-spin-to-win' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( __CLASS__, 'render' ),
			'dashicons-awards',
			57
		);

		add_submenu_page(
			self::PAGE_SLUG,
			__( 'Dashboard', 'design-cart-spin-to-win' ),
			__( 'Dashboard', 'design-cart-spin-to-win' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * URL do sekcji panelu.
	 *
	 * @param string $section Section key.
	 */
	public static function section_url( string $section = 'dashboard' ): string {
		if ( ! in_array( $section, DC_STW_Plugin::SECTIONS, true ) ) {
			$section = 'dashboard';
		}

		return add_query_arg(
			array(
				'page'    => self::PAGE_SLUG,
				'section' => $section,
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Aktualna sekcja z requestu.
	 */
	public static function current_section(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Routing tylko do odczytu.
		$section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'dashboard';

		if ( ! in_array( $section, DC_STW_Plugin::SECTIONS, true ) ) {
			return 'dashboard';
		}

		return $section;
	}

	/**
	 * Meta kafli dashboardu.
	 *
	 * @return list<array{key:string,title:string,desc:string,icon:string,url:string}>
	 */
	public static function dashboard_tiles(): array {
		return array(
			array(
				'key'   => 'general',
				'title' => __( 'Ogólne', 'design-cart-spin-to-win' ),
				'desc'  => __( 'Włączenie, strony, częstotliwość, kolory popupów', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-cog',
				'url'   => self::section_url( 'general' ),
			),
			array(
				'key'   => 'coupons',
				'title' => __( 'Kupony', 'design-cart-spin-to-win' ),
				'desc'  => __( 'Wybierz systemowe kupony widoczne na kole', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-ticket',
				'url'   => self::section_url( 'coupons' ),
			),
			array(
				'key'   => 'emails',
				'title' => __( 'Adresy e-mail', 'design-cart-spin-to-win' ),
				'desc'  => __( 'Zebrane maile i kody, eksport CSV', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-envelope-o',
				'url'   => self::section_url( 'emails' ),
			),
			array(
				'key'   => 'content',
				'title' => __( 'Treści', 'design-cart-spin-to-win' ),
				'desc'  => __( 'Teksty popupu i maila w wielu językach', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-font',
				'url'   => self::section_url( 'content' ),
			),
			array(
				'key'   => 'typography',
				'title' => __( 'Typografia', 'design-cart-spin-to-win' ),
				'desc'  => __( 'Kolor, rozmiar, grubość każdego bloku tekstu', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-paint-brush',
				'url'   => self::section_url( 'typography' ),
			),
		);
	}

	/**
	 * Meta sekcji (tytuł + ikona hero).
	 *
	 * @return array{title:string,icon:string}
	 */
	public static function section_meta( string $section ): array {
		$map = array(
			'dashboard'  => array(
				'title' => 'SpinToWin',
				'icon'  => 'fa-diamond',
			),
			'general'    => array(
				'title' => __( 'Ogólne', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-cog',
			),
			'coupons'    => array(
				'title' => __( 'Kupony', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-ticket',
			),
			'emails'     => array(
				'title' => __( 'Adresy e-mail', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-envelope-o',
			),
			'content'    => array(
				'title' => __( 'Treści', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-font',
			),
			'typography' => array(
				'title' => __( 'Typografia', 'design-cart-spin-to-win' ),
				'icon'  => 'fa-paint-brush',
			),
		);

		return $map[ $section ] ?? $map['dashboard'];
	}

	/**
	 * Form ID powiązane z przyciskiem Zapisz w hero.
	 *
	 * @return array<string, string>
	 */
	private static function section_form_ids(): array {
		return array(
			'general'    => 'dc-stw-form-general',
			'coupons'    => 'dc-stw-form-coupons',
			'content'    => 'dc-stw-form-content',
			'typography' => 'dc-stw-form-typo',
		);
	}

	/**
	 * Redirect po zapisie sekcji.
	 */
	private static function redirect_section( string $section ): void {
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => self::PAGE_SLUG,
					'section' => $section,
					'updated' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Zapis sekcji Ogólne.
	 */
	public static function save_general(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-spin-to-win' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'dc_stw_save_general' );

		$raw = array();
		if ( isset( $_POST['dc_stw_general'] ) && is_array( $_POST['dc_stw_general'] ) ) {
			$raw = map_deep( wp_unslash( $_POST['dc_stw_general'] ), 'sanitize_text_field' );
		}

		DC_STW_General_Settings::save( $raw );
		self::redirect_section( 'general' );
	}

	/**
	 * Zapis sekcji Kupony.
	 */
	public static function save_coupons(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-spin-to-win' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'dc_stw_save_coupons' );

		$raw = array();
		if ( isset( $_POST['dc_stw_coupons'] ) && is_array( $_POST['dc_stw_coupons'] ) ) {
			$raw = array_map( 'absint', wp_unslash( $_POST['dc_stw_coupons'] ) );
		}

		DC_STW_Coupons_Settings::save( $raw );
		self::redirect_section( 'coupons' );
	}

	/**
	 * Zapis sekcji Treści.
	 */
	public static function save_content(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-spin-to-win' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'dc_stw_save_content' );

		$raw = array();
		if ( isset( $_POST['dc_stw_content'] ) && is_array( $_POST['dc_stw_content'] ) ) {
			$raw = map_deep( wp_unslash( $_POST['dc_stw_content'] ), 'sanitize_textarea_field' );
		}

		DC_STW_Content_Settings::save( $raw );
		self::redirect_section( 'content' );
	}

	/**
	 * Zapis sekcji Typografia.
	 */
	public static function save_typography(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-spin-to-win' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'dc_stw_save_typography' );

		$raw = array();
		if ( isset( $_POST['dc_stw_typo'] ) && is_array( $_POST['dc_stw_typo'] ) ) {
			$raw = map_deep( wp_unslash( $_POST['dc_stw_typo'] ), 'sanitize_text_field' );
		}

		DC_STW_Typography_Settings::save( $raw );
		self::redirect_section( 'typography' );
	}

	/**
	 * Jedna linia CSV (separator ;).
	 *
	 * @param list<string|int|float> $fields Fields.
	 */
	private static function csv_line( array $fields ): string {
		$escaped = array();
		foreach ( $fields as $field ) {
			$value       = (string) $field;
			$value       = str_replace( '"', '""', $value );
			$escaped[]   = '"' . $value . '"';
		}
		return implode( ';', $escaped ) . "\r\n";
	}

	/**
	 * Eksport leadów CSV.
	 */
	public static function export_leads(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-spin-to-win' ), '', array( 'response' => 403 ) );
		}

		check_admin_referer( 'dc_stw_export_leads' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verified above.
		$mode        = isset( $_GET['mode'] ) ? sanitize_key( wp_unslash( $_GET['mode'] ) ) : 'full';
		$emails_only = ( 'emails' === $mode );
		$rows        = DC_STW_Email_Lead_Repository::find_all( 100000 );

		$filename = 'dc-spintowin-' . ( $emails_only ? 'emails' : 'leads' ) . '-' . gmdate( 'Ymd-His' ) . '.csv';
		$csv      = '';

		if ( $emails_only ) {
			$csv .= self::csv_line( array( 'email' ) );
			foreach ( $rows as $row ) {
				$csv .= self::csv_line( array( (string) ( $row['email'] ?? '' ) ) );
			}
		} else {
			$csv .= self::csv_line(
				array( 'id', 'email', 'coupon_code', 'prize_label', 'prize_amount', 'coupon_id', 'created_at', 'ip_address' )
			);
			foreach ( $rows as $row ) {
				$csv .= self::csv_line(
					array(
						(string) ( $row['id'] ?? '' ),
						(string) ( $row['email'] ?? '' ),
						(string) ( $row['coupon_code'] ?? '' ),
						(string) ( $row['prize_label'] ?? '' ),
						(string) ( $row['prize_amount'] ?? '' ),
						(string) ( $row['coupon_id'] ?? '' ),
						(string) ( $row['created_at'] ?? '' ),
						(string) ( $row['ip_address'] ?? '' ),
					)
				);
			}
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		// BOM dla Excela + treść (bez fwrite/fclose — Plugin Check).
		echo "\xEF\xBB\xBF" . $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV download body.
		exit;
	}

	/**
	 * Render strony admina.
	 */
	public static function render(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-spin-to-win' ) );
		}

		$section = self::current_section();
		$meta    = self::section_meta( $section );
		$forms   = self::section_form_ids();

		$hero = array(
			'title'          => $meta['title'],
			'icon'           => $meta['icon'],
			'crumb'          => 'dashboard' === $section ? '' : $meta['title'],
			'form_id'        => $forms[ $section ] ?? '',
			'show_dashboard' => 'dashboard' !== $section,
		);

		if ( 'dashboard' === $section ) {
			$tiles = self::dashboard_tiles();
			include DC_STW_PATH . 'admin/views/dashboard.php';
			return;
		}

		$view = DC_STW_PATH . 'admin/views/sections/' . $section . '.php';
		if ( ! is_readable( $view ) ) {
			$tiles = self::dashboard_tiles();
			include DC_STW_PATH . 'admin/views/dashboard.php';
			return;
		}

		include $view;
	}
}
