<?php
/**
 * Hero banner panelu admina.
 *
 * Oczekuje zmiennej $hero:
 * - title (string)
 * - icon (string) — klasa Font Awesome bez „fa ”
 * - crumb (string) — opcjonalny breadcrumb sekcji
 * - form_id (string) — ID formularza do przycisku Zapisz
 * - show_dashboard (bool) — link powrotu do dashboardu
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$hero = wp_parse_args(
	isset( $hero ) && is_array( $hero ) ? $hero : array(),
	array(
		'title'          => 'SpinToWin',
		'icon'           => 'fa-diamond',
		'crumb'          => '',
		'form_id'        => '',
		'show_dashboard' => false,
	)
);

$logo_url = DC_STW_URL . 'admin/img/dc-logo-white.png';
$dash_url = DC_STW_Admin::section_url( 'dashboard' );
$plugins  = admin_url( 'plugins.php' );
?>
<div class="dc-hero">
	<div class="dc-hero__mesh"></div>
	<img
		class="dc-hero__logo"
		src="<?php echo esc_url( $logo_url ); ?>"
		alt=""
		aria-hidden="true"
		width="384"
		height="384"
	>
	<div class="dc-hero__orb dc-hero__orb--1"></div>
	<div class="dc-hero__orb dc-hero__orb--2"></div>
	<div class="dc-hero__inner">
		<div class="dc-hero__row">
			<div class="dc-hero__brand">
				<span class="dc-hero__icon"><i class="fa <?php echo esc_attr( $hero['icon'] ); ?>"></i></span>
				<div>
					<p class="dc-hero__eyebrow">Design Cart</p>
					<h1 class="dc-hero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
				</div>
			</div>
			<div class="dc-hero__actions">
				<?php if ( $hero['form_id'] ) : ?>
					<button type="submit" form="<?php echo esc_attr( $hero['form_id'] ); ?>" class="dc-btn dc-btn--light">
						<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz', 'design-cart-spin-to-win' ); ?>
					</button>
				<?php endif; ?>
				<?php if ( ! empty( $hero['show_dashboard'] ) ) : ?>
					<a class="dc-btn dc-btn--ghost" href="<?php echo esc_url( $dash_url ); ?>">
						<i class="fa fa-th-large"></i> <?php esc_html_e( 'Dashboard', 'design-cart-spin-to-win' ); ?>
					</a>
				<?php endif; ?>
				<a class="dc-btn dc-btn--ghost" href="<?php echo esc_url( $plugins ); ?>">
					<i class="fa fa-arrow-left"></i>
				</a>
			</div>
		</div>
		<ul class="dc-hero__bc">
			<li><a href="<?php echo esc_url( $plugins ); ?>"><?php esc_html_e( 'Wtyczki', 'design-cart-spin-to-win' ); ?></a></li>
			<li><a href="<?php echo esc_url( $dash_url ); ?>">SpinToWin</a></li>
			<?php if ( $hero['crumb'] ) : ?>
				<li><?php echo esc_html( $hero['crumb'] ); ?></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
