<?php
/**
 * Sekcja: Treści — teksty popupu i maila.
 *
 * @var array $hero
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$settings = DC_STW_Content_Settings::get();
$fields   = DC_STW_Content_Settings::field_meta();
$form_id  = 'dc-stw-form-content';

$hero['form_id'] = $form_id;

$by_key = array();
foreach ( $fields as $field ) {
	$by_key[ $field['key'] ] = $field;
}

$popup_keys = array( 'title', 'subtitle' );
$claim_keys = array( 'win_lead', 'email_label', 'email_placeholder', 'claim_button', 'claim_success', 'claim_error' );
$rodo_keys  = array( 'rodo_text', 'rodo_error' );
$mail_keys  = array( 'mail_subject', 'mail_body' );

/**
 * Render jednego pola treści.
 *
 * @param array{key:string,label:string,hint:string,type:string} $field Field meta.
 * @param array<string,string>                                  $settings Settings.
 * @param int                                                   $rows Textarea rows.
 */
$render_field = static function ( array $field, array $settings, int $rows = 3 ): void {
	$key = $field['key'];
	?>
	<div class="dc-field">
		<label class="dc-label" for="dc_stw_content_<?php echo esc_attr( $key ); ?>">
			<?php echo esc_html( $field['label'] ); ?>
		</label>
		<?php if ( $field['hint'] ) : ?>
			<p class="dc-hint"><?php echo esc_html( $field['hint'] ); ?></p>
		<?php endif; ?>
		<?php if ( 'textarea' === $field['type'] ) : ?>
			<textarea
				class="dc-input dc-textarea"
				id="dc_stw_content_<?php echo esc_attr( $key ); ?>"
				name="dc_stw_content[<?php echo esc_attr( $key ); ?>]"
				rows="<?php echo esc_attr( (string) $rows ); ?>"
			><?php echo esc_textarea( (string) ( $settings[ $key ] ?? '' ) ); ?></textarea>
		<?php else : ?>
			<input
				class="dc-input"
				type="text"
				id="dc_stw_content_<?php echo esc_attr( $key ); ?>"
				name="dc_stw_content[<?php echo esc_attr( $key ); ?>]"
				value="<?php echo esc_attr( (string) ( $settings[ $key ] ?? '' ) ); ?>"
			/>
		<?php endif; ?>
	</div>
	<?php
};
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible dc-stw-notice">
				<p><?php esc_html_e( 'Treści zostały zapisane.', 'design-cart-spin-to-win' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-interface" id="dcStwContentInterface">
					<form
						method="post"
						action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
						class="dc-form dc-form--full"
						id="<?php echo esc_attr( $form_id ); ?>"
					>
						<input type="hidden" name="action" value="dc_stw_save_content" />
						<?php wp_nonce_field( 'dc_stw_save_content' ); ?>

						<nav class="dc-nav dc-tabs" role="tablist">
							<button type="button" class="dc-nav__btn dc-tabs__btn dc-active" data-dc-tab="tab-popup" role="tab" aria-selected="true">
								<i class="fa fa-window-maximize"></i> <?php esc_html_e( 'Popup z kołem', 'design-cart-spin-to-win' ); ?>
							</button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-claim" role="tab">
								<i class="fa fa-gift"></i> <?php esc_html_e( 'Odbiór nagrody', 'design-cart-spin-to-win' ); ?>
							</button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-mail" role="tab">
								<i class="fa fa-envelope"></i> <?php esc_html_e( 'E-mail z kodem', 'design-cart-spin-to-win' ); ?>
							</button>
						</nav>

						<div class="dc-form-card__body">
							<div id="tab-popup" class="dc-tab-panel dc-active" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-header"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Teksty nad kołowrotkiem', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'To widzi klient zaraz po otwarciu popupu.', 'design-cart-spin-to-win' ); ?></p>
										</div>
									</div>
									<?php
									foreach ( $popup_keys as $key ) {
										if ( isset( $by_key[ $key ] ) ) {
											$render_field( $by_key[ $key ], $settings );
										}
									}
									?>
								</div>
							</div>

							<div id="tab-claim" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-gift"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Mały popup po wygranej', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Tu klient wpisuje e-mail i odbiera kod.', 'design-cart-spin-to-win' ); ?></p>
										</div>
									</div>
									<?php
									foreach ( $claim_keys as $key ) {
										if ( isset( $by_key[ $key ] ) ) {
											$render_field( $by_key[ $key ], $settings, 'claim_success' === $key || 'claim_error' === $key ? 3 : 2 );
										}
									}
									?>
								</div>

								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-shield"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'RODO / zgoda na e-mail', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Checkbox pod polem e-mail. Bez zaznaczenia kod nie zostanie wysłany.', 'design-cart-spin-to-win' ); ?></p>
										</div>
									</div>
									<label class="dc-toggle">
										<input
											class="dc-toggle__input"
											type="checkbox"
											name="dc_stw_content[rodo_enabled]"
											value="1"
											<?php checked( $settings['rodo_enabled'], '1' ); ?>
										/>
										<span class="dc-toggle__track"></span>
										<span><?php esc_html_e( 'Pokazuj zgodę RODO pod polem e-mail', 'design-cart-spin-to-win' ); ?></span>
									</label>
									<?php
									foreach ( $rodo_keys as $key ) {
										if ( isset( $by_key[ $key ] ) ) {
											echo '<div style="margin-top:1rem;">';
											$render_field( $by_key[ $key ], $settings, 'rodo_text' === $key ? 4 : 2 );
											echo '</div>';
										}
									}
									?>
								</div>
							</div>

							<div id="tab-mail" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-envelope"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Wiadomość z kodem rabatowym', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'W treści możesz użyć placeholderów: {coupon_code}, {prize_amount}, {prize_name}.', 'design-cart-spin-to-win' ); ?></p>
										</div>
									</div>
									<?php
									foreach ( $mail_keys as $key ) {
										if ( isset( $by_key[ $key ] ) ) {
											$render_field( $by_key[ $key ], $settings, 'mail_body' === $key ? 8 : 2 );
										}
									}
									?>
								</div>
							</div>

							<div class="dc-actions">
								<button type="submit" class="dc-btn dc-btn--primary">
									<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz treści', 'design-cart-spin-to-win' ); ?>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
