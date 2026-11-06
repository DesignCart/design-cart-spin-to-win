<?php
/**
 * Sekcja: Ogólne — status, strony, częstotliwość, trigger, kolory.
 *
 * @var array $hero
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$settings = DC_STW_General_Settings::get();
$form_id  = 'dc-stw-form-general';

$hero['form_id'] = $form_id;
?>
<div class="wrap dc-stw-admin-wrap">
	<div class="dc-page dc-stw-bo">
		<?php include DC_STW_PATH . 'admin/views/_partials/hero.php'; ?>

		<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible dc-stw-notice">
				<p><?php esc_html_e( 'Ustawienia ogólne zostały zapisane.', 'design-cart-spin-to-win' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-interface" id="dcStwInterface">
					<form
						method="post"
						action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
						class="dc-form dc-form--full"
						id="<?php echo esc_attr( $form_id ); ?>"
					>
						<input type="hidden" name="action" value="dc_stw_save_general" />
						<?php wp_nonce_field( 'dc_stw_save_general' ); ?>

						<nav class="dc-nav dc-tabs" role="tablist">
							<button type="button" class="dc-nav__btn dc-tabs__btn dc-active" data-dc-tab="tab-status" role="tab" aria-selected="true">
								<i class="fa fa-power-off"></i> <?php esc_html_e( 'Status i wyświetlanie', 'design-cart-spin-to-win' ); ?>
							</button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-trigger" role="tab">
								<i class="fa fa-hand-pointer-o"></i> <?php esc_html_e( 'Przycisk / klasy CSS', 'design-cart-spin-to-win' ); ?>
							</button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-colors" role="tab">
								<i class="fa fa-paint-brush"></i> <?php esc_html_e( 'Kolory popupów', 'design-cart-spin-to-win' ); ?>
							</button>
						</nav>

						<div class="dc-form-card__body">

							<div id="tab-status" class="dc-tab-panel dc-active" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-power-off"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Włączenie modułu', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Gdy wyłączone — kołowrotek nie pojawia się na sklepie.', 'design-cart-spin-to-win' ); ?></p>
										</div>
									</div>
									<label class="dc-toggle">
										<input
											class="dc-toggle__input"
											type="checkbox"
											name="dc_stw_general[enabled]"
											value="1"
											<?php checked( $settings['enabled'], '1' ); ?>
										/>
										<span class="dc-toggle__track"></span>
										<span><?php esc_html_e( 'Moduł włączony na froncie', 'design-cart-spin-to-win' ); ?></span>
									</label>
								</div>

								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-desktop"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Na których stronach pokazywać?', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub">
												<?php
												esc_html_e(
													'„Strona główna” = strona ustawiona w WordPress jako strona startowa (Ustawienia → Czytanie).',
													'design-cart-spin-to-win'
												);
												?>
											</p>
										</div>
									</div>
									<div class="dc-switch-group dc-switch-group--solid dc-switch-group--block" role="radiogroup">
										<label class="dc-switch-btn">
											<input
												class="dc-switch-btn__input"
												type="radio"
												name="dc_stw_general[display_pages]"
												value="home"
												<?php checked( $settings['display_pages'], 'home' ); ?>
											/>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Tylko strona główna', 'design-cart-spin-to-win' ); ?></span>
										</label>
										<label class="dc-switch-btn">
											<input
												class="dc-switch-btn__input"
												type="radio"
												name="dc_stw_general[display_pages]"
												value="all"
												<?php checked( $settings['display_pages'], 'all' ); ?>
											/>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Każda podstrona sklepu', 'design-cart-spin-to-win' ); ?></span>
										</label>
									</div>
								</div>

								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-clock-o"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Jak często otwierać popup?', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub">
												<?php
												esc_html_e(
													'„Raz” = po pierwszym pokazaniu zapamiętujemy w przeglądarce. „Sesja” = po każdym nowym wejściu w sesję.',
													'design-cart-spin-to-win'
												);
												?>
											</p>
										</div>
									</div>
									<div class="dc-switch-group dc-switch-group--solid dc-switch-group--block" role="radiogroup">
										<label class="dc-switch-btn">
											<input
												class="dc-switch-btn__input"
												type="radio"
												name="dc_stw_general[display_frequency]"
												value="once"
												<?php checked( $settings['display_frequency'], 'once' ); ?>
											/>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Tylko raz (zapamiętaj w przeglądarce)', 'design-cart-spin-to-win' ); ?></span>
										</label>
										<label class="dc-switch-btn">
											<input
												class="dc-switch-btn__input"
												type="radio"
												name="dc_stw_general[display_frequency]"
												value="every_session"
												<?php checked( $settings['display_frequency'], 'every_session' ); ?>
											/>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Przy każdej nowej sesji', 'design-cart-spin-to-win' ); ?></span>
										</label>
										<label class="dc-switch-btn">
											<input
												class="dc-switch-btn__input"
												type="radio"
												name="dc_stw_general[display_frequency]"
												value="every_reload"
												<?php checked( $settings['display_frequency'], 'every_reload' ); ?>
											/>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Przy każdym przeładowaniu strony (testy)', 'design-cart-spin-to-win' ); ?></span>
										</label>
										<label class="dc-switch-btn">
											<input
												class="dc-switch-btn__input"
												type="radio"
												name="dc_stw_general[display_frequency]"
												value="button_only"
												<?php checked( $settings['display_frequency'], 'button_only' ); ?>
											/>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Tylko po kliknięciu przycisku (bez auto-otwarcia)', 'design-cart-spin-to-win' ); ?></span>
										</label>
									</div>
								</div>
							</div>

							<div id="tab-trigger" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-code"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Klasy CSS uruchamiające popup', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub">
												<?php
												esc_html_e(
													'Podaj selektory CSS elementów, po których kliknięciu otworzy się kołowrotek. Możesz podać kilka, oddzielonych przecinkiem.',
													'design-cart-spin-to-win'
												);
												?>
											</p>
										</div>
									</div>
									<div class="dc-field">
										<label class="dc-label" for="dc_stw_trigger_classes"><?php esc_html_e( 'Selektory CSS', 'design-cart-spin-to-win' ); ?></label>
										<input
											class="dc-input"
											type="text"
											id="dc_stw_trigger_classes"
											name="dc_stw_general[trigger_classes]"
											value="<?php echo esc_attr( $settings['trigger_classes'] ); ?>"
											placeholder=".dc-stw-open, .btn-spin"
										/>
										<p class="dc-hint"><?php esc_html_e( 'Przykład: .dc-stw-open, #open-wheel, .menu-spin', 'design-cart-spin-to-win' ); ?></p>
									</div>
								</div>
							</div>

							<div id="tab-colors" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-paint-brush"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Tła popupów', 'design-cart-spin-to-win' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Kolor dużego popupu z kołem oraz małej karty z odbiorem nagrody.', 'design-cart-spin-to-win' ); ?></p>
										</div>
									</div>
									<div class="dc-row dc-row--2">
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Tło popupu z kołowrotkiem', 'design-cart-spin-to-win' ); ?></label>
											<div
												class="dc-colorpicker"
												data-dc-colorpicker
												data-name="dc_stw_general[wheel_bg]"
												data-value="<?php echo esc_attr( $settings['wheel_bg'] ); ?>"
												data-label="<?php esc_attr_e( 'Tło koła', 'design-cart-spin-to-win' ); ?>"
											></div>
										</div>
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Tło małego popupu (odbiór nagrody)', 'design-cart-spin-to-win' ); ?></label>
											<div
												class="dc-colorpicker"
												data-dc-colorpicker
												data-name="dc_stw_general[claim_bg]"
												data-value="<?php echo esc_attr( $settings['claim_bg'] ); ?>"
												data-label="<?php esc_attr_e( 'Tło karty', 'design-cart-spin-to-win' ); ?>"
											></div>
										</div>
									</div>
								</div>
							</div>

							<div class="dc-actions">
								<button type="submit" class="dc-btn dc-btn--primary">
									<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz ustawienia', 'design-cart-spin-to-win' ); ?>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
