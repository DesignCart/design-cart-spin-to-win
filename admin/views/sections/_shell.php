<?php
/**
 * Wspólna ramka sekcji ustawień (placeholder do czasu formularzy).
 *
 * Oczekuje:
 * - $hero
 * - $section_hint (string) — krótki opis co będzie w tej sekcji
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$section_hint = isset( $section_hint ) ? (string) $section_hint : '';
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-interface">
					<div class="dc-form-card__body">
						<div class="dc-section-card dc-section">
							<div class="dc-section-card__head">
								<span class="dc-section-card__icon">
									<i class="fa <?php echo esc_attr( $hero['icon'] ?? 'fa-cog' ); ?>"></i>
								</span>
								<div>
									<h3 class="dc-section-card__title">
										<?php echo esc_html( $hero['title'] ?? '' ); ?>
									</h3>
									<?php if ( $section_hint ) : ?>
										<p class="dc-section-card__sub"><?php echo esc_html( $section_hint ); ?></p>
									<?php endif; ?>
								</div>
							</div>
							<p class="dc-stw-placeholder">
								<?php
								esc_html_e(
									'Formularz tej sekcji pojawi się w kolejnym kroku — struktura i nawigacja są już gotowe.',
									'design-cart-spin-to-win'
								);
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
