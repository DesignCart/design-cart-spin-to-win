<?php
/**
 * Pojedynczy blok typografii.
 *
 * @var string               $name_prefix
 * @var array<string,mixed>  $data
 * @var bool                 $has_background
 *
 * @package DesignCartSpinToWin
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- Template scope variables for included views.

$name_prefix     = isset( $name_prefix ) ? (string) $name_prefix : '';
$data            = isset( $data ) && is_array( $data ) ? $data : array();
$has_background  = ! empty( $has_background );
$weights         = DC_STW_Typography_Settings::font_weights();
$bg_value        = ! empty( $data['background'] ) ? (string) $data['background'] : '#ffb703';
?>
<div class="dc-row dc-row--2">
	<div class="dc-field">
		<label class="dc-label"><?php esc_html_e( 'Kolor tekstu', 'design-cart-spin-to-win' ); ?></label>
		<div
			class="dc-colorpicker"
			data-dc-colorpicker
			data-name="<?php echo esc_attr( $name_prefix . '[color]' ); ?>"
			data-value="<?php echo esc_attr( (string) ( $data['color'] ?? '#2a1b4d' ) ); ?>"
			data-label="<?php esc_attr_e( 'Kolor', 'design-cart-spin-to-win' ); ?>"
		></div>
	</div>
	<?php if ( $has_background ) : ?>
		<div class="dc-field">
			<label class="dc-label"><?php esc_html_e( 'Tło', 'design-cart-spin-to-win' ); ?></label>
			<div
				class="dc-colorpicker"
				data-dc-colorpicker
				data-name="<?php echo esc_attr( $name_prefix . '[background]' ); ?>"
				data-value="<?php echo esc_attr( $bg_value ); ?>"
				data-label="<?php esc_attr_e( 'Tło', 'design-cart-spin-to-win' ); ?>"
			></div>
		</div>
	<?php else : ?>
		<div class="dc-field">
			<label class="dc-label"><?php esc_html_e( 'Rozmiar (px)', 'design-cart-spin-to-win' ); ?></label>
			<input
				class="dc-input"
				type="number"
				min="10"
				max="96"
				name="<?php echo esc_attr( $name_prefix . '[size]' ); ?>"
				value="<?php echo esc_attr( (string) (int) ( $data['size'] ?? 16 ) ); ?>"
			/>
		</div>
	<?php endif; ?>
</div>
<div class="dc-row dc-row--2">
	<?php if ( $has_background ) : ?>
		<div class="dc-field">
			<label class="dc-label"><?php esc_html_e( 'Rozmiar (px)', 'design-cart-spin-to-win' ); ?></label>
			<input
				class="dc-input"
				type="number"
				min="10"
				max="96"
				name="<?php echo esc_attr( $name_prefix . '[size]' ); ?>"
				value="<?php echo esc_attr( (string) (int) ( $data['size'] ?? 16 ) ); ?>"
			/>
		</div>
	<?php endif; ?>
	<div class="dc-field">
		<label class="dc-label"><?php esc_html_e( 'Grubość', 'design-cart-spin-to-win' ); ?></label>
		<select class="dc-input dc-select" name="<?php echo esc_attr( $name_prefix . '[weight]' ); ?>">
			<?php foreach ( $weights as $weight ) : ?>
				<option value="<?php echo esc_attr( (string) $weight ); ?>" <?php selected( (int) ( $data['weight'] ?? 400 ), $weight ); ?>>
					<?php echo esc_html( (string) $weight ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php if ( ! $has_background ) : ?>
		<div class="dc-field">
			<label class="dc-toggle" style="margin-top:1.6rem;">
				<input
					class="dc-toggle__input"
					type="checkbox"
					name="<?php echo esc_attr( $name_prefix . '[uppercase]' ); ?>"
					value="1"
					<?php checked( (string) ( $data['uppercase'] ?? '0' ), '1' ); ?>
				/>
				<span class="dc-toggle__track"></span>
				<span><?php esc_html_e( 'Uppercase', 'design-cart-spin-to-win' ); ?></span>
			</label>
		</div>
	<?php endif; ?>
</div>
<?php if ( $has_background ) : ?>
	<div class="dc-field" style="margin-top:1rem;">
		<label class="dc-toggle">
			<input
				class="dc-toggle__input"
				type="checkbox"
				name="<?php echo esc_attr( $name_prefix . '[uppercase]' ); ?>"
				value="1"
				<?php checked( (string) ( $data['uppercase'] ?? '0' ), '1' ); ?>
			/>
			<span class="dc-toggle__track"></span>
			<span><?php esc_html_e( 'Uppercase', 'design-cart-spin-to-win' ); ?></span>
		</label>
	</div>
<?php endif; ?>
