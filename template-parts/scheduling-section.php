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

<section class="contacto-agenda contacto-agenda--animated" aria-label="<?php esc_attr_e('Sección de Agendamiento', 'avance-template'); ?>">
	<div class="contacto-agenda__container">
		<div class="contacto-agenda__header animate-on-scroll" data-animate>
			<div class="contacto-agenda__header-content">
				<div class="contacto-agenda__kicker"><?php esc_html_e('Agendamiento directo', 'avance-template'); ?></div>
				<h2 class="contacto-agenda__title"><?php esc_html_e('Sesión gratuita de 10 min', 'avance-template'); ?></h2>
				<div class="contacto-agenda__subtitle"><?php esc_html_e('Google Meet · Sin compromiso', 'avance-template'); ?></div>
			</div>
		</div>

		<div class="contacto-agenda__grid animate-on-scroll" data-animate>
			<div class="contacto-agenda__left">
				<div class="contacto-agenda__card">
					<div class="contacto-agenda__host-row">
						<div class="contacto-agenda__avatar">AE</div>
						<div class="contacto-agenda__host-info">
							<div class="contacto-agenda__host-title"><?php esc_html_e('Avance Empresarial', 'avance-template'); ?></div>
							<div class="contacto-agenda__host-meta"><?php esc_html_e('Sesión de diagnóstico · 30 min · Google Meet', 'avance-template'); ?></div>
						</div>
						<span class="contacto-agenda__tag"><?php esc_html_e('Gratis', 'avance-template'); ?></span>
					</div>
				</div>

				<div class="contacto-agenda__card">
					<div class="contacto-agenda__cal-header">
						<button class="contacto-agenda__cal-nav" id="contactoAgendaPrevMonth" aria-label="<?php esc_attr_e('Mes anterior', 'avance-template'); ?>">‹</button>
						<div class="contacto-agenda__cal-month-label" id="contactoAgendaMonthLabel"></div>
						<button class="contacto-agenda__cal-nav" id="contactoAgendaNextMonth" aria-label="<?php esc_attr_e('Mes siguiente', 'avance-template'); ?>">›</button>
					</div>

					<div class="contacto-agenda__cal-grid" id="contactoAgendaCalGrid">
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('DOM', 'avance-template'); ?></div>
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('LUN', 'avance-template'); ?></div>
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('MAR', 'avance-template'); ?></div>
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('MIÉ', 'avance-template'); ?></div>
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('JUE', 'avance-template'); ?></div>
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('VIE', 'avance-template'); ?></div>
						<div class="contacto-agenda__cal-weekday"><?php esc_html_e('SÁB', 'avance-template'); ?></div>
					</div>

					<div class="contacto-agenda__tz-row">🕐 Lima (GMT-5)</div>
					<div class="contacto-agenda__links-row">
						<span><?php esc_html_e('Google Calendar', 'avance-template'); ?></span>
						<span><?php esc_html_e('Google Meet', 'avance-template'); ?></span>
					</div>
				</div>
			</div>

			<div class="contacto-agenda__right">
				<div class="contacto-agenda__section-header">
					<div class="contacto-agenda__section-kicker"><?php esc_html_e('¿Qué te gustaría tratar en la sesión?', 'avance-template'); ?></div>
					<div class="contacto-agenda__section-sub"><?php esc_html_e('Selecciona el tema principal para que pueda preparar la sesión y llegar con información relevante para tu caso.', 'avance-template'); ?></div>
				</div>

				<div class="contacto-agenda__form-fields">
					<div class="contacto-agenda__form-row">
						<div class="contacto-agenda__field">
							<label for="contacto-agenda-name"><?php esc_html_e('Nombre completo *', 'avance-template'); ?></label>
							<input class="contacto-agenda__input" type="text" id="contacto-agenda-name" placeholder="<?php esc_attr_e('Juan Pérez', 'avance-template'); ?>">
						</div>
						<div class="contacto-agenda__field">
							<label for="contacto-agenda-phone"><?php esc_html_e('WhatsApp', 'avance-template'); ?></label>
							<input class="contacto-agenda__input" type="text" id="contacto-agenda-phone" placeholder="<?php esc_attr_e('+51 999 000 000', 'avance-template'); ?>">
						</div>
					</div>

					<div class="contacto-agenda__field">
						<label for="contacto-agenda-topic"><?php esc_html_e('Tema de interés *', 'avance-template'); ?></label>
						<div class="contacto-agenda__select-wrapper">
							<button class="contacto-agenda__select-trigger" id="contacto-agenda-topic-trigger" type="button">
								<?php esc_html_e('Selecciona un servicio o tema...', 'avance-template'); ?>
							</button>
							<div class="contacto-agenda__select-menu" id="contacto-agenda-topic-menu">
								<div class="contacto-agenda__select-option" data-value="Diagnóstico de negocio"><?php esc_html_e('Diagnóstico de negocio', 'avance-template'); ?></div>
								<div class="contacto-agenda__select-option" data-value="Estrategia comercial"><?php esc_html_e('Estrategia comercial', 'avance-template'); ?></div>
								<div class="contacto-agenda__select-option" data-value="Capacitación ejecutiva"><?php esc_html_e('Capacitación ejecutiva', 'avance-template'); ?></div>
								<div class="contacto-agenda__select-option" data-value="Mentoría 1:1"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></div>
								<div class="contacto-agenda__select-option" data-value="Otro tema"><?php esc_html_e('Otro tema', 'avance-template'); ?></div>
							</div>
						</div>
						<input type="hidden" id="contacto-agenda-topic" value="">
					</div>
				</div>

				<button class="contacto-agenda__btn contacto-agenda__btn--primary contacto-agenda__btn--block animate-on-scroll" id="contacto-agenda-submit" disabled data-animate><?php esc_html_e('Agendar Reunión', 'avance-template'); ?></button>

				<div class="contacto-agenda__feature-grid animate-on-scroll" data-animate>
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
