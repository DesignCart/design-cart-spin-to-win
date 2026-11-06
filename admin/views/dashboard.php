<?php
/**
 * Dashboard — kafle do sekcji ustawień.
 *
 * @var array $hero
 * @var list<array{key:string,title:string,desc:string,icon:string,url:string}> $tiles
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$tiles = isset( $tiles ) && is_array( $tiles ) ? $tiles : array();
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-form-card__body">
					<div class="dc-section-card dc-section dc-stw-dash-intro">
						<div class="dc-section-card__head">
							<span class="dc-section-card__icon"><i class="fa fa-th-large"></i></span>
							<div>
								<h3 class="dc-section-card__title">
									<?php esc_html_e( 'Panel ustawień', 'design-cart-spin-to-win' ); ?>
								</h3>
								<p class="dc-section-card__sub">
									<?php
									esc_html_e(
										'Wybierz kaferek, aby przejść do konkretnej grupy ustawień. Wszystko podzielone jest tak, by było czytelne nawet bez wiedzy technicznej.',
										'design-cart-spin-to-win'
									);
									?>
								</p>
							</div>
						</div>
					</div>

					<div
						class="dc-stw-tiles"
						role="navigation"
						aria-label="<?php esc_attr_e( 'Sekcje ustawień', 'design-cart-spin-to-win' ); ?>"
					>
						<?php foreach ( $tiles as $tile ) : ?>
							<a class="dc-stw-tile" href="<?php echo esc_url( $tile['url'] ); ?>">
								<span class="dc-stw-tile__icon">
									<i class="fa <?php echo esc_attr( $tile['icon'] ); ?>"></i>
								</span>
								<span class="dc-stw-tile__body">
									<span class="dc-stw-tile__title"><?php echo esc_html( $tile['title'] ); ?></span>
									<span class="dc-stw-tile__desc"><?php echo esc_html( $tile['desc'] ); ?></span>
								</span>
								<span class="dc-stw-tile__arrow" aria-hidden="true">
									<i class="fa fa-angle-right"></i>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
