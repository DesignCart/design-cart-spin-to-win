<?php
/**
 * Sekcja: Adresy e-mail — lista + eksport CSV.
 *
 * @var array $hero
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$leads       = DC_STW_Email_Lead_Repository::find_all( 200 );
$leads_count = DC_STW_Email_Lead_Repository::count_all();

$export_full = wp_nonce_url(
	admin_url( 'admin-post.php?action=dc_stw_export_leads&mode=full' ),
	'dc_stw_export_leads'
);
$export_emails = wp_nonce_url(
	admin_url( 'admin-post.php?action=dc_stw_export_leads&mode=emails' ),
	'dc_stw_export_leads'
);
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-form-card__body">
					<div class="dc-section-card dc-section">
						<div class="dc-section-card__head">
							<span class="dc-section-card__icon"><i class="fa fa-download"></i></span>
							<div>
								<h3 class="dc-section-card__title"><?php esc_html_e( 'Eksport CSV', 'design-cart-spin-to-win' ); ?></h3>
								<p class="dc-section-card__sub"><?php esc_html_e( 'Pobierz zebrane dane — całość albo same adresy e-mail.', 'design-cart-spin-to-win' ); ?></p>
							</div>
						</div>
						<div class="dc-stw-export-actions">
							<a class="dc-btn dc-btn--primary" href="<?php echo esc_url( $export_full ); ?>">
								<i class="fa fa-file-text-o"></i> <?php esc_html_e( 'Eksportuj wszystko', 'design-cart-spin-to-win' ); ?>
							</a>
							<a class="dc-btn dc-btn--secondary" href="<?php echo esc_url( $export_emails ); ?>">
								<i class="fa fa-envelope"></i> <?php esc_html_e( 'Tylko adresy e-mail', 'design-cart-spin-to-win' ); ?>
							</a>
							<span class="dc-stw-export-count">
								<?php esc_html_e( 'Łącznie rekordów:', 'design-cart-spin-to-win' ); ?>
								<strong><?php echo esc_html( (string) $leads_count ); ?></strong>
							</span>
						</div>
					</div>

					<div class="dc-section-card dc-section">
						<div class="dc-section-card__head">
							<span class="dc-section-card__icon"><i class="fa fa-list"></i></span>
							<div>
								<h3 class="dc-section-card__title"><?php esc_html_e( 'Lista zebranych adresów', 'design-cart-spin-to-win' ); ?></h3>
								<p class="dc-section-card__sub"><?php esc_html_e( 'Najnowsze wpisy na górze (max 200 w podglądzie — pełny eksport w CSV).', 'design-cart-spin-to-win' ); ?></p>
							</div>
						</div>

						<?php if ( ! $leads ) : ?>
							<p class="dc-hint"><?php esc_html_e( 'Brak zapisanych adresów. Pojawią się tu, gdy użytkownik wylosuje nagrodę i poda e-mail.', 'design-cart-spin-to-win' ); ?></p>
						<?php else : ?>
							<div class="dc-stw-table-wrap">
								<table class="dc-stw-table">
									<thead>
										<tr>
											<th><?php esc_html_e( 'Data', 'design-cart-spin-to-win' ); ?></th>
											<th><?php esc_html_e( 'E-mail', 'design-cart-spin-to-win' ); ?></th>
											<th><?php esc_html_e( 'Kod', 'design-cart-spin-to-win' ); ?></th>
											<th><?php esc_html_e( 'Nagroda', 'design-cart-spin-to-win' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $leads as $lead ) : ?>
											<tr>
												<td><?php echo esc_html( (string) ( $lead['created_at'] ?? '' ) ); ?></td>
												<td><?php echo esc_html( (string) ( $lead['email'] ?? '' ) ); ?></td>
												<td><code><?php echo esc_html( (string) ( $lead['coupon_code'] ?? '' ) ); ?></code></td>
												<td>
													<?php
													echo esc_html(
														trim(
															(string) ( $lead['prize_amount'] ?? '' ) . ' ' . (string) ( $lead['prize_label'] ?? '' )
														)
													);
													?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
