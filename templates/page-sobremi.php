<?php

/**
 * Template Name: Sobre Mí
 * Template Post Type: page
 *
 * Página sobre mí
 *
 * @package Avance_Template
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="sobremi-main">

	<!-- Intro Section -->
	<section class="sobremi-intro" aria-label="<?php esc_attr_e('Introducción', 'avance-template'); ?>">
		<div class="sobremi-intro__container animate-on-scroll" data-animate>
			<!-- Content Group -->
			<div class="sobremi-intro__content">
				<div class="sobremi-intro__image">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/PlaceholderImg.png'); ?>" alt="<?php bloginfo('name'); ?>" class="sobremi-intro__image-img">
				</div>
				<div class="sobremi-intro__text">
					<ul class="sobremi-intro__list animate-on-scroll" data-animate>
						<li class="sobremi-intro__list-item animate-on-scroll" data-animate><?php esc_html_e('MBA — Universidad ESAN', 'avance-template'); ?></li>
						<li class="sobremi-intro__list-item animate-on-scroll" data-animate><?php esc_html_e('Consultor certificado', 'avance-template'); ?></li>
						<li class="sobremi-intro__list-item animate-on-scroll" data-animate><?php esc_html_e('+200 empresas asesoradas', 'avance-template'); ?></li>
						<li class="sobremi-intro__list-item animate-on-scroll" data-animate><?php esc_html_e('+15 años de experiencia', 'avance-template'); ?></li>
						<li class="sobremi-intro__list-item animate-on-scroll" data-animate><?php esc_html_e('Speaker internacional', 'avance-template'); ?></li>
					</ul>
				</div>
				<div class="sobremi-intro__buttom">
					<div class="sobremi-intro__buttons animate-on-scroll" data-animate>
						<a href="#" class="sobremi-intro__btn sobremi-intro__btn--primary animate-on-scroll" data-animate><?php esc_html_e('Agendar sesión', 'avance-template'); ?></a>
						<a href="#" class="sobremi-intro__btn sobremi-intro__btn--secondary animate-on-scroll" data-animate>
							<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="sobremi-intro__btn-icon" aria-hidden="true">
							<?php esc_html_e('Escribir por WhatsApp', 'avance-template'); ?>
						</a>
						<a href="#" class="sobremi-intro__btn sobremi-intro__btn--tertiary animate-on-scroll" data-animate><?php esc_html_e('Diagnóstico gratuito', 'avance-template'); ?></a>
					</div>
					<div class="sobremi-intro__socials animate-on-scroll" data-animate>
						<a href="https://linkedin.com" class="sobremi-intro__social-link animate-on-scroll" data-animate aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.39v-1.2h-2.94v8.37h3.09v-4.26c0-.85.62-1.84 1.24-1.84.93 0 1.25.86 1.25 1.84v4.26h3.04M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69-.93 0-1.69.76-1.69 1.69 0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77Z"/>
							</svg>
						</a>
						<a href="https://instagram.com" class="sobremi-intro__social-link animate-on-scroll" data-animate aria-label="Instagram" target="_blank" rel="noopener noreferrer">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4c0 3.2-2.6 5.8-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2m-.3 2c-2.1 0-3.8 1.7-3.8 3.8v8.4c0 2.1 1.7 3.8 3.8 3.8h8.4c2.1 0 3.8-1.7 3.8-3.8V7.8c0-2.1-1.7-3.8-3.8-3.8H7.5m9.6 1.5a1.5 1.5 0 0 1 0 3 1.5 1.5 0 0 1 0-3M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
							</svg>
						</a>
						<a href="https://youtube.com" class="sobremi-intro__social-link animate-on-scroll" data-animate aria-label="YouTube" target="_blank" rel="noopener noreferrer">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
								<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
							</svg>
						</a>
					</div>
				</div>
			</div>

			<!-- Features Group -->
			<div class="sobremi-intro__features">

				<!-- Stat Section -->
				<div class="sobremi-intro__stat animate-on-scroll" data-animate>
					<h2 class="sobremi-intro__stat-title"><?php esc_html_e('Sobre Mí', 'avance-template'); ?></h2>
					<h3 class="sobremi-intro__stat-subtitle"><?php esc_html_e('Consultor, mentor y speaker en estrategia comercial', 'avance-template'); ?></h3>

					<p class="sobremi-intro__stat-text"><?php esc_html_e('Soy fundador de Avance Empresarial, una firma peruana dedicada a transformar la gestión comercial de empresas a través de capacitación ejecutiva, consultoría estratégica y mentoría personalizada.', 'avance-template'); ?></p>

					<p class="sobremi-intro__stat-text"><?php esc_html_e('Mi enfoque combina alta formación académica con experiencia real en empresas de múltiples sectores. No enseño teoría: acompaño a los equipos hasta ver los resultados medibles en sus métricas.', 'avance-template'); ?></p>

					<blockquote class="sobremi-intro__stat-quote">
						<p class="sobremi-intro__stat-quote-text"><?php esc_html_e('"El aprendizaje es experiencia. Todo lo demás es información."', 'avance-template'); ?></p>
						<p class="sobremi-intro__stat-quote-author"><?php esc_html_e('— Albert Einstein', 'avance-template'); ?></p>
					</blockquote>
				</div>

				<!-- Skill Section (Timeline) -->
				<div class="sobremi-intro__skill animate-on-scroll" data-animate>
					<h2 class="sobremi-intro__skill-title"><?php esc_html_e('Trayectoria', 'avance-template'); ?></h2>

					<div class="sobremi-intro__timeline">
						<div class="sobremi-intro__timeline-item animate-on-scroll" data-animate>
							<span class="sobremi-intro__timeline-year">2024</span>
							<p class="sobremi-intro__timeline-text"><?php esc_html_e('Publicación del libro "Avance Comercial" — disponible en Amazon', 'avance-template'); ?></p>
						</div>
						<div class="sobremi-intro__timeline-item animate-on-scroll" data-animate>
							<span class="sobremi-intro__timeline-year">2022</span>
							<p class="sobremi-intro__timeline-text"><?php esc_html_e('Expansión a mentoría 1:1 y programas online para Latinoamérica', 'avance-template'); ?></p>
						</div>
						<div class="sobremi-intro__timeline-item animate-on-scroll" data-animate>
							<span class="sobremi-intro__timeline-year">2018</span>
							<p class="sobremi-intro__timeline-text"><?php esc_html_e('Consultor para empresas del sector financiero y retail', 'avance-template'); ?></p>
						</div>
						<div class="sobremi-intro__timeline-item animate-on-scroll" data-animate>
							<span class="sobremi-intro__timeline-year">2015</span>
							<p class="sobremi-intro__timeline-text"><?php esc_html_e('Fundación de Avance Empresarial en Lima, Perú', 'avance-template'); ?></p>
						</div>
						<div class="sobremi-intro__timeline-item animate-on-scroll" data-animate>
							<span class="sobremi-intro__timeline-year">2009</span>
							<p class="sobremi-intro__timeline-text"><?php esc_html_e('MBA — Universidad ESAN, Lima', 'avance-template'); ?></p>
						</div>
					</div>
				</div>

				<!-- Achievement Section (CTA) -->
				<div class="sobremi-intro__achievement animate-on-scroll" data-animate>
					<div class="sobremi-intro__achievement-header">
						<h2 class="sobremi-intro__achievement-title"><?php esc_html_e('¿Quieres trabajar conmigo?', 'avance-template'); ?></h2>
						<p class="sobremi-intro__achievement-subtitle"><?php esc_html_e('Capacitación · Consultoría · Mentoría', 'avance-template'); ?></p>
					</div>

					<div class="sobremi-intro__achievement-buttons">
						<a href="#" class="sobremi-intro__achievement-btn sobremi-intro__achievement-btn--primary"><?php esc_html_e('Agendar sesión', 'avance-template'); ?></a>
						<a href="#" class="sobremi-intro__achievement-btn sobremi-intro__achievement-btn--secondary">
							<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
							<?php esc_html_e('WhatsApp', 'avance-template'); ?>
						</a>
					</div>
				</div>

			</div>

		</div>
	</section>

</main>

<?php
get_footer();
