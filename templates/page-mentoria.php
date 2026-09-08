<?php

/**
 * Template: Página de Mentorías
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="site-main">

	<!-- SECTION 1: Mentoría 1:1 -->
	<section class="mentoria-intro" aria-label="<?php esc_attr_e('Mentoría Personalizada', 'avance-template'); ?>">
		<div class="mentoria-intro__container">
			<div class="mentoria-intro__features">
				<div class="mentoria-intro__mentoria">
					<h2 class="mentoria-intro__title"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></h2>
					<h3 class="mentoria-intro__subtitle"><?php esc_html_e('Acelera tu crecimiento con acompañamiento real', 'avance-template'); ?></h3>
					<p class="mentoria-intro__description"><?php esc_html_e('Trabajo contigo de forma personalizada para desbloquear los obstáculos que frenan tu desarrollo profesional y los resultados de tu empresa. Sin teoría: sesiones prácticas, acción y seguimiento.', 'avance-template'); ?></p>
					<ul class="mentoria-intro__list">
						<li class="mentoria-intro__list-item"><?php esc_html_e('Sesiones 1:1 enfocadas en tus desafíos reales', 'avance-template'); ?></li>
						<li class="mentoria-intro__list-item"><?php esc_html_e('Plan de acción concreto y medible', 'avance-template'); ?></li>
						<li class="mentoria-intro__list-item"><?php esc_html_e('Acceso directo por WhatsApp entre sesiones', 'avance-template'); ?></li>
						<li class="mentoria-intro__list-item"><?php esc_html_e('Revisión de métricas y ajuste continuo', 'avance-template'); ?></li>
					</ul>
				</div>
				<div class="mentoria-intro__img"></div>
			</div>
		</div>
	</section>

	<!-- SECTION 2: Público Objetivo -->
	<section class="mentoria-target" aria-label="<?php esc_attr_e('Público Objetivo', 'avance-template'); ?>">
		<div class="mentoria-target__container">
			<header class="mentoria-target__header">
				<h2 class="mentoria-target__title"><?php esc_html_e('¿Para quién es?', 'avance-template'); ?></h2>
			</header>
			<div class="mentoria-target__list">
				<div class="mentoria-target__item">
					<h3 class="mentoria-target__item-title"><?php esc_html_e('Fundadores y CEOs', 'avance-template'); ?></h3>
					<p class="mentoria-target__item-text"><?php esc_html_e('Que quieren escalar sin perder el foco en lo que importa.', 'avance-template'); ?></p>
				</div>
				<div class="mentoria-target__item">
					<h3 class="mentoria-target__item-title"><?php esc_html_e('Gerentes Comerciales', 'avance-template'); ?></h3>
					<p class="mentoria-target__item-text"><?php esc_html_e('En transición o buscando llevar a su equipo al siguiente nivel.', 'avance-template'); ?></p>
				</div>
				<div class="mentoria-target__item">
					<h3 class="mentoria-target__item-title"><?php esc_html_e('Ejecutivos Senior', 'avance-template'); ?></h3>
					<p class="mentoria-target__item-text"><?php esc_html_e('Que quieren acelerar su carrera con apoyo estratégico.', 'avance-template'); ?></p>
				</div>
				<div class="mentoria-target__item">
					<h3 class="mentoria-target__item-title"><?php esc_html_e('Emprendedores con Tracción', 'avance-template'); ?></h3>
					<p class="mentoria-target__item-text"><?php esc_html_e('Que ya tienen mercado pero necesitan sistema y estructura.', 'avance-template'); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- SECTION 3: Pilares de Trabajo -->
	<section class="mentoria-together" aria-label="<?php esc_attr_e('Pilares de Trabajo', 'avance-template'); ?>">
		<div class="mentoria-together__container">
			<div class="mentoria-together__features">
				<div class="mentoria-together__img"></div>
				<div class="mentoria-together__content">
					<header class="mentoria-together__header">
						<h2 class="mentoria-together__title"><?php esc_html_e('Qué trabajamos juntos', 'avance-template'); ?></h2>
					</header>
					<ol class="mentoria-together__list">
						<li class="mentoria-together__list-item"><?php esc_html_e('Claridad de visión y objetivos comerciales', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Construcción de propuesta de valor personal', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Gestión del equipo y delegación efectiva', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Toma de decisiones bajo incertidumbre', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Desarrollo de habilidades de negociación', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Gestión del tiempo y foco estratégico', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Marca personal y posicionamiento profesional', 'avance-template'); ?></li>
						<li class="mentoria-together__list-item"><?php esc_html_e('Revisión y ajuste del modelo de negocio', 'avance-template'); ?></li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<!-- Booking Section: Reserva de Sesión -->
	<section id="mentoria-booking" class="mentoria-reserva" aria-label="<?php esc_attr_e('Reserva tu Primera Sesión', 'avance-template'); ?>">
		<div class="mentoria-reserva__container">
			<div class="mentoria-reserva__main">
				<header class="mentoria-reserva__header">
					<div class="mentoria-reserva__kicker"><?php esc_html_e('ELIGE TU PLAN Y AGENDA', 'avance-template'); ?></div>
					<h2 class="mentoria-reserva__title"><?php esc_html_e('Reserva tu primera sesión', 'avance-template'); ?></h2>
				</header>

				<div id="mentoriaPlans" class="mentoria-reserva__plans-container"></div>

				<div class="mentoria-reserva__testimonial">
					<p class="mentoria-reserva__quote"><?php esc_html_e('"La mentoría fue un antes y un después. En 3 meses reorganicé mi equipo, redefiní mi propuesta de valor y cerré los mejores contratos de mi carrera."', 'avance-template'); ?></p>
					<div class="mentoria-reserva__who">
						<div class="mentoria-reserva__who-info">
							<div class="mentoria-reserva__name"><?php esc_html_e('Ana P.', 'avance-template'); ?></div>
							<div class="mentoria-reserva__role"><?php esc_html_e('Directora Comercial, Lima', 'avance-template'); ?></div>
						</div>
					</div>
				</div>
			</div>

			<div class="mentoria-reserva__right">
				<!-- Calendar Card -->
				<div class="mentoria-reserva__card mentoria-reserva__card--calendar" id="mentoriaCalendarCard">
					<div class="mentoria-reserva__card-kicker"><?php esc_html_e('AGENDA TU SESIÓN', 'avance-template'); ?></div>
					<div class="mentoria-reserva__card-subtitle" id="agendaSub"><?php esc_html_e('Pro · S/ 1200/mes', 'avance-template'); ?></div>

					<?php
					get_template_part('template-parts/calendar-agenda', null, [
						'host_title'    => 'Avance Empresarial',
						'host_meta'     => 'Sesión de diagnóstico · 30 min · Google Meet',
						'avatar_text'   => 'AE',
						'tag_text'      => 'Gratis',
						'cal_id'        => 'mentoriaAgenda',
						'timezone'      => 'Lima (GMT-5)',
					]);
					?>
				</div>

				<aside class="mentoria-reserva__sidebar">
					<!-- Form Card -->
					<div class="mentoria-reserva__card" id="mentoriaFormCard">
						<div class="mentoria-reserva__card-kicker"><?php esc_html_e('TUS DATOS', 'avance-template'); ?></div>

						<form class="mentoria-reserva__form">
							<div class="mentoria-reserva__form-row">
								<div class="mentoria-reserva__field">
									<label for="mentoriaFirstName"><?php esc_html_e('Nombre Completo *', 'avance-template'); ?></label>
									<input type="text" id="mentoriaFirstName" name="mentoria_first_name" placeholder="<?php esc_attr_e('Ej. Juan', 'avance-template'); ?>" required>
								</div>
								<div class="mentoria-reserva__field">
									<label for="mentoriaLastName"><?php esc_html_e('Apellido Completo *', 'avance-template'); ?></label>
									<input type="text" id="mentoriaLastName" name="mentoria_last_name" placeholder="<?php esc_attr_e('Ej. García Rodríguez', 'avance-template'); ?>" required>
								</div>
							</div>

							<div class="mentoria-reserva__form-row">
								<div class="mentoria-reserva__field">
									<label for="mentoriaEmail"><?php esc_html_e('Email *', 'avance-template'); ?></label>
									<input type="email" id="mentoriaEmail" name="mentoria_email" placeholder="<?php esc_attr_e('Ej. juan@empresa.com', 'avance-template'); ?>" required>
								</div>
								<div class="mentoria-reserva__field">
									<label for="mentoriaWhatsapp"><?php esc_html_e('WhatsApp *', 'avance-template'); ?></label>
									<input type="tel" id="mentoriaWhatsapp" name="mentoria_whatsapp" placeholder="<?php esc_attr_e('Ej. +51 987 654 321', 'avance-template'); ?>" required>
								</div>
							</div>

							<div class="mentoria-reserva__form-row">
								<div class="mentoria-reserva__field">
									<label for="mentoriaDesafio"><?php esc_html_e('¿Cuál es tu principal desafío ahora mismo?', 'avance-template'); ?></label>
									<textarea id="mentoriaDesafio" name="mentoria_desafio" rows="2" placeholder="<?php esc_attr_e('Ej. Necesito organizar mejor mi equipo o mejorar la propuesta de valor...', 'avance-template'); ?>" required></textarea>
								</div>
							</div>

							<button type="submit" class="mentoria-reserva__submit" id="mentoriaSubmitBtn"><?php esc_html_e('Confirmar reserva · S/ 1200/mes →', 'avance-template'); ?></button>
							<button type="button" class="mentoria-reserva__back-btn" id="mentoriaBackBtn"><?php esc_html_e('Volver al calendario', 'avance-template'); ?></button>
						</form>
						<div class="mentoria-reserva__note"><?php esc_html_e('Pago seguro. Confirmación inmediata por email y WhatsApp.', 'avance-template'); ?></div>
					</div>

					<!-- WhatsApp Fallback Button -->
					<button class="mentoria-reserva__whatsapp-btn">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="<?php esc_attr_e('WhatsApp', 'avance-template'); ?>" width="16" height="16">
						<?php esc_html_e('Prefiero consultar por WhatsApp primero', 'avance-template'); ?>
					</button>
				</aside>
			</div>
		</div>
</section>

	<!-- FAQ Section: Preguntas Frecuentes -->
	<section class="mentoria-faq" aria-label="<?php esc_attr_e('Preguntas Frecuentes', 'avance-template'); ?>">
		<div class="mentoria-faq__container">
			<header class="mentoria-faq__header">
				<h2 class="mentoria-faq__title"><?php esc_html_e('Preguntas Frecuentes', 'avance-template'); ?></h2>
			</header>
			<div class="mentoria-faq__list">
				<div class="mentoria-faq__item">
					<button class="mentoria-faq__question">
						<span class="mentoria-faq__text"><?php esc_html_e('¿Puedo cancelar en cualquier momento?', 'avance-template'); ?></span>
						<svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<polyline points="6 9 12 15 18 9"></polyline>
						</svg>
					</button>
					<p class="mentoria-faq__answer"><?php esc_html_e('Sí, con 30 días de anticipación. Envía notificación formal a informes@avance-empresarial.com indicando que deseas cancelar tu plan de mentoría. No hay penalización si cancelas dentro de ese plazo. El acceso al sistema se suspenderá inmediatamente.', 'avance-template'); ?></p>
				</div>
				<div class="mentoria-faq__item">
					<button class="mentoria-faq__question">
						<span class="mentoria-faq__text"><?php esc_html_e('¿En qué horarios están disponibles las sesiones?', 'avance-template'); ?></span>
						<svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<polyline points="6 9 12 15 18 9"></polyline>
						</svg>
					</button>
					<p class="mentoria-faq__answer"><?php esc_html_e('Sesiones disponibles de 15:00 a 17:00 (3 PM a 5 PM), lunes a domingo. Reservas confirmadas mediante Calendly con 48 horas de anticipación. Cada sesión dura 30 minutos y se realiza vía Google Meet o presencial en Lima. Adaptamos horarios según tu disponibilidad dentro de este rango.', 'avance-template'); ?></p>
				</div>
				<div class="mentoria-faq__item">
					<button class="mentoria-faq__question">
						<span class="mentoria-faq__text"><?php esc_html_e('¿Las sesiones son presenciales u online?', 'avance-template'); ?></span>
						<svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<polyline points="6 9 12 15 18 9"></polyline>
						</svg>
					</button>
					<p class="mentoria-faq__answer"><?php esc_html_e('Principalmente virtual vía Google Meet (15:00-17:00). Disponible lunes a domingo, 30 minutos por sesión. Presencial en Santiago de Surco es opcional: consulta disponibilidad escribiendo a informes@avance-empresarial.com. La mayoría de clientes elige online por flexibilidad y acceso inmediato.', 'avance-template'); ?></p>
				</div>
				<div class="mentoria-faq__item">
					<button class="mentoria-faq__question">
						<span class="mentoria-faq__text"><?php esc_html_e('¿Qué pasa si no puedo asistir a una sesión?', 'avance-template'); ?></span>
						<svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<polyline points="6 9 12 15 18 9"></polyline>
						</svg>
					</button>
					<p class="mentoria-faq__answer"><?php esc_html_e('Reprogramamos sin costo si notificas con mínimo 48 horas de anticipación. Contacta a informes@avance-empresarial.com o ajusta directamente en Calendly. Si cancelas con menos de 48 horas, la sesión se considera utilizada. Contamos con disponibilidad en horarios 15:00-17:00 (3 PM-5 PM), lunes a domingo.', 'avance-template'); ?></p>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
