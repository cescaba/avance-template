<?php

/**
 * Template: Página de Inicio
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="site-main">

	<!-- Hero Section -->
	<section class="home-hero" aria-label="<?php esc_attr_e('Sección Hero', 'avance-template'); ?>">
		<div class="home-hero__bg" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Rectangle 3.png'); ?>')">
			<div class="home-hero__container">
				<p class="home-hero__label"><?php esc_html_e('Consultoría · Capacitación · Mentoría', 'avance-template'); ?></p>
				<h1 class="home-hero__title"><?php esc_html_e('Transforma tu empresa con estrategia y acción real', 'avance-template'); ?></h1>
				<p class="home-hero__description"><?php esc_html_e('Capacitación ejecutiva, consultoría comercial y mentoría para líderes que quieren resultados concretos en Lima y el Perú.', 'avance-template'); ?></p>
				<div class="home-hero__content">
					<a href="#" class="home-hero__btn-diag"><?php esc_html_e('Diagnóstico gratuito', 'avance-template'); ?></a>
					<a href="#" class="home-hero__btn-consul"><?php esc_html_e('Agendar consulta', 'avance-template'); ?></a>
					<a href="#" class="home-hero__btn-link">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-hero__btn-link-icon" aria-hidden="true">
						<?php esc_html_e('Escribir ahora', 'avance-template'); ?>
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Section -->
	<section class="home-features" aria-label="<?php esc_attr_e('Características', 'avance-template'); ?>">
		<div class="home-features__container animate-on-scroll" data-animate>
			<div class="home-features__item">
				<span class="home-features__stat counter" data-animate data-value="50" data-suffix="+">0</span>
				<p class="home-features__text"><?php esc_html_e('Empresas asesoradas', 'avance-template'); ?></p>
			</div>
			<div class="home-features__item">
				<span class="home-features__stat counter" data-animate data-value="35" data-suffix="+">0</span>
				<p class="home-features__text"><?php esc_html_e('Años de experiencia', 'avance-template'); ?></p>
			</div>
			<div class="home-features__item">
				<span class="home-features__stat counter" data-animate data-value="100" data-suffix="%">0</span>
				<p class="home-features__text"><?php esc_html_e('Clientes satisfechos', 'avance-template'); ?></p>
			</div>
		</div>
	</section>

	<!-- Services Section -->
	<section class="home-services" id="home-services" aria-label="<?php esc_attr_e('Servicios', 'avance-template'); ?>">
		<div class="home-services__container">
			<header class="home-services__header animate-on-scroll" data-animate>
				<h2 class="home-services__title"><?php esc_html_e('Servicios', 'avance-template'); ?></h2>
				<p class="home-services__subtitle"><?php esc_html_e('¿En qué te puedo ayudar?', 'avance-template'); ?></p>
			</header>

			<div class="home-services__list">
				<div class="home-services__item animate-on-scroll" data-animate>
					<div class="home-services__badge"><?php esc_html_e('01', 'avance-template'); ?></div>
					<h3 class="home-services__item-title"><?php esc_html_e('Capacitación', 'avance-template'); ?></h3>
					<p class="home-services__item-text"><?php esc_html_e('Programas ejecutivos y seminarios de alto impacto. In-company o formato abierto. Metodología experiencial.', 'avance-template'); ?></p>
					<div class="home-services__actions">
						<a href="#" class="home-services__btn"><?php esc_html_e('Consultar programa', 'avance-template'); ?></a>
						<a href="#" class="home-services__link"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-hero__btn-link-icon">
							<?php esc_html_e('Preguntar por WhatsApp', 'avance-template'); ?>
						</a>
					</div>
				</div>

				<div class="home-services__item animate-on-scroll" data-animate>
					<div class="home-services__badge"><?php esc_html_e('02', 'avance-template'); ?></div>
					<h3 class="home-services__item-title"><?php esc_html_e('Consultoría Comercial', 'avance-template'); ?></h3>
					<p class="home-services__item-text"><?php esc_html_e('Diagnóstico y estrategia orientada a resultados. Acompañamiento en la implementación del proceso comercial.', 'avance-template'); ?></p>
					<div class="home-services__actions">
						<a href="#" class="home-services__btn"><?php esc_html_e('Consultar programa', 'avance-template'); ?></a>
						<a href="#" class="home-services__link"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-hero__btn-link-icon">
							<?php esc_html_e('Preguntar por WhatsApp', 'avance-template'); ?>
						</a>
					</div>
				</div>

				<div class="home-services__item animate-on-scroll" data-animate>
					<div class="home-services__badge"><?php esc_html_e('03', 'avance-template'); ?></div>
					<h3 class="home-services__item-title"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></h3>
					<p class="home-services__item-text"><?php esc_html_e('Mentoría personalizada para ejecutivos y emprendedores en búsqueda de crecimiento.', 'avance-template'); ?></p>
					<div class="home-services__actions">
						<a href="#" class="home-services__btn"><?php esc_html_e('Consultar programa', 'avance-template'); ?></a>
						<a href="#" class="home-services__link"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-hero__btn-link-icon">
							<?php esc_html_e('Preguntar por WhatsApp', 'avance-template'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Process Section -->
	<section class="home-process" aria-label="<?php esc_attr_e('Proceso', 'avance-template'); ?>">
		<div class="home-process__container">
			<header class="home-process__header animate-on-scroll" data-animate>
				<h2 class="home-process__title"><?php esc_html_e('Nuestro Proceso', 'avance-template'); ?></h2>
				<p class="home-process__subtitle"><?php esc_html_e('Cómo trabajamos juntos', 'avance-template'); ?></p>
			</header>

			<div class="home-process__list">
				<div class="home-process__item animate-on-scroll" data-animate>
					<div class="home-process__badge"><?php esc_html_e('01', 'avance-template'); ?></div>
					<div class="home-process__line">
						<div class="home-process__dots"></div>
						<div class="home-process__arrow">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9 6l6 6-6 6" stroke="#6fa593" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
					</div>
					<div class="home-process__content">
						<h3 class="home-process__item-title"><?php esc_html_e('Diagnóstico', 'avance-template'); ?></h3>
						<p class="home-process__item-text"><?php esc_html_e('Evaluamos tu situación actual: proceso comercial, equipo y resultados.', 'avance-template'); ?></p>
					</div>
				</div>

				<div class="home-process__item animate-on-scroll" data-animate>
					<div class="home-process__badge"><?php esc_html_e('02', 'avance-template'); ?></div>
					<div class="home-process__line">
						<div class="home-process__dots"></div>
						<div class="home-process__arrow">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9 6l6 6-6 6" stroke="#6fa593" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
					</div>
					<div class="home-process__content">
						<h3 class="home-process__item-title"><?php esc_html_e('Plan a medida', 'avance-template'); ?></h3>
						<p class="home-process__item-text"><?php esc_html_e('Diseñamos un programa específico: capacitación, consultoría o mentoría según tu necesidad.', 'avance-template'); ?></p>
					</div>
				</div>

				<div class="home-process__item animate-on-scroll" data-animate>
					<div class="home-process__badge"><?php esc_html_e('03', 'avance-template'); ?></div>
					<div class="home-process__content">
						<h3 class="home-process__item-title"><?php esc_html_e('Implementación', 'avance-template'); ?></h3>
						<p class="home-process__item-text"><?php esc_html_e('Acompañamiento continuo para garantizar resultados', 'avance-template'); ?></p>
					</div>
				</div>
			</div>

			<div class="home-process__cta animate-on-scroll" data-animate>
				<div class="home-process__cta-left">
					<h3 class="home-process__cta-title"><?php esc_html_e('¿Listo para empezar el proceso?', 'avance-template'); ?></h3>
					<p class="home-process__cta-texto"><?php esc_html_e('Primera sesión sin costo · Sin compromiso', 'avance-template'); ?></p>
				</div>
				<div class="home-process__cta-right">
					<a href="#" class="home-process__cta-grat"><?php esc_html_e('Agendar sesión gratuita', 'avance-template'); ?></a>
					<a href="#" class="home-process__cta-what"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-hero__btn-link-icon">
						<?php esc_html_e('Hablar por WhatsApp', 'avance-template'); ?>
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Testimonials Section -->
	<section class="home-testimonials" id="home-testimonials" aria-label="<?php esc_attr_e('Testimonios', 'avance-template'); ?>">
		<div class="home-testimonials__container">
			<header class="home-testimonials__header animate-on-scroll" data-animate>
				<h2 class="home-testimonials__title"><?php esc_html_e('Testimonios', 'avance-template'); ?></h2>
				<p class="home-testimonials__subtitle"><?php esc_html_e('Lo que dicen nuestros clientes', 'avance-template'); ?></p>
			</header>

			<div class="home-testimonials__grid">
				<article class="home-testimonials__item animate-on-scroll" data-animate>
					<p class="home-testimonials__text"><?php esc_html_e('"El diagnóstico comercial nos permitió identificar puntos ciegos que frenaban nuestras ventas. En 3 meses duplicamos el ticket promedio."', 'avance-template'); ?></p>
					<div class="home-testimonials__author">
						<div class="home-testimonials__author-icon">
							<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/quote-icon.png'); ?>" alt="" class="home-testimonials__icon" aria-hidden="true">
						</div>
						<div class="home-testimonials__author-content">
							<h4 class="home-testimonials__name"><?php esc_html_e('Alfred M.', 'avance-template'); ?></h4>
							<p class="home-testimonials__role"><?php esc_html_e('Gerente Comercial — Retail, Lima', 'avance-template'); ?></p>
						</div>
					</div>
				</article>

				<article class="home-testimonials__item animate-on-scroll" data-animate>
					<p class="home-testimonials__text"><?php esc_html_e('"El diagnóstico comercial nos permitió identificar puntos ciegos que frenaban nuestras ventas. En 3 meses duplicamos el ticket promedio."', 'avance-template'); ?></p>
					<div class="home-testimonials__author">
						<div class="home-testimonials__author-icon">
							<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/quote-icon.png'); ?>" alt="" class="home-testimonials__icon" aria-hidden="true">
						</div>
						<div class="home-testimonials__author-content">
							<h4 class="home-testimonials__name"><?php esc_html_e('Jerico M.', 'avance-template'); ?></h4>
							<p class="home-testimonials__role"><?php esc_html_e('Gerente Comercial — Retail, Lima', 'avance-template'); ?></p>
						</div>
					</div>
				</article>
			</div>
		</div>
	</section>

	<!-- CTA Section -->
	<section class="home-cta" id="home-cta" aria-label="<?php esc_attr_e('Llamada a la Acción', 'avance-template'); ?>">
		<div class="home-cta__container animate-on-scroll" data-animate>
			<p class="home-cta__text"><?php esc_html_e('¿Quieres resultados similares para tu empresa?', 'avance-template'); ?></p>
			<div class="home-cta__buttons">
				<a href="#" class="home-cta__btn-diag"><?php esc_html_e('Empezar con diagnóstico gratuito', 'avance-template'); ?></a>
				<a href="#" class="home-cta__btn-consul">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-hero__btn-link-icon">
					<?php esc_html_e('Escribir al WhatsApp', 'avance-template'); ?>
				</a>
			</div>
		</div>
	</section>

	<!-- Clients Logos Section -->
	<section class="home-clients" id="home-clients" aria-label="<?php esc_attr_e('Nuestros Clientes', 'avance-template'); ?>">
		<div class="home-clients__container animate-on-scroll" data-animate>
			<p class="home-clients__title"><?php esc_html_e('Nuestros clientes', 'avance-template'); ?></p>
			<div class="home-clients__grid">
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
				<div class="home-clients__logo"></div>
			</div>
		</div>
	</section>

	<!-- Dark Band Section -->
	<section class="home-dark-band" id="home-dark-band" aria-label="<?php esc_attr_e('Sección Destacada', 'avance-template'); ?>">
		<div class="home-dark-band__bg" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Rectangle 5.png'); ?>')">
			<div class="home-dark-band__container animate-on-scroll" data-animate>
				<h2 class="home-dark-band__title"><?php esc_html_e('¿Sabes cuánto puede estar perdiendo tu empresa por no tener estrategia comercial?', 'avance-template'); ?></h2>
				<div class="home-dark-band__actions">
					<a href="#" class="home-dark-band__btn-primary"><?php esc_html_e('Hacer el diagnóstico ahora es gratis', 'avance-template'); ?></a>
					<a href="#" class="home-dark-band__btn-secondary">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-dark-band__icon" aria-hidden="true">
						<?php esc_html_e('Hablar por WhatsApp ahora', 'avance-template'); ?>
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Form Section -->
	<section class="home-form" id="home-form" aria-label="<?php esc_attr_e('Formulario de Contacto', 'avance-template'); ?>">
		<div class="home-form__container">
			<header class="home-form__header animate-on-scroll" data-animate>
				<p class="home-form__label"><?php esc_html_e('FORMULARIO MULTISERVICIO', 'avance-template'); ?></p>
				<h2 class="home-form__title"><?php esc_html_e('¿Listo para dar el siguiente paso?', 'avance-template'); ?></h2>
				<p class="home-form__description"><?php esc_html_e('Completa el formulario y me contacto en menos de 24h.', 'avance-template'); ?></p>
			</header>

			<form id="contacto-wsp-form" class="home-form__form" aria-label="<?php esc_attr_e('Formulario de contacto por WhatsApp', 'avance-template'); ?>">
				<?php wp_nonce_field('avance_contact_form', 'nonce', false); ?>
				<div class="home-form__row animate-on-scroll" data-animate>
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

				<div class="home-form__row animate-on-scroll" data-animate>
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
						<select
							id="contacto_wsp_asunto"
							name="contacto_wsp_asunto"
							class="home-form__input"
							required
							aria-required="true">
							<option value=""><?php esc_html_e('Selecciona una opción...', 'avance-template'); ?></option>
							<option value="Capacitación"><?php esc_html_e('Capacitación', 'avance-template'); ?></option>
							<option value="Consultoría Comercial"><?php esc_html_e('Consultoría Comercial', 'avance-template'); ?></option>
							<option value="Mentoría 1:1"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></option>
							<option value="Otra consulta"><?php esc_html_e('Otra consulta', 'avance-template'); ?></option>
						</select>
					</div>
				</div>

				<div class="home-form__field home-form__field--full animate-on-scroll" data-animate>
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

				<div class="home-form__buttons animate-on-scroll" data-animate>
					<button type="submit" class="home-form__button animate-on-scroll" data-animate>
						<?php esc_html_e('Enviar mensaje', 'avance-template'); ?>
					</button>
					<a href="#" class="home-form__button--secondary animate-on-scroll" data-animate>
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-form__button-icon" aria-hidden="true">
						<?php esc_html_e('Escribir al WhatsApp', 'avance-template'); ?>
					</a>
				</div>
			</form>
		</div>
	</section>

	<!-- Scheduling Section -->
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
							<div class="contacto-agenda__note" id="contacto-agenda-note"></div>
						</div>
					</div>

					<button class="contacto-agenda__btn contacto-agenda__btn--primary contacto-agenda__btn--block animate-on-scroll" data-animate id="contacto-agenda-submit" disabled><?php esc_html_e('Agendar Reunión', 'avance-template'); ?></button>

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

</main>

<?php get_footer();
