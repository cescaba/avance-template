<?php
/**
 * Template Name: Página de Inicio
 * Template Post Type: page
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
		<div class="home-hero__bg">
			<div class="home-hero__container">
				<div class="home-hero__top">
					<p class="home-hero__label"><?php esc_html_e('Consultoría · Capacitación · Mentoría', 'avance-template'); ?></p>
					<h1 class="home-hero__title"><?php esc_html_e('Transforma tu empresa con estrategia y acción real', 'avance-template'); ?></h1>
				</div>
				<div class="home-hero__bottom">
					<p class="home-hero__description"><?php esc_html_e('Capacitación ejecutiva, consultoría comercial y mentoría para líderes que quieren resultados concretos en Lima y el Perú.', 'avance-template'); ?></p>
					<div class="home-hero__content">
						<a href="<?php echo esc_url(get_permalink(get_page_by_path('diagnostico')) . '#diagnostico-quiz'); ?>" class="home-hero__btn-diag"><?php esc_html_e('Diagnóstico gratuito', 'avance-template'); ?></a>
						<a href="#scheduling-section" class="home-hero__btn-consul"><?php esc_html_e('Agendar consulta', 'avance-template'); ?></a>
						<button type="button" class="home-hero__btn-link" id="open-pdf-modal" onclick="document.getElementById('pf-modal-overlay').classList.remove('pf-overlay--hidden'); document.body.style.overflow = 'hidden';">
							<?php esc_html_e('Descargar ebook', 'avance-template'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Features Section -->
	<section class="home-features" aria-label="<?php esc_attr_e('Características', 'avance-template'); ?>">
		<div class="home-features__container">
			<div class="home-features__item">
				<span class="home-features__stat counter" data-value="50" data-suffix="+">0</span>
				<p class="home-features__text"><?php esc_html_e('Empresas asesoradas', 'avance-template'); ?></p>
			</div>
			<div class="home-features__item">
				<span class="home-features__stat counter" data-value="35" data-suffix="+">0</span>
				<p class="home-features__text"><?php esc_html_e('Años de experiencia', 'avance-template'); ?></p>
			</div>
			<div class="home-features__item">
				<span class="home-features__stat counter" data-value="100" data-suffix="%">0</span>
				<p class="home-features__text"><?php esc_html_e('Clientes satisfechos', 'avance-template'); ?></p>
			</div>
		</div>
	</section>
	<!-- Services Section -->
	<section class="home-services" id="home-services" aria-label="<?php esc_attr_e('Servicios', 'avance-template'); ?>">
		<div class="home-services__container">
			<header class="home-services__header">
				<h2 class="home-services__title"><?php esc_html_e('Servicios', 'avance-template'); ?></h2>
				<p class="home-services__subtitle"><?php esc_html_e('¿En qué te puedo ayudar?', 'avance-template'); ?></p>
			</header>
			<div class="home-services__list">
				<div class="home-services__item">
					<div class="home-services__badge"><?php esc_html_e('01', 'avance-template'); ?></div>
					<h3 class="home-services__item-title"><?php esc_html_e('Capacitación', 'avance-template'); ?></h3>
					<p class="home-services__item-text"><?php esc_html_e('Programas ejecutivos y seminarios de alto impacto. In-company o formato abierto. Metodología experiencial.', 'avance-template'); ?></p>
					<div class="home-services__actions">
						<a href="<?php echo esc_url(get_permalink(get_page_by_path('mentorias')) . '#mentoria-booking'); ?>" class="home-services__btn"><?php esc_html_e('Consultar programa', 'avance-template'); ?></a>
					</div>
				</div>
				<div class="home-services__item">
					<div class="home-services__badge"><?php esc_html_e('02', 'avance-template'); ?></div>
					<h3 class="home-services__item-title"><?php esc_html_e('Consultoría Comercial', 'avance-template'); ?></h3>
					<p class="home-services__item-text"><?php esc_html_e('Diagnóstico y estrategia orientada a resultados. Acompañamiento en la implementación del proceso comercial.', 'avance-template'); ?></p>
					<div class="home-services__actions">
						<a href="<?php echo esc_url(get_permalink(get_page_by_path('mentorias')) . '#mentoria-booking'); ?>" class="home-services__btn"><?php esc_html_e('Consultar programa', 'avance-template'); ?></a>
					</div>
				</div>
				<div class="home-services__item">
					<div class="home-services__badge"><?php esc_html_e('03', 'avance-template'); ?></div>
					<h3 class="home-services__item-title"><?php esc_html_e('Mentoría 1:1', 'avance-template'); ?></h3>
					<p class="home-services__item-text"><?php esc_html_e('Mentoría personalizada para ejecutivos y emprendedores en búsqueda de crecimiento.', 'avance-template'); ?></p>
					<div class="home-services__actions">
						<a href="<?php echo esc_url(get_permalink(get_page_by_path('mentorias')) . '#mentoria-booking'); ?>" class="home-services__btn"><?php esc_html_e('Consultar programa', 'avance-template'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Process Section -->
	<section class="home-process" aria-label="<?php esc_attr_e('Proceso', 'avance-template'); ?>">
		<div class="home-process__container">
			<header class="home-process__header">
				<h2 class="home-process__title"><?php esc_html_e('Nuestro Proceso', 'avance-template'); ?></h2>
				<p class="home-process__subtitle"><?php esc_html_e('Cómo trabajamos juntos', 'avance-template'); ?></p>
			</header>
			<div class="home-process__list">
				<div class="home-process__item">
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
				<div class="home-process__item">
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
				<div class="home-process__item">
					<div class="home-process__badge"><?php esc_html_e('03', 'avance-template'); ?></div>
					<div class="home-process__content">
						<h3 class="home-process__item-title"><?php esc_html_e('Implementación', 'avance-template'); ?></h3>
						<p class="home-process__item-text"><?php esc_html_e('Acompañamiento continuo para garantizar resultados', 'avance-template'); ?></p>
					</div>
				</div>
			</div>
			<div class="home-process__cta">
				<div class="home-process__cta-left">
					<h3 class="home-process__cta-title"><?php esc_html_e('¿Listo para empezar el proceso?', 'avance-template'); ?></h3>
					<p class="home-process__cta-texto"><?php esc_html_e('Primera sesión sin costo · Sin compromiso', 'avance-template'); ?></p>
				</div>
				<div class="home-process__cta-right">
					<a href="#scheduling-section" class="home-process__cta-grat"><?php esc_html_e('Agendar sesión gratuita', 'avance-template'); ?></a>
				</div>
			</div>
		</div>
	</section>
	<!-- Testimonials Section -->
	<section class="home-testimonials" id="home-testimonials" aria-label="<?php esc_attr_e('Testimonios', 'avance-template'); ?>">
		<div class="home-testimonials__container">
			<header class="home-testimonials__header">
				<h2 class="home-testimonials__title"><?php esc_html_e('Testimonios', 'avance-template'); ?></h2>
				<p class="home-testimonials__subtitle"><?php esc_html_e('Lo que dicen nuestros clientes', 'avance-template'); ?></p>
			</header>
			<div class="home-testimonials__grid">
				<article class="home-testimonials__item">
					<p class="home-testimonials__text"><?php esc_html_e('“Este libro presenta una visión práctica y estructurada de la gestión comercial, integrando cultura, estrategia, procesos y ejecución. Una guía valiosa para líderes que buscan construir equipos comerciales sólidos, medibles y orientados a resultados sostenibles.”"', 'avance-template'); ?></p>
					<div class="home-testimonials__author">
						<div class="home-testimonials__author-content">
							<h4 class="home-testimonials__name"><?php esc_html_e('Oscar Lobatón Soragastua', 'avance-template'); ?></h4>
							<p class="home-testimonials__role"><?php esc_html_e('Gerente General — B. Braun Medical Perú S.A.', 'avance-template'); ?></p>
						</div>
					</div>
				</article>
				<article class="home-testimonials__item">
					<p class="home-testimonials__text"><?php esc_html_e('“Luis Bailly transforma la estrategia comercial en un sistema claro, práctico y aplicable. Su enfoque conecta estrategia y ejecución para construir organizaciones comerciales más sólidas, coherentes y capaces de generar resultados sostenibles.”', 'avance-template'); ?></p>
					<div class="home-testimonials__author">
						<div class="home-testimonials__author-content">
							<h4 class="home-testimonials__name"><?php esc_html_e('Lewonardo Morales', 'avance-template'); ?></h4>
							<p class="home-testimonials__role"><?php esc_html_e('Gerente General de Carmar', 'avance-template'); ?></p>
						</div>
					</div>
				</article>
			</div>
		</div>
	</section>
	<!-- CTA Section -->
	<section class="home-cta" id="home-cta" aria-label="<?php esc_attr_e('Llamada a la Acción', 'avance-template'); ?>">
		<div class="home-cta__container">
			<p class="home-cta__text"><?php esc_html_e('¿Quieres resultados similares para tu empresa?', 'avance-template'); ?></p>
			<div class="home-cta__buttons">
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('diagnostico')) . '#diagnosticoQuizView'); ?>" class="home-cta__btn-diag"><?php esc_html_e('Empezar con diagnóstico gratuito', 'avance-template'); ?></a>
			</div>
		</div>
	</section>
	<!-- Dark Band Section -->
	<section class="home-dark-band" id="home-dark-band" aria-label="<?php esc_attr_e('Sección Destacada', 'avance-template'); ?>">
		<div class="home-dark-band__bg" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Rectangle 5.png'); ?>')">
			<div class="home-dark-band__container">
				<h2 class="home-dark-band__title"><?php esc_html_e('¿Sabes cuánto puede estar perdiendo tu empresa por no tener estrategia comercial?', 'avance-template'); ?></h2>
				<div class="home-dark-band__actions">
					<a href="<?php echo esc_url(get_permalink(get_page_by_path('diagnostico')) . '#diagnostico-quiz'); ?>" class="home-dark-band__btn-primary"><?php esc_html_e('Hacer el diagnóstico ahora es gratis', 'avance-template'); ?></a>
					<a href="<?php echo esc_url(AVANCE_WHATSAPP_URL); ?>" class="home-dark-band__btn-secondary" <?php echo AVANCE_WHATSAPP_ATTRS; ?>>
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-dark-band__icon" aria-hidden="true">
						<?php esc_html_e('Hablar por WhatsApp ahora', 'avance-template'); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- Form Section -->
	<?php get_template_part('template-parts/form-section'); ?>
	<?php get_template_part('template-parts/scheduling-section'); ?>
</main>
<?php get_footer();

