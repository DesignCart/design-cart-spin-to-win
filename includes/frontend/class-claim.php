<?php
/**
 * AJAX — odbiór nagrody (e-mail + kod kuponu).
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Endpoint admin-ajax: dc_stw_claim.
 */
final class DC_STW_Claim {

	/**
	 * Rejestracja hooków.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_dc_stw_claim', array( __CLASS__, 'handle' ) );
		add_action( 'wp_ajax_nopriv_dc_stw_claim', array( __CLASS__, 'handle' ) );
	}

	/**
	 * Obsługa POST claim.
	 */
	public static function handle(): void {
		$general = DC_STW_General_Settings::get();
		if ( '1' !== $general['enabled'] ) {
			self::error( __( 'Moduł jest wyłączony.', 'design-cart-spin-to-win' ) );
		}

		$token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
		if ( ! $token || ! wp_verify_nonce( $token, 'dc_stw_claim' ) ) {
			self::error( __( 'Nieprawidłowy token bezpieczeństwa.', 'design-cart-spin-to-win' ) );
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		if ( ! $email || ! is_email( $email ) ) {
			self::error( __( 'Podaj prawidłowy adres e-mail.', 'design-cart-spin-to-win' ) );
		}

		$content = DC_STW_Content_Settings::get();

		if ( '1' === $content['rodo_enabled'] ) {
			$rodo = isset( $_POST['rodo'] ) ? sanitize_text_field( wp_unslash( $_POST['rodo'] ) ) : '';
			if ( '1' !== $rodo ) {
				self::error(
					$content['rodo_error']
						? $content['rodo_error']
						: __( 'Zaznacz zgodę RODO.', 'design-cart-spin-to-win' )
				);
			}
		}

		$coupon_id = isset( $_POST['id_cart_rule'] ) ? absint( $_POST['id_cart_rule'] ) : 0;
		$selected  = DC_STW_Coupons_Settings::get_selected_ids();

		if ( ! $coupon_id || ! in_array( $coupon_id, $selected, true ) ) {
			self::error( __( 'Nieprawidłowa nagroda.', 'design-cart-spin-to-win' ) );
		}

		$coupon = new WC_Coupon( $coupon_id );
		if ( (int) $coupon->get_id() !== $coupon_id || 'publish' !== get_post_status( $coupon_id ) ) {
			self::error( __( 'Nagroda nie jest już dostępna.', 'design-cart-spin-to-win' ) );
		}

		$code = trim( (string) $coupon->get_code() );
		if ( '' === $code ) {
			self::error( __( 'Wybrany kupon nie ma kodu.', 'design-cart-spin-to-win' ) );
		}

		$prize_amount = DC_STW_Coupons_Settings::format_discount_amount( $coupon );
		$prize_label  = DC_STW_Coupons_Settings::plain_text( (string) $coupon->get_description() );
		if ( '' === $prize_label ) {
			$prize_label = $code;
		}

		$lead_id = DC_STW_Email_Lead_Repository::insert(
			array(
				'coupon_id'    => $coupon_id,
				'email'        => $email,
				'coupon_code'  => $code,
				'prize_label'  => $prize_label,
				'prize_amount' => $prize_amount,
				'user_id'      => get_current_user_id(),
				'ip_address'   => self::client_ip(),
			)
		);

		if ( ! $lead_id ) {
			self::error( __( 'Nie udało się zapisać zgłoszenia.', 'design-cart-spin-to-win' ) );
		}

		$sent = self::send_mail( $email, $code, $prize_amount, $prize_label, $content );
		if ( ! $sent ) {
			self::error(
				$content['claim_error']
					? $content['claim_error']
					: __( 'Nie udało się wysłać e-maila.', 'design-cart-spin-to-win' )
			);
		}

		wp_send_json(
			array(
				'success' => true,
				'message' => $content['claim_success'],
				'code'    => $code,
			)
		);
	}

	/**
	 * @param array<string, string> $content Content settings.
	 */
	private static function send_mail(
		string $email,
		string $code,
		string $amount,
		string $name,
		array $content
	): bool {
		$subject = str_replace(
			array( '{coupon_code}', '{prize_amount}', '{prize_name}' ),
			array( $code, $amount, $name ),
			(string) $content['mail_subject']
		);

		$body = str_replace(
			array( '{coupon_code}', '{prize_amount}', '{prize_name}' ),
			array( $code, $amount, $name ),
			(string) $content['mail_body']
		);

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		return (bool) wp_mail( $email, $subject, $body, $headers );
	}

	private static function client_ip(): string {
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$forwarded = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			$parts     = explode( ',', $forwarded );
			return sanitize_text_field( trim( $parts[0] ) );
		}
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		return '';
	}

	/**
	 * @param string $message Error message.
	 */
	private static function error( string $message ): void {
		wp_send_json(
			array(
				'success' => false,
				'message' => $message,
			)
		);
	}
}
