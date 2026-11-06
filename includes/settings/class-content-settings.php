<?php
/**
 * Treści popupu / maila.
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Teksty widoczne użytkownikowi.
 */
final class DC_STW_Content_Settings {

	public const OPTION = 'dc_stw_content';

	/**
	 * @return list<string>
	 */
	public static function fields(): array {
		return array(
			'title',
			'subtitle',
			'win_lead',
			'email_label',
			'email_placeholder',
			'rodo_text',
			'rodo_error',
			'claim_button',
			'claim_success',
			'claim_error',
			'mail_subject',
			'mail_body',
		);
	}

	/**
	 * Meta pól do formularza admina.
	 *
	 * @return list<array{key:string,label:string,hint:string,type:string}>
	 */
	public static function field_meta(): array {
		return array(
			array(
				'key'   => 'title',
				'label' => __( 'Tytuł koła', 'design-cart-spin-to-win' ),
				'hint'  => __( 'Duży nagłówek nad kołowrotkiem', 'design-cart-spin-to-win' ),
				'type'  => 'text',
			),
			array(
				'key'   => 'subtitle',
				'label' => __( 'Podtytuł', 'design-cart-spin-to-win' ),
				'hint'  => __( 'Krótka instrukcja pod tytułem', 'design-cart-spin-to-win' ),
				'type'  => 'text',
			),
			array(
				'key'   => 'win_lead',
				'label' => __( 'Nagłówek wygranej', 'design-cart-spin-to-win' ),
				'hint'  => __( 'Tekst w małym popupie po wylosowaniu', 'design-cart-spin-to-win' ),
				'type'  => 'text',
			),
			array(
				'key'   => 'email_label',
				'label' => __( 'Etykieta pola e-mail', 'design-cart-spin-to-win' ),
				'hint'  => '',
				'type'  => 'text',
			),
			array(
				'key'   => 'email_placeholder',
				'label' => __( 'Placeholder e-mail', 'design-cart-spin-to-win' ),
				'hint'  => '',
				'type'  => 'text',
			),
			array(
				'key'   => 'rodo_text',
				'label' => __( 'Tekst zgody RODO', 'design-cart-spin-to-win' ),
				'hint'  => __( 'Wyświetlany pod polem e-mail obok checkboxa', 'design-cart-spin-to-win' ),
				'type'  => 'textarea',
			),
			array(
				'key'   => 'rodo_error',
				'label' => __( 'Komunikat gdy RODO niezaznaczone', 'design-cart-spin-to-win' ),
				'hint'  => '',
				'type'  => 'text',
			),
			array(
				'key'   => 'claim_button',
				'label' => __( 'Przycisk odbioru', 'design-cart-spin-to-win' ),
				'hint'  => __( 'Przycisk wysyłający kod na maila', 'design-cart-spin-to-win' ),
				'type'  => 'text',
			),
			array(
				'key'   => 'claim_success',
				'label' => __( 'Komunikat sukcesu', 'design-cart-spin-to-win' ),
				'hint'  => '',
				'type'  => 'textarea',
			),
			array(
				'key'   => 'claim_error',
				'label' => __( 'Komunikat błędu', 'design-cart-spin-to-win' ),
				'hint'  => '',
				'type'  => 'textarea',
			),
			array(
				'key'   => 'mail_subject',
				'label' => __( 'Temat e-maila', 'design-cart-spin-to-win' ),
				'hint'  => '',
				'type'  => 'text',
			),
			array(
				'key'   => 'mail_body',
				'label' => __( 'Treść e-maila', 'design-cart-spin-to-win' ),
				'hint'  => __( 'Placeholdery: {coupon_code}, {prize_amount}, {prize_name}', 'design-cart-spin-to-win' ),
				'type'  => 'textarea',
			),
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function defaults(): array {
		$locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
		$is_pl  = 0 === strpos( strtolower( (string) $locale ), 'pl' );

		if ( $is_pl ) {
			$text = array(
				'title'             => 'SPIN TO WIN',
				'subtitle'          => 'Chwyć koło i zakręć, aby wygrać rabat.',
				'win_lead'          => 'Gratulacje! Wygrałeś:',
				'email_label'       => 'Twój adres e-mail',
				'email_placeholder' => 'jan@example.com',
				'rodo_text'         => 'Wyrażam zgodę na przetwarzanie mojego adresu e-mail w celu przesłania kodu rabatowego zgodnie z polityką prywatności sklepu.',
				'rodo_error'        => 'Aby otrzymać kod, zaznacz zgodę RODO.',
				'claim_button'      => 'Wyślij kod na e-mail',
				'claim_success'     => 'Kod rabatowy został wysłany na podany adres e-mail.',
				'claim_error'       => 'Nie udało się wysłać kodu. Sprawdź adres e-mail i spróbuj ponownie.',
				'mail_subject'      => 'Twój kod rabatowy ze Spin to Win',
				'mail_body'         => "Cześć!\n\nTwój kod rabatowy: {coupon_code}\nNagroda: {prize_amount} {prize_name}\n\nMiłych zakupów!",
			);
		} else {
			$text = array(
				'title'             => 'SPIN TO WIN',
				'subtitle'          => 'Grab the wheel and spin to win a discount.',
				'win_lead'          => 'Congratulations! You won:',
				'email_label'       => 'Your email address',
				'email_placeholder' => 'john@example.com',
				'rodo_text'         => 'I agree to the processing of my email address in order to receive the discount code, in accordance with the store privacy policy.',
				'rodo_error'        => 'Please accept the privacy consent to receive the code.',
				'claim_button'      => 'Send code to email',
				'claim_success'     => 'The discount code has been sent to your email.',
				'claim_error'       => 'Could not send the code. Check the email address and try again.',
				'mail_subject'      => 'Your Spin to Win discount code',
				'mail_body'         => "Hi!\n\nYour discount code: {coupon_code}\nPrize: {prize_amount} {prize_name}\n\nHappy shopping!",
			);
		}

		return array_merge(
			array( 'rodo_enabled' => '1' ),
			$text
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function get(): array {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return array_merge( self::defaults(), array_map( 'strval', $stored ) );
	}

	/**
	 * @param array<string, mixed> $data Raw POST.
	 * @return array<string, string>
	 */
	public static function save( array $data ): array {
		$defaults = self::defaults();
		$clean    = array(
			'rodo_enabled' => empty( $data['rodo_enabled'] ) ? '0' : '1',
		);

		foreach ( self::fields() as $field ) {
			$raw = isset( $data[ $field ] ) ? (string) $data[ $field ] : $defaults[ $field ];
			$clean[ $field ] = sanitize_textarea_field( $raw );
		}

		update_option( self::OPTION, $clean, false );

		return $clean;
	}

	public static function ensure_defaults(): void {
		if ( false === get_option( self::OPTION, false ) ) {
			add_option( self::OPTION, self::defaults(), '', false );
		}
	}
}
