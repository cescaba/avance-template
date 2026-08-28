<?php

/**
 * Form Section Template Part
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

/** @var array $args */
$args = wp_parse_args($args ?? array(), array(
	'label' => 'FORMULARIO MULTISERVICIO',
	'title' => '¿Listo para dar el siguiente paso?',
	'description' => 'Completa el formulario y me contacto en menos de 24h.',
	'button_text' => 'Enviar mensaje',
	'whatsapp_text' => 'Escribir al WhatsApp',
	'container_class' => 'home-form',
));
?>

<section class="<?php echo esc_attr($args['container_class']); ?>" id="home-form" aria-label="<?php esc_attr_e('Formulario de Contacto', 'avance-template'); ?>">
	<div class="home-form__container">
		<header class="home-form__header">
			<p class="home-form__label"><?php echo esc_html($args['label']); ?></p>
			<h2 class="home-form__title"><?php echo esc_html($args['title']); ?></h2>
			<p class="home-form__description"><?php echo esc_html($args['description']); ?></p>
		</header>

		<form id="contacto-wsp-form" class="home-form__form" aria-label="<?php esc_attr_e('Formulario de contacto por WhatsApp', 'avance-template'); ?>">
			<?php wp_nonce_field('avance_contact_form', 'nonce', false); ?>

			<div class="home-form__row">
				<div class="home-form__field">
					<label for="contacto_wsp_nombre" class="home-form__label-field">
						<?php esc_html_e('Nombre completo', 'avance-template'); ?>
						<span aria-label="<?php esc_attr_e('requerido', 'avance-template'); ?>">*</span>
					</label>
					<input
						type="text"
						id="contacto_wsp_nombre"
						name="contacto_wsp_nombre"
						class="home-form__input"
						placeholder="<?php esc_attr_e('Juan Pérez', 'avance-template'); ?>"
						required
						aria-required="true">
				</div>
				<div class="home-form__field">
					<label for="contacto_wsp_email" class="home-form__label-field">
						<?php esc_html_e('Email', 'avance-template'); ?>
						<span aria-label="<?php esc_attr_e('requerido', 'avance-template'); ?>">*</span>
					</label>
					<input
						type="email"
						id="contacto_wsp_email"
						name="contacto_wsp_email"
						class="home-form__input"
						placeholder="<?php esc_attr_e('juan@empresa.com', 'avance-template'); ?>"
						required
						aria-required="true">
				</div>
			</div>

			<div class="home-form__row">
				<div class="home-form__field">
					<label for="contacto_wsp_numero" class="home-form__label-field">
						<?php esc_html_e('WhatsApp', 'avance-template'); ?>
						<span aria-label="<?php esc_attr_e('requerido', 'avance-template'); ?>">*</span>
					</label>
					<input
						type="tel"
						id="contacto_wsp_numero"
						name="contacto_wsp_numero"
						class="home-form__input"
						placeholder="<?php esc_attr_e('+51 999 000 000', 'avance-template'); ?>"
						required
						aria-required="true">
				</div>
				<div class="home-form__field">
					<label for="contacto_wsp_asunto" class="home-form__label-field">
						<?php esc_html_e('Servicio de interés', 'avance-template'); ?>
						<span aria-label="<?php esc_attr_e('requerido', 'avance-template'); ?>">*</span>
					</label>
					<input
						type="text"
						id="contacto_wsp_asunto"
						name="contacto_wsp_asunto"
						class="home-form__input"
						placeholder="<?php esc_attr_e('Ej: Capacitación, Mentoría', 'avance-template'); ?>"
						required
						aria-required="true">
				</div>
			</div>

			<div class="home-form__field home-form__field--full">
				<label for="contacto_wsp_mensaje" class="home-form__label-field">
					<?php esc_html_e('Mensaje', 'avance-template'); ?>
				</label>
				<textarea
					id="contacto_wsp_mensaje"
					name="contacto_wsp_mensaje"
					class="home-form__textarea"
					rows="4"
					placeholder="<?php esc_attr_e('Cuéntame brevemente tu situación...', 'avance-template'); ?>"></textarea>
			</div>

			<div class="home-form__buttons ">
				<button type="submit" class="home-form__button">
					<?php echo esc_html($args['button_text']); ?>
				</button>
			</div>
		</form>
	</div>
</section>