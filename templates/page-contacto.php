<?php

/**
 * Template Name: Contacto
 * Template Post Type: page
 *
 * Página de contacto
 *
 * @package Avance_Template
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="contacto-main">

	<!-- Hero Section -->
	<section class="contacto-hero" aria-label="<?php esc_attr_e('Sección Hero Contacto', 'avance-template'); ?>">
		<div class="contacto-hero__bg" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Rectangle 16.png'); ?>')">
			<div class="contacto-hero__container">
				<p class="contacto-hero__label"><?php esc_html_e('Contacto', 'avance-template'); ?></p>
				<h1 class="contacto-hero__title"><?php esc_html_e('Hablemos', 'avance-template'); ?></h1>
				<p class="contacto-hero__description"><?php esc_html_e('Elige cómo prefieres conectar. Respondo en menos de 1 hora.', 'avance-template'); ?></p>
				<div class="contacto-hero__content">
					<a href="#" class="contacto-hero__btn-secondary">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="contacto-hero__btn-icon" aria-hidden="true">
						<?php esc_html_e('Hablar por WhatsApp', 'avance-template'); ?>
					</a>
					<a href="#contacto-form" class="contacto-hero__btn-primary"><?php esc_html_e('informes@avance-empresarial.com', 'avance-template'); ?></a>

				</div>
			</div>
		</div>
	</section>

	<!-- Scheduling Section -->
	<section class="contacto-agenda" aria-label="<?php esc_attr_e('Agendar Sesión', 'avance-template'); ?>">
		<div class="contacto-agenda__container">
			<div class="contacto-agenda__header animate-on-scroll" data-animate>
				<h2 class="contacto-agenda__title"><?php esc_html_e('Agendamiento directo', 'avance-template'); ?></h2>
				<p class="contacto-agenda__description"><?php esc_html_e('Sesión gratuita de 30 min', 'avance-template'); ?></p>
				<p class="contacto-agenda__details"><?php esc_html_e('Google Meet · Sin compromiso · Calendly plan gratuito', 'avance-template'); ?></p>
			</div>

			<div class="contacto-agenda__grid">
				<div class="contacto-agenda__consultoría animate-on-scroll" data-animate>

				</div>

				<div class="contacto-agenda__mentoría animate-on-scroll" data-animate>

				</div>
			</div>
		</div>
	</section>

	<!-- Contact Form Section -->
	<section class="contacto-form-section" aria-label="<?php esc_attr_e('Formulario de Contacto', 'avance-template'); ?>" id="contacto-form">
		<div class="contacto-form-container">
			<div class="contacto-form-header animate-on-scroll" data-animate>
				<h2 class="contacto-form-title"><?php esc_html_e('Cuéntame sobre ti', 'avance-template'); ?></h2>
				<p class="contacto-form-subtitle"><?php esc_html_e('Comparte información sobre tu empresa y tus desafíos comerciales.', 'avance-template'); ?></p>
			</div>

			<form class="contacto-form animate-on-scroll" data-animate>
				<div class="contacto-form-row">
					<div class="contacto-form-group">
						<label for="contacto-name" class="contacto-form-label"><?php esc_html_e('Nombre completo', 'avance-template'); ?></label>
						<input type="text" id="contacto-name" name="nombre" class="contacto-form-input" placeholder="<?php esc_attr_e('Tu nombre', 'avance-template'); ?>" required>
					</div>
					<div class="contacto-form-group">
						<label for="contacto-email" class="contacto-form-label"><?php esc_html_e('Correo electrónico', 'avance-template'); ?></label>
						<input type="email" id="contacto-email" name="email" class="contacto-form-input" placeholder="<?php esc_attr_e('tu@email.com', 'avance-template'); ?>" required>
					</div>
				</div>

				<div class="contacto-form-row">
					<div class="contacto-form-group">
						<label for="contacto-empresa" class="contacto-form-label"><?php esc_html_e('Empresa', 'avance-template'); ?></label>
						<input type="text" id="contacto-empresa" name="empresa" class="contacto-form-input" placeholder="<?php esc_attr_e('Nombre de tu empresa', 'avance-template'); ?>" required>
					</div>
					<div class="contacto-form-group">
						<label for="contacto-telefono" class="contacto-form-label"><?php esc_html_e('Teléfono', 'avance-template'); ?></label>
						<input type="tel" id="contacto-telefono" name="telefono" class="contacto-form-input" placeholder="<?php esc_attr_e('+51 999 999 999', 'avance-template'); ?>" required>
					</div>
				</div>

				<div class="contacto-form-group">
					<label for="contacto-asunto" class="contacto-form-label"><?php esc_html_e('Asunto', 'avance-template'); ?></label>
					<select id="contacto-asunto" name="asunto" class="contacto-form-input" required>
						<option value=""><?php esc_html_e('Selecciona un asunto', 'avance-template'); ?></option>
						<option value="capacitacion"><?php esc_html_e('Capacitación', 'avance-template'); ?></option>
						<option value="consultoría"><?php esc_html_e('Consultoría comercial', 'avance-template'); ?></option>
						<option value="mentoría"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></option>
						<option value="otro"><?php esc_html_e('Otro', 'avance-template'); ?></option>
					</select>
				</div>

				<div class="contacto-form-group">
					<label for="contacto-mensaje" class="contacto-form-label"><?php esc_html_e('Mensaje', 'avance-template'); ?></label>
					<textarea id="contacto-mensaje" name="mensaje" class="contacto-form-textarea" placeholder="<?php esc_attr_e('Cuéntame sobre tu situación...', 'avance-template'); ?>" rows="6" required></textarea>
				</div>

				<button type="submit" class="contacto-form-btn"><?php esc_html_e('Enviar mensaje', 'avance-template'); ?></button>
			</form>
		</div>
	</section>

</main>

<?php
get_footer();
