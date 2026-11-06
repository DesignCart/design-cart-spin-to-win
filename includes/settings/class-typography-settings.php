<?php
/**
 * Typografia bloków tekstowych (CSS variables na froncie).
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Style tekstów popupu.
 */
final class DC_STW_Typography_Settings {

	public const OPTION = 'dc_stw_typography';

	/**
	 * @return list<string>
	 */
	public static function blocks(): array {
		return array(
			'title',
			'subtitle',
			'win_lead',
			'win_amount',
			'win_name',
			'email_label',
			'claim_button',
		);
	}

	/**
	 * @return list<int>
	 */
	public static function font_weights(): array {
		return array( 400, 500, 600, 700, 800, 900 );
	}

	/**
	 * Meta bloków do formularza.
	 *
	 * @return list<array{key:string,label:string,hint:string,has_background:bool}>
	 */
	public static function block_meta(): array {
		return array(
			array(
				'key'            => 'title',
				'label'          => __( 'Tytuł koła', 'design-cart-spin-to-win' ),
				'hint'           => '',
				'has_background' => false,
			),
			array(
				'key'            => 'subtitle',
				'label'          => __( 'Podtytuł', 'design-cart-spin-to-win' ),
				'hint'           => '',
				'has_background' => false,
			),
			array(
				'key'            => 'win_lead',
				'label'          => __( 'Nagłówek wygranej', 'design-cart-spin-to-win' ),
				'hint'           => '',
				'has_background' => false,
			),
			array(
				'key'            => 'win_amount',
				'label'          => __( 'Wartość rabatu', 'design-cart-spin-to-win' ),
				'hint'           => __( 'Duża liczba / procent na karcie wygranej', 'design-cart-spin-to-win' ),
				'has_background' => false,
			),
			array(
				'key'            => 'win_name',
				'label'          => __( 'Nazwa nagrody', 'design-cart-spin-to-win' ),
				'hint'           => '',
				'has_background' => false,
			),
			array(
				'key'            => 'email_label',
				'label'          => __( 'Etykieta e-mail', 'design-cart-spin-to-win' ),
				'hint'           => '',
				'has_background' => false,
			),
			array(
				'key'            => 'claim_button',
				'label'          => __( 'Przycisk odbioru', 'design-cart-spin-to-win' ),
				'hint'           => __( 'Kolor tekstu i tła przycisku', 'design-cart-spin-to-win' ),
				'has_background' => true,
			),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function default_block( string $key ): array {
		$map = array(
			'title'        => array(
				'color'      => '#ffd54a',
				'size'       => 42,
				'weight'     => 900,
				'uppercase'  => '1',
				'background' => '',
			),
			'subtitle'     => array(
				'color'      => '#e6dcff',
				'size'       => 16,
				'weight'     => 400,
				'uppercase'  => '0',
				'background' => '',
			),
			'win_lead'     => array(
				'color'      => '#2a1b4d',
				'size'       => 18,
				'weight'     => 700,
				'uppercase'  => '0',
				'background' => '',
			),
			'win_amount'   => array(
				'color'      => '#6d28d9',
				'size'       => 48,
				'weight'     => 900,
				'uppercase'  => '0',
				'background' => '',
			),
			'win_name'     => array(
				'color'      => '#5b4d7d',
				'size'       => 15,
				'weight'     => 500,
				'uppercase'  => '0',
				'background' => '',
			),
			'email_label'  => array(
				'color'      => '#2a1b4d',
				'size'       => 14,
				'weight'     => 600,
				'uppercase'  => '0',
				'background' => '',
			),
			'claim_button' => array(
				'color'      => '#2a1b4d',
				'size'       => 16,
				'weight'     => 800,
				'uppercase'  => '0',
				'background' => '#ffb703',
			),
		);

		return $map[ $key ] ?? array(
			'color'      => '#2a1b4d',
			'size'       => 16,
			'weight'     => 400,
			'uppercase'  => '0',
			'background' => '',
		);
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public static function defaults(): array {
		$out = array();
		foreach ( self::blocks() as $key ) {
			$out[ $key ] = self::default_block( $key );
		}
		return $out;
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public static function get(): array {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$out = self::defaults();
		foreach ( $out as $key => $block ) {
			if ( isset( $stored[ $key ] ) && is_array( $stored[ $key ] ) ) {
				$out[ $key ] = self::sanitize_block( $stored[ $key ], $block );
			}
		}

		return $out;
	}

	/**
	 * @param array<string, mixed> $posted Raw POST (blocks keyed).
	 * @return array<string, array<string, mixed>>
	 */
	public static function save( array $posted ): array {
		$clean = array();
		foreach ( self::blocks() as $key ) {
			$defaults = self::default_block( $key );
			$block    = isset( $posted[ $key ] ) && is_array( $posted[ $key ] ) ? $posted[ $key ] : array();
			$clean[ $key ] = self::sanitize_block( $block, $defaults );
		}

		update_option( self::OPTION, $clean, false );

		return $clean;
	}

	public static function ensure_defaults(): void {
		if ( false === get_option( self::OPTION, false ) ) {
			add_option( self::OPTION, self::defaults(), '', false );
		}
	}

	/**
	 * @param array<string, mixed> $block    Raw.
	 * @param array<string, mixed> $defaults Defaults.
	 * @return array{color:string,size:int,weight:int,uppercase:string,background:string}
	 */
	private static function sanitize_block( array $block, array $defaults ): array {
		$color = trim( (string) ( $block['color'] ?? $defaults['color'] ) );
		if ( ! preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $color ) ) {
			$color = (string) $defaults['color'];
		}

		$bg = trim( (string) ( $block['background'] ?? $defaults['background'] ) );
		if ( '' !== $bg && ! preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $bg ) ) {
			$bg = (string) $defaults['background'];
		}

		$weight = (int) ( $block['weight'] ?? $defaults['weight'] );
		if ( ! in_array( $weight, self::font_weights(), true ) ) {
			$weight = (int) $defaults['weight'];
		}

		return array(
			'color'      => $color,
			'size'       => max( 10, min( 96, (int) ( $block['size'] ?? $defaults['size'] ) ) ),
			'weight'     => $weight,
			'uppercase'  => empty( $block['uppercase'] ) ? '0' : '1',
			'background' => $bg,
		);
	}
}
