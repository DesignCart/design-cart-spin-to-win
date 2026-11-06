<?php
/**
 * Sekcja: Kupony — kafle z kuponami WooCommerce.
 *
 * @var array $hero
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$coupons = DC_STW_Coupons_Settings::get_available_coupons();
$form_id = 'dc-stw-form-coupons';

$hero['form_id'] = $form_id;

$coupons_url = admin_url( 'edit.php?post_type=shop_coupon' );
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible dc-stw-notice">
				<p><?php esc_html_e( 'Wybór kuponów został zapisany.', 'design-cart-spin-to-win' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-interface">
					<form
						method="post"
						action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
						class="dc-form dc-form--full"
						id="<?php echo esc_attr( $form_id ); ?>"
					>
						<input type="hidden" name="action" value="dc_stw_save_coupons" />
						<?php wp_nonce_field( 'dc_stw_save_coupons' ); ?>

						<div class="dc-form-card__body">
							<div class="dc-section-card dc-section">
								<div class="dc-section-card__head">
									<span class="dc-section-card__icon"><i class="fa fa-ticket"></i></span>
									<div>
										<h3 class="dc-section-card__title">
											<?php esc_html_e( 'Kupony systemowe na kole', 'design-cart-spin-to-win' ); ?>
										</h3>
										<p class="dc-section-card__sub">
											<?php
											esc_html_e(
												'Kliknij kaferek, aby zaznaczyć lub odznaczyć kupon. Podświetlone kupony pojawią się na froncie w Spin to Win.',
												'design-cart-spin-to-win'
											);
											?>
										</p>
									</div>
								</div>

								<?php if ( ! $coupons ) : ?>
									<p class="dc-hint">
										<?php esc_html_e( 'Brak aktywnych kuponów w sklepie.', 'design-cart-spin-to-win' ); ?>
										<a href="<?php echo esc_url( $coupons_url ); ?>">
											<?php esc_html_e( 'Utwórz je w Marketing → Kupony', 'design-cart-spin-to-win' ); ?>
										</a>
										<?php esc_html_e( ', a potem wróć tutaj.', 'design-cart-spin-to-win' ); ?>
									</p>
								<?php else : ?>
									<div class="dc-stw-coupon-grid">
										<?php foreach ( $coupons as $coupon ) : ?>
											<label class="dc-stw-coupon<?php echo ! empty( $coupon['selected'] ) ? ' is-selected' : ''; ?>">
												<input
													type="checkbox"
													name="dc_stw_coupons[]"
													value="<?php echo esc_attr( (string) $coupon['id'] ); ?>"
													class="dc-stw-coupon__input"
													<?php checked( ! empty( $coupon['selected'] ) ); ?>
												/>
												<span class="dc-stw-coupon__amount"><?php echo esc_html( $coupon['amount'] ); ?></span>
												<span class="dc-stw-coupon__name"><?php echo esc_html( $coupon['name'] ); ?></span>
												<?php if ( $coupon['code'] ) : ?>
													<span class="dc-stw-coupon__code"><?php echo esc_html( $coupon['code'] ); ?></span>
												<?php endif; ?>
												<span class="dc-stw-coupon__check" aria-hidden="true"><i class="fa fa-check"></i></span>
											</label>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="dc-actions">
								<button type="submit" class="dc-btn dc-btn--primary">
									<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz wybór kuponów', 'design-cart-spin-to-win' ); ?>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
