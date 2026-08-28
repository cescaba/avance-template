<?php
/**
 * Scheduling Section Template Part
 *
 * @package Avance_Template
 * 
 * Variables passed to template:
 * @var array $args Configuración del componente
 */

if (!defined('ABSPATH')) {
	exit;
}

/** @var array $args */
$args = wp_parse_args($args ?? array(), array());

?>

<section id="scheduling-section" class="contacto-agenda contacto-agenda--animated" aria-label="<?php esc_attr_e('Sección de Agendamiento', 'avance-template'); ?>">
	<div class="contacto-agenda__container">
		<header class="contacto-agenda__header">
			<div class="contacto-agenda__header-content">
				<div class="contacto-agenda__kicker"><?php esc_html_e('Agendamiento directo', 'avance-template'); ?></div>
				<h2 class="contacto-agenda__title"><?php esc_html_e('Sesión gratuita de 10 min', 'avance-template'); ?></h2>
				<div class="contacto-agenda__subtitle"><?php esc_html_e('Google Meet · Sin compromiso · Calendly plan gratuito', 'avance-template'); ?></div>
			</div>
		</header>

		<div class="contacto-agenda__grid">
			<?php
			get_template_part('template-parts/calendar-agenda', null, [
				'host_title'    => 'Avance Empresarial',
				'host_meta'     => 'Sesión de diagnóstico · 30 min · Google Meet',
				'avatar_text'   => 'AE',
				'tag_text'      => 'Gratis',
				'cal_id'        => 'contactoAgenda',
				'timezone'      => 'Lima (GMT-5)',
			]);
			?>

			<div class="contacto-agenda__right">
				<form id="contacto-agenda-form" class="contacto-agenda__form" aria-label="<?php esc_attr_e('Formulario de agendamiento', 'avance-template'); ?>">
					<?php wp_nonce_field('avance_scheduling_form', 'nonce', false); ?>

					<div class="contacto-agenda__section-header">
					<div class="contacto-agenda__section-kicker"><?php esc_html_e('¿Qué te gustaría tratar en la sesión?', 'avance-template'); ?></div>
					<div class="contacto-agenda__section-sub"><?php esc_html_e('Selecciona el tema principal para que pueda preparar la sesión y llegar con información relevante para tu caso.', 'avance-template'); ?></div>
				</div>

				<div class="contacto-agenda__form-fields ">
					<div class="contacto-agenda__form-row">
						<div class="contacto-agenda__field">
							<label for="contacto-agenda-name"><?php esc_html_e('Nombre completo *', 'avance-template'); ?></label>
							<input class="contacto-agenda__input" type="text" id="contacto-agenda-name" name="contacto-agenda-name" placeholder="<?php esc_attr_e('ej. Carlos González', 'avance-template'); ?>" required aria-required="true">
						</div>
						<div class="contacto-agenda__field">
							<label for="contacto-agenda-phone"><?php esc_html_e('WhatsApp', 'avance-template'); ?></label>
							<input class="contacto-agenda__input" type="tel" id="contacto-agenda-phone" name="contacto-agenda-phone" placeholder="<?php esc_attr_e('ej. +51 987 654 321', 'avance-template'); ?>" required aria-required="true">
						</div>
					</div>

					<div class="contacto-agenda__field">
						<label for="contacto-agenda-topic"><?php esc_html_e('Tema de interés *', 'avance-template'); ?></label>
						<select
							id="contacto-agenda-topic"
							name="contacto-agenda-topic"
							class="contacto-agenda__input"
							required
							aria-required="true">
							<option value=""><?php esc_html_e('Selecciona un servicio o tema...', 'avance-template'); ?></option>
							<option value="Diagnóstico de negocio"><?php esc_html_e('Diagnóstico de negocio', 'avance-template'); ?></option>
							<option value="Estrategia comercial"><?php esc_html_e('Estrategia comercial', 'avance-template'); ?></option>
							<option value="Capacitación ejecutiva"><?php esc_html_e('Capacitación ejecutiva', 'avance-template'); ?></option>
							<option value="Mentoría 1:1"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></option>
							<option value="Otro tema"><?php esc_html_e('Otro tema', 'avance-template'); ?></option>
						</select>
					</div>
				</div>

				<button type="submit" class="contacto-agenda__btn contacto-agenda__btn--primary contacto-agenda__btn--block" id="contacto-agenda-submit" disabled><?php esc_html_e('Agendar Reunión', 'avance-template'); ?></button>

					<button type="button" class="contacto-agenda__btn contacto-agenda__btn--back" id="contacto-agenda-back"><?php esc_html_e('Volver al calendario', 'avance-template'); ?></button>
				</form>

				<div class="contacto-agenda__feature-grid ">
					<div class="contacto-agenda__feature-card">
						<div class="contacto-agenda__feature-title"><?php esc_html_e('Sin compromiso', 'avance-template'); ?></div>
						<div class="contacto-agenda__feature-body"><?php esc_html_e('La sesión es 100% gratuita', 'avance-template'); ?></div>
					</div>
					<div class="contacto-agenda__feature-card">
						<div class="contacto-agenda__feature-title"><?php esc_html_e('Respuesta rápida', 'avance-template'); ?></div>
						<div class="contacto-agenda__feature-body"><?php esc_html_e('Confirmación en minutos', 'avance-template'); ?></div>
					</div>
					<div class="contacto-agenda__feature-card">
						<div class="contacto-agenda__feature-title"><?php esc_html_e('Flexible', 'avance-template'); ?></div>
						<div class="contacto-agenda__feature-body"><?php esc_html_e('Reagenda cuando quieras', 'avance-template'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
