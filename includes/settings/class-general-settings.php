<?php
/**
 * Ustawienia ogólne (włączenie, strony, częstotliwość, kolory).
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Persystencja opcji ogólnych w wp_options.
 */
final class DC_STW_General_Settings {

	public const OPTION = 'dc_stw_general';

	/**
	 * @return array{
	 *   enabled:string,
	 *   display_pages:string,
	 *   display_frequency:string,
	 *   trigger_classes:string,
	 *   wheel_bg:string,
	 *   claim_bg:string
	 * }
	 */
	public static function defaults(): array {
		return array(
			'enabled'           => '0',
			'display_pages'     => 'home', // home | all
			'display_frequency' => 'once', // once | every_session | every_reload | button_only
			'trigger_classes'   => '.dc-stw-open',
			'wheel_bg'          => '#2a1259',
			'claim_bg'          => '#ffffff',
		);
	}

	/**
	 * @return array{
	 *   enabled:string,
	 *   display_pages:string,
	 *   display_frequency:string,
	 *   trigger_classes:string,
	 *   wheel_bg:string,
	 *   claim_bg:string
	 * }
	 */
	public static function get(): array {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return array_merge( self::defaults(), $stored );
	}

	/**
	 * Zapis z oczyszczeniem danych z formularza.
	 *
	 * @param array<string, mixed> $data Raw input.
	 * @return array<string, string> Zapisane wartości.
	 */
	public static function save( array $data ): array {
		$defaults = self::defaults();

		$pages = isset( $data['display_pages'] ) ? (string) $data['display_pages'] : '';
		$freq  = isset( $data['display_frequency'] ) ? (string) $data['display_frequency'] : '';

		$clean = array(
			'enabled'           => empty( $data['enabled'] ) ? '0' : '1',
			'display_pages'     => in_array( $pages, array( 'home', 'all' ), true )
				? $pages
				: $defaults['display_pages'],
			'display_frequency' => in_array(
				$freq,
				array( 'once', 'every_session', 'every_reload', 'button_only' ),
				true
			)
				? $freq
				: $defaults['display_frequency'],
			'trigger_classes'   => self::sanitize_selectors(
				isset( $data['trigger_classes'] ) ? (string) $data['trigger_classes'] : $defaults['trigger_classes']
			),
			'wheel_bg'          => self::sanitize_color(
				isset( $data['wheel_bg'] ) ? (string) $data['wheel_bg'] : $defaults['wheel_bg'],
				$defaults['wheel_bg']
			),
			'claim_bg'          => self::sanitize_color(
				isset( $data['claim_bg'] ) ? (string) $data['claim_bg'] : $defaults['claim_bg'],
				$defaults['claim_bg']
			),
		);

		update_option( self::OPTION, $clean, false );

		return $clean;
	}

	/**
	 * Domyślne wartości przy instalacji (nie nadpisuje istniejących).
	 */
	public static function ensure_defaults(): void {
		if ( false === get_option( self::OPTION, false ) ) {
			add_option( self::OPTION, self::defaults(), '', false );
		}
	}

	/**
	 * Czy frontend powinien wczytać kołowrotek (logika stron Woo/WP).
	 *
	 * home = strona główna WordPress (`is_front_page`),
	 * all  = każda publiczna podstrona sklepu.
	 */
	public static function should_display_on_current_page(): bool {
		$settings = self::get();

		if ( '1' !== $settings['enabled'] ) {
			return false;
		}

		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		if ( 'home' === $settings['display_pages'] ) {
			return is_front_page();
		}

		return true;
	}

	/**
	 * @param string $selectors Comma-separated CSS selectors.
	 */
	private static function sanitize_selectors( string $selectors ): string {
		$selectors = trim( wp_strip_all_tags( $selectors ) );
		$selectors = preg_replace( '/\s+/', ' ', $selectors ) ?? $selectors;

		if ( '' === $selectors ) {
			return self::defaults()['trigger_classes'];
		}

		return $selectors;
	}

	/**
	 * @param string $color    Candidate.
	 * @param string $fallback Fallback hex.
	 */
	private static function sanitize_color( string $color, string $fallback ): string {
		$color = trim( $color );

		if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $color ) ) {
			return $color;
		}

		return $fallback;
	}
}
