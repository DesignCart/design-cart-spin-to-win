<?php
/**
 * Sekcja: Typografia.
 *
 * @var array $hero
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$typography = DC_STW_Typography_Settings::get();
$blocks     = DC_STW_Typography_Settings::block_meta();
$form_id    = 'dc-stw-form-typo';

$hero['form_id'] = $form_id;
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible dc-stw-notice">
				<p><?php esc_html_e( 'Typografia została zapisana.', 'design-cart-spin-to-win' ); ?></p>
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
						<input type="hidden" name="action" value="dc_stw_save_typography" />
						<?php wp_nonce_field( 'dc_stw_save_typography' ); ?>

						<nav class="dc-nav dc-tabs" role="tablist">
							<button type="button" class="dc-nav__btn dc-tabs__btn dc-active" data-dc-tab="tab-typo-wheel" role="tab" aria-selected="true">
								<i class="fa fa-circle-o"></i> <?php esc_html_e( 'Kołowrotek', 'design-cart-spin-to-win' ); ?>
							</button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-typo-claim" role="tab">
								<i class="fa fa-gift"></i> <?php esc_html_e( 'Karta wygranej', 'design-cart-spin-to-win' ); ?>
							</button>
						</nav>

						<div class="dc-form-card__body">
							<div id="tab-typo-wheel" class="dc-tab-panel dc-active" role="tabpanel">
								<?php foreach ( $blocks as $block ) : ?>
									<?php if ( ! in_array( $block['key'], array( 'title', 'subtitle' ), true ) ) { continue; } ?>
									<div class="dc-section-card dc-section">
										<div class="dc-section-card__head">
											<span class="dc-section-card__icon"><i class="fa fa-font"></i></span>
											<div>
												<h3 class="dc-section-card__title"><?php echo esc_html( $block['label'] ); ?></h3>
												<?php if ( $block['hint'] ) : ?>
													<p class="dc-section-card__sub"><?php echo esc_html( $block['hint'] ); ?></p>
												<?php endif; ?>
											</div>
										</div>
										<?php
										$name_prefix    = 'dc_stw_typo[' . $block['key'] . ']';
										$data           = $typography[ $block['key'] ] ?? array();
										$has_background = ! empty( $block['has_background'] );
										include DC_STW_PATH . 'admin/views/_partials/typo-block.php';
										?>
									</div>
								<?php endforeach; ?>
							</div>

							<div id="tab-typo-claim" class="dc-tab-panel" role="tabpanel">
								<?php foreach ( $blocks as $block ) : ?>
									<?php if ( in_array( $block['key'], array( 'title', 'subtitle' ), true ) ) { continue; } ?>
									<div class="dc-section-card dc-section">
										<div class="dc-section-card__head">
											<span class="dc-section-card__icon"><i class="fa fa-font"></i></span>
											<div>
												<h3 class="dc-section-card__title"><?php echo esc_html( $block['label'] ); ?></h3>
												<?php if ( $block['hint'] ) : ?>
													<p class="dc-section-card__sub"><?php echo esc_html( $block['hint'] ); ?></p>
												<?php endif; ?>
											</div>
										</div>
										<?php
										$name_prefix    = 'dc_stw_typo[' . $block['key'] . ']';
										$data           = $typography[ $block['key'] ] ?? array();
										$has_background = ! empty( $block['has_background'] );
										include DC_STW_PATH . 'admin/views/_partials/typo-block.php';
										?>
									</div>
								<?php endforeach; ?>
							</div>

							<div class="dc-actions">
								<button type="submit" class="dc-btn dc-btn--primary">
									<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz typografię', 'design-cart-spin-to-win' ); ?>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
