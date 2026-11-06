<?php
/**
 * Wybrane kupony WooCommerce na koło Spin To Win.
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Persystencja ID kuponów + pobieranie kafli z WooCommerce.
 */
final class DC_STW_Coupons_Settings {

	public const OPTION = 'dc_stw_coupons';

	/**
	 * @return list<int>
	 */
	public static function get_selected_ids(): array {
		$raw = get_option( self::OPTION, array() );
		if ( ! is_array( $raw ) ) {
			return array();
		}

		return array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
	}

	/**
	 * @param list<int|string> $ids Coupon IDs.
	 * @return list<int>
	 */
	public static function save( array $ids ): array {
		$clean = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
		update_option( self::OPTION, $clean, false );

		return $clean;
	}

	/**
	 * Domyślna opcja przy instalacji.
	 */
	public static function ensure_defaults(): void {
		if ( false === get_option( self::OPTION, false ) ) {
			add_option( self::OPTION, array(), '', false );
		}
	}

	/**
	 * Aktywne (opublikowane) kupony WooCommerce do wyboru w panelu.
	 *
	 * @return list<array{id:int,name:string,code:string,amount:string,selected:bool}>
	 */
	public static function get_available_coupons(): array {
		if ( ! function_exists( 'wc_get_coupons' ) && ! class_exists( 'WC_Coupon' ) ) {
			return array();
		}

		$selected = self::get_selected_ids();
		$out      = array();

		$query = new WP_Query(
			array(
				'post_type'              => 'shop_coupon',
				'post_status'            => 'publish',
				'posts_per_page'         => 200,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
			)
		);

		foreach ( $query->posts as $post ) {
			$coupon = new WC_Coupon( $post->ID );
			$id     = (int) $coupon->get_id();

			if ( $id <= 0 ) {
				continue;
			}

			// Pomijamy wygasłe — nie powinny trafić na koło.
			$expires = $coupon->get_date_expires();
			if ( $expires && $expires->getTimestamp() < time() ) {
				continue;
			}

			$code = (string) $coupon->get_code();
			$name = self::resolve_display_name( $coupon );

			$out[] = array(
				'id'       => $id,
				'name'     => $name,
				'code'     => $code,
				'amount'   => self::format_discount_amount( $coupon ),
				'selected' => in_array( $id, $selected, true ),
			);
		}

		return $out;
	}

	/**
	 * Nagrody na koło — tylko zaznaczone, w kolejności zapisu.
	 *
	 * @return list<array{id:int,name:string,code:string,amount:string}>
	 */
	public static function get_wheel_prizes(): array {
		$selected = self::get_selected_ids();
		if ( ! $selected ) {
			return array();
		}

		$prizes = array();
		foreach ( $selected as $id ) {
			$coupon = new WC_Coupon( $id );
			if ( (int) $coupon->get_id() !== $id ) {
				continue;
			}
			if ( 'publish' !== get_post_status( $id ) ) {
				continue;
			}

			$expires = $coupon->get_date_expires();
			if ( $expires && $expires->getTimestamp() < time() ) {
				continue;
			}

			$prizes[] = array(
				'id'           => $id,
				'id_cart_rule' => $id, // kompatybilność z front JS (PrestaShop)
				'name'         => self::resolve_display_name( $coupon ),
				'code'         => (string) $coupon->get_code(),
				'amount'       => self::format_discount_amount( $coupon ),
			);
		}

		return $prizes;
	}

	/**
	 * Nazwa na kaflek: opis kuponu albo kod.
	 */
	private static function resolve_display_name( WC_Coupon $coupon ): string {
		$desc = self::plain_text( (string) $coupon->get_description() );
		if ( '' !== $desc ) {
			// Krótka linia na kaflek.
			if ( function_exists( 'mb_strlen' ) && mb_strlen( $desc ) > 80 ) {
				return mb_substr( $desc, 0, 77 ) . '…';
			}
			if ( strlen( $desc ) > 80 ) {
				return substr( $desc, 0, 77 ) . '…';
			}

			return $desc;
		}

		return (string) $coupon->get_code();
	}

	/**
	 * Etykieta zniżki na kaflek / segment koła.
	 */
	public static function format_discount_amount( WC_Coupon $coupon ): string {
		$type   = $coupon->get_discount_type();
		$amount = (float) $coupon->get_amount();

		if ( 'percent' === $type && $amount > 0 ) {
			$formatted = rtrim( rtrim( number_format( $amount, 2, '.', '' ), '0' ), '.' );
			return $formatted . '%';
		}

		if ( in_array( $type, array( 'fixed_cart', 'fixed_product' ), true ) && $amount > 0 ) {
			if ( function_exists( 'wc_price' ) ) {
				// wc_price() zwraca HTML z &nbsp; / &#122;&#322; — dekodujemy do zwykłego tekstu.
				return self::plain_text( wc_price( $amount ) );
			}
			$formatted = rtrim( rtrim( number_format( $amount, 2, '.', '' ), '0' ), '.' );
			return $formatted;
		}

		if ( $coupon->get_free_shipping() ) {
			return __( 'FREE SHIP', 'design-cart-spin-to-win' );
		}

		$code = (string) $coupon->get_code();
		return '' !== $code ? $code : '—';
	}

	/**
	 * HTML Woo → czytelny tekst (bez tagów i encji typu &nbsp;).
	 */
	public static function plain_text( string $html ): string {
		$text = wp_strip_all_tags( $html );
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		// NBSP i wąskie spacje → zwykła spacja.
		$text = preg_replace( '/[\x{00A0}\x{202F}\x{2007}]/u', ' ', $text ) ?? $text;

		return trim( preg_replace( '/\s+/u', ' ', $text ) ?? $text );
	}
}
