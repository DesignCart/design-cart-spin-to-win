<?php
/**
 * Markup popupu Spin To Win na froncie.
 *
 * @var array  $content
 * @var string $css
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.
?>
<div class="dc-stw" id="dc-stw" style="<?php echo esc_attr( $css ); ?>" hidden>
	<div class="dc-stw__overlay" id="dc-stw-overlay" role="dialog" aria-modal="true" aria-label="Spin to win" hidden>
		<div class="dc-stw__modal">
			<button class="dc-stw__x" id="dc-stw-close" type="button" aria-label="<?php esc_attr_e( 'Zamknij', 'design-cart-spin-to-win' ); ?>">×</button>
			<h1 class="dc-stw__title"><?php echo esc_html( $content['title'] ); ?></h1>
			<p class="dc-stw__sub"><?php echo esc_html( $content['subtitle'] ); ?></p>
			<div class="dc-stw__stage" id="dc-stw-stage"></div>

			<div class="dc-stw__win" id="dc-stw-win" hidden>
				<div class="dc-stw__card" role="alert">
					<p class="dc-stw__lead"><?php echo esc_html( $content['win_lead'] ); ?></p>
					<p class="dc-stw__big" id="dc-stw-win-amount"></p>
					<p class="dc-stw__name" id="dc-stw-win-name"></p>

					<form class="dc-stw__claim-form" id="dc-stw-claim-form" novalidate>
						<label class="dc-stw__email-label" for="dc-stw-email"><?php echo esc_html( $content['email_label'] ); ?></label>
						<input
							class="dc-stw__email"
							type="email"
							id="dc-stw-email"
							name="email"
							required
							autocomplete="email"
							placeholder="<?php echo esc_attr( $content['email_placeholder'] ); ?>"
						/>
						<?php if ( '1' === $content['rodo_enabled'] && $content['rodo_text'] ) : ?>
							<label class="dc-stw__rodo">
								<input class="dc-stw__rodo-input" type="checkbox" id="dc-stw-rodo" name="rodo" value="1" required />
								<span class="dc-stw__rodo-box" aria-hidden="true"></span>
								<span class="dc-stw__rodo-text"><?php echo esc_html( $content['rodo_text'] ); ?></span>
							</label>
						<?php endif; ?>
						<p class="dc-stw__msg" id="dc-stw-msg" hidden></p>
						<button class="dc-stw__claim" id="dc-stw-claim" type="submit">
							<?php echo esc_html( $content['claim_button'] ); ?>
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
