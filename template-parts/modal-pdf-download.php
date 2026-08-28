<?php
/**
 * Modal PDF Download
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div id="pf-modal-overlay" class="pf-overlay pf-overlay--hidden" aria-hidden="true" role="dialog" aria-labelledby="pf-modal-title">
	<form id="pf-modal-form" class="pf-modal" novalidate>
		<div class="pf-head">
			<div class="pf-topbar">
				<span class="pf-kicker"><?php esc_html_e('Formulario de descarga', 'avance-template'); ?></span>
				<button type="button" class="pf-close" id="pf-close-btn" aria-label="<?php esc_attr_e('Cerrar', 'avance-template'); ?>">&#10005;</button>
			</div>

			<div class="pf-titles">
				<h2 id="pf-modal-title" class="pf-title"><?php esc_html_e('Descarga tu PDF gratuito', 'avance-template'); ?></h2>
				<p class="pf-sub"><?php esc_html_e('Completa tus datos y te enviamos el enlace de descarga al instante.', 'avance-template'); ?></p>
			</div>
		</div>

		<div class="pf-grid">
			<div class="pf-field">
				<label for="pf-email"><?php esc_html_e('Correo electrónico *', 'avance-template'); ?></label>
				<input id="pf-email" class="pf-input" type="email" name="email" placeholder="<?php esc_attr_e('juan@empresa.com', 'avance-template'); ?>" required aria-required="true">
			</div>

			<div class="pf-field">
				<label for="pf-name"><?php esc_html_e('Nombre completo *', 'avance-template'); ?></label>
				<input id="pf-name" class="pf-input" type="text" name="nombre" placeholder="<?php esc_attr_e('Juan Pérez', 'avance-template'); ?>" required aria-required="true">
			</div>

			<div class="pf-field">
				<label for="pf-phone"><?php esc_html_e('Teléfono', 'avance-template'); ?></label>
				<input id="pf-phone" class="pf-input" type="tel" name="telefono" placeholder="<?php esc_attr_e('+51 999 000 000', 'avance-template'); ?>">
			</div>

			<div class="pf-field">
				<label for="pf-company"><?php esc_html_e('Empresa *', 'avance-template'); ?></label>
				<input id="pf-company" class="pf-input" type="text" name="empresa" placeholder="<?php esc_attr_e('Nombre de tu empresa', 'avance-template'); ?>" required aria-required="true">
			</div>

			<div class="pf-field">
				<label for="pf-size"><?php esc_html_e('N.º de empleados *', 'avance-template'); ?></label>
				<div class="pf-selectwrap">
					<select id="pf-size" class="pf-select" name="empleados" required aria-required="true">
						<option value="" selected disabled><?php esc_html_e('Selecciona un rango', 'avance-template'); ?></option>
						<option value="1-10">1 – 10</option>
						<option value="11-50">11 – 50</option>
						<option value="51-200">51 – 200</option>
						<option value="201-1000">201 – 1000</option>
						<option value="1000+"><?php esc_html_e('Más de 1000', 'avance-template'); ?></option>
					</select>
					<span class="pf-caret">&#9662;</span>
				</div>
			</div>

			<div class="pf-field">
				<label for="pf-industry"><?php esc_html_e('Industria *', 'avance-template'); ?></label>
				<div class="pf-selectwrap">
					<select id="pf-industry" class="pf-select" name="industria" required aria-required="true">
						<option value="" selected disabled><?php esc_html_e('Selecciona tu industria', 'avance-template'); ?></option>
						<option value="tecnologia"><?php esc_html_e('Tecnología', 'avance-template'); ?></option>
						<option value="retail"><?php esc_html_e('Retail y consumo', 'avance-template'); ?></option>
						<option value="finanzas"><?php esc_html_e('Finanzas y seguros', 'avance-template'); ?></option>
						<option value="salud"><?php esc_html_e('Salud', 'avance-template'); ?></option>
						<option value="manufactura"><?php esc_html_e('Manufactura', 'avance-template'); ?></option>
						<option value="educacion"><?php esc_html_e('Educación', 'avance-template'); ?></option>
						<option value="otro"><?php esc_html_e('Otro', 'avance-template'); ?></option>
					</select>
					<span class="pf-caret">&#9662;</span>
				</div>
			</div>
		</div>

		<div class="pf-foot">
			<button type="submit" class="pf-submit">
				<span><?php esc_html_e('Descargar PDF', 'avance-template'); ?></span>
				<span class="pf-arrow">&#8594;</span>
			</button>
			<p class="pf-note"><?php esc_html_e('Recibirás el enlace también por correo electrónico.', 'avance-template'); ?></p>
		</div>

	</form>
</div>
