<?php

/**
 * Template Name: Libro
 * Template Post Type: page
 *
 * Página de libro
 *
 * @package Avance_Template
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="libro-main">
	<div class="libro-wrap">
		<!-- Sidebar -->
		<aside class="libro-sidebar">
			<img class="libro-cover" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/PlaceholderImg2.png'); ?>" alt="<?php esc_attr_e('Portada del libro Avance Comercial', 'avance-template'); ?>">

			<a href="#" class="libro-btn-primary animate-on-scroll" data-animate><?php esc_html_e('Comprar en Amazon', 'avance-template'); ?></a>
			<div class="libro-hint"><?php esc_html_e('Enlace directo Amazon', 'avance-template'); ?></div>

			<div class="libro-row-2">
				<button class="libro-btn-small animate-on-scroll" data-animate><?php esc_html_e('Kindle', 'avance-template'); ?></button>
				<button class="libro-btn-small animate-on-scroll" data-animate><?php esc_html_e('Tapa blanda', 'avance-template'); ?></button>
			</div>

			<div class="libro-mini-label"><?php esc_html_e('¿Quieres aplicar el método?', 'avance-template'); ?></div>

			<a href="#" class="libro-btn-secondary animate-on-scroll" data-animate>
				<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
				<?php esc_html_e('Escribir al consultor', 'avance-template'); ?>
			</a>
			<a href="#" class="libro-btn-secondary animate-on-scroll" data-animate><?php esc_html_e('Ir a agendar reunión', 'avance-template'); ?></a>

			<div class="libro-rating-section animate-on-scroll" data-animate>
				<div class="libro-rating">
					<span class="libro-stars">★★★★★</span>
					<span class="libro-rating-value">4.9</span>
				</div>
				<div class="libro-reviews"><?php esc_html_e('+48 reseñas verificadas en Amazon', 'avance-template'); ?></div>
			</div>
		</aside>

		<!-- Content -->
		<div class="libro-content">
			<!-- Header -->
			<div class="libro-header animate-on-scroll" data-animate>
				<div class="libro-kicker"><?php esc_html_e('MI LIBRO', 'avance-template'); ?></div>
				<h1 class="libro-title"><?php esc_html_e('Avance Comercial: El método para transformar tu estrategia de ventas', 'avance-template'); ?></h1>
			</div>

			<!-- Meta Information -->
			<div class="libro-meta animate-on-scroll" data-animate>
				<div class="libro-meta-item">
					<span class="libro-meta-label"><?php esc_html_e('PÁGINAS', 'avance-template'); ?></span>
					<span class="libro-meta-value">248</span>
				</div>
				<div class="libro-meta-item">
					<span class="libro-meta-label"><?php esc_html_e('EDICIÓN', 'avance-template'); ?></span>
					<span class="libro-meta-value">2024</span>
				</div>
				<div class="libro-meta-item">
					<span class="libro-meta-label"><?php esc_html_e('IDIOMA', 'avance-template'); ?></span>
					<span class="libro-meta-value"><?php esc_html_e('Español', 'avance-template'); ?></span>
				</div>
			</div>

			<!-- Description -->
			<p class="libro-description animate-on-scroll" data-animate><?php esc_html_e('Una guía práctica para ejecutivos y emprendedores que quieren construir un proceso comercial sólido, escalable y orientado a resultados. Sin teoría vacía: cada capítulo incluye herramientas aplicables desde el primer día.', 'avance-template'); ?></p>

			<!-- Chapters Section -->
			<div class="libro-chapters-section animate-on-scroll" data-animate>
				<div class="libro-section-label"><?php esc_html_e('QUÉ ENCONTRARÁS', 'avance-template'); ?></div>
				<ol class="libro-chapters">
					<li class="libro-chapter animate-on-scroll" data-animate><?php esc_html_e('Diagnóstico de tu situación comercial actual', 'avance-template'); ?></li>
					<li class="libro-chapter animate-on-scroll" data-animate><?php esc_html_e('Construcción de propuesta de valor diferenciada', 'avance-template'); ?></li>
					<li class="libro-chapter animate-on-scroll" data-animate><?php esc_html_e('Diseño del proceso comercial paso a paso', 'avance-template'); ?></li>
					<li class="libro-chapter animate-on-scroll" data-animate><?php esc_html_e('Gestión y motivación de equipos de ventas', 'avance-template'); ?></li>
					<li class="libro-chapter animate-on-scroll" data-animate><?php esc_html_e('Métricas y KPIs que importan de verdad', 'avance-template'); ?></li>
					<li class="libro-chapter animate-on-scroll" data-animate><?php esc_html_e('Plan de implementación en 90 días', 'avance-template'); ?></li>
				</ol>
			</div>

			<!-- Quote Section -->
			<div class="libro-quote animate-on-scroll" data-animate>
				<p class="libro-quote-text"><?php esc_html_e('"El libro que ojalá hubiera tenido cuando empecé. Práctico, directo y con herramientas que funcionan."', 'avance-template'); ?></p>
				<div class="libro-quote-author"><?php esc_html_e('— Lector Amazon ★★★★★', 'avance-template'); ?></div>
			</div>

			<!-- CTA Section -->
			<div class="libro-cta-band animate-on-scroll" data-animate>
				<div class="libro-cta-title"><?php esc_html_e('¿Quieres implementar el método en tu empresa?', 'avance-template'); ?></div>
				<div class="libro-cta-actions">
					<a href="#" class="libro-btn libro-btn--primary animate-on-scroll" data-animate><?php esc_html_e('Agendar consultoría', 'avance-template'); ?></a>
					<a href="#" class="libro-btn libro-btn--secondary animate-on-scroll" data-animate>
				<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
				<?php esc_html_e('Hablar por WhatsApp', 'avance-template'); ?>
			</a>
					<a href="#" class="libro-btn libro-btn--secondary animate-on-scroll" data-animate><?php esc_html_e('Ir al Diagnóstico Gratuito', 'avance-template'); ?></a>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
