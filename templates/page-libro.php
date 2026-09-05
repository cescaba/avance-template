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
			<a href="#" class="libro-btn-primary"><?php esc_html_e('Comprar en Amazon', 'avance-template'); ?></a>

			<p class="libro-cover-caption"><?php esc_html_e('Enlace directo: Compra por Amazon', 'avance-template'); ?></p>
			<p class="libro-cover-author"><?php esc_html_e('¿Quieres aplicar el método?', 'avance-template'); ?></p>


			<a href="#scheduling-section" class="libro-btn-secondary"><?php esc_html_e('Ir a agendar reunión', 'avance-template'); ?></a>

			<div class="libro-rating-section">
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
			<div class="libro-header">
				<div class="libro-kicker"><?php esc_html_e('MI LIBRO', 'avance-template'); ?></div>
				<h1 class="libro-title"><?php esc_html_e('Avance Comercial: El método para transformar tu estrategia de ventas', 'avance-template'); ?></h1>
			</div>

			<!-- Meta Information -->
			<div class="libro-meta">
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

			<p class="libro-description"><?php esc_html_e('Una guía práctica para ejecutivos y emprendedores que quieren construir un proceso comercial sólido, escalable y orientado a resultados. Sin teoría vacía: cada capítulo incluye herramientas aplicables desde el primer día.', 'avance-template'); ?></p>


			<div class="libro-route-section">
				<div class="libro-route-label"><?php esc_html_e('QUÉ APRENDERÁ EL LECTOR', 'avance-template'); ?></div>
				<h2 class="libro-route-title"><?php esc_html_e('La ruta de transformación', 'avance-template'); ?></h2>
				<p class="libro-route-description"><?php esc_html_e('Imagina este libro como un mapa de navegación comercial. Cada capítulo es una etapa del recorrido, diseñada para llevarte desde la identificación precisa del problema hasta la implementación de un sistema comercial integral.', 'avance-template'); ?></p>
			</div>


			<!-- Chapters Section -->
			<div class="libro-chapters-section">
				<div class="libro-section-label"><?php esc_html_e('Este es el itinerario del viaje:', 'avance-template'); ?></div>
				<div class="libro-chapters">
					<div class="libro-chapter">
						<h3 class="libro-chapter-title"><?php esc_html_e('Diagnóstico de tu situación comercial actual', 'avance-template'); ?></h3>
						<p class="libro-chapter-description"><?php esc_html_e('Evalúa dónde estás hoy: estructura de ventas, pipeline, problemas clave y oportunidades ocultas.', 'avance-template'); ?></p>
					</div>
					<div class="libro-chapter">
						<h3 class="libro-chapter-title"><?php esc_html_e('Construcción de propuesta de valor diferenciada', 'avance-template'); ?></h3>
						<p class="libro-chapter-description"><?php esc_html_e('Crea un posicionamiento único que resuene con tus clientes ideales y te diferencie de la competencia.', 'avance-template'); ?></p>
					</div>
					<div class="libro-chapter">
						<h3 class="libro-chapter-title"><?php esc_html_e('Diseño del proceso comercial paso a paso', 'avance-template'); ?></h3>
						<p class="libro-chapter-description"><?php esc_html_e('Implementa un funnel estructurado: prospectar, calificar, demostrar valor y cerrar con precisión.', 'avance-template'); ?></p>
					</div>
					<div class="libro-chapter">
						<h3 class="libro-chapter-title"><?php esc_html_e('Gestión y motivación de equipos de ventas', 'avance-template'); ?></h3>
						<p class="libro-chapter-description"><?php esc_html_e('Lidera, desarrolla talento y crea una cultura de alto rendimiento en tu equipo comercial.', 'avance-template'); ?></p>
					</div>
					<div class="libro-chapter">
						<h3 class="libro-chapter-title"><?php esc_html_e('Métricas y KPIs que importan de verdad', 'avance-template'); ?></h3>
						<p class="libro-chapter-description"><?php esc_html_e('Mide lo que realmente importa y usa datos para tomar decisiones estratégicas inteligentes.', 'avance-template'); ?></p>
					</div>
					<div class="libro-chapter">
						<h3 class="libro-chapter-title"><?php esc_html_e('Plan de implementación en 90 días', 'avance-template'); ?></h3>
						<p class="libro-chapter-description"><?php esc_html_e('Ejecuta todo lo aprendido con un roadmap práctico: semana a semana, acción a acción.', 'avance-template'); ?></p>
					</div>
				</div>
			</div>

			<p class="libro-subdescription"><?php esc_html_e('Cada etapa es un paso lógico en tu camino de transformación y crecimiento como líder empresarial. No hay atajos: dejarás de improvisar para gestionar con claridad y enfoque, llevando a tu equipo de la confusión a una efectividad sostenida.', 'avance-template'); ?></p>

			<!-- Quote Section -->
			<div class="libro-quote">
				<p class="libro-quote-text"><?php esc_html_e('“Este libro recoge años de experiencia, metodología y aprendizajes prácticos aplicables a cualquier organización B2B.”', 'avance-template'); ?></p>
				<div class="libro-quote-author"><?php esc_html_e('— Lector Amazon ★★★★★', 'avance-template'); ?></div>
			</div>

			<!-- CTA Section -->
			<div class="libro-cta-band">
				<div class="libro-cta-title"><?php esc_html_e('¿Quieres implementar el método en tu empresa?', 'avance-template'); ?></div>
				<div class="libro-cta-actions">
					<a href="#scheduling-section" class="libro-btn-primary"><?php esc_html_e('Agendar consultoría', 'avance-template'); ?></a>
					<a href="<?php echo esc_url(get_permalink(get_page_by_path('diagnostico')) . '#diagnosticoQuizView'); ?>" class="libro-btn-secondary"><?php esc_html_e('Ir al Diagnóstico Gratuito', 'avance-template'); ?></a>
				</div>
			</div>
		</div>
	</div>

	<!-- MOBILE 479px VERSION - Reorganized Layout -->
	<div class="libro-wrap-mobile">
		<!-- Top Section: Cover + Header + Meta (2 Columns) -->
		<div class="libro-header-section-mobile">
			<!-- Left Column: Cover -->
			<div class="libro-sidebar-mobile">
				<img class="libro-cover" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/LibroMobil.png'); ?>" alt="<?php esc_attr_e('Portada del libro Avance Comercial', 'avance-template'); ?>">
			</div>

			<!-- Right Column: Header + Meta -->
			<div class="libro-header-content-mobile">
				<div class="libro-header-mobile">
					<div class="libro-kicker"><?php esc_html_e('MI LIBRO', 'avance-template'); ?></div>
					<h1 class="libro-title"><?php esc_html_e('Avance Comercial: El método para transformar tu estrategia de ventas', 'avance-template'); ?></h1>
				</div>

				<div class="libro-meta-mobile">
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
			</div>
		</div>

		<!-- Full Width Below: Description + CTA -->
		<p class="libro-description"><?php esc_html_e('Una guía práctica para ejecutivos y emprendedores que quieren construir un proceso comercial sólido, escalable y orientado a resultados. Sin teoría vacía: cada capítulo incluye herramientas aplicables desde el primer día.', 'avance-template'); ?></p>

		<a href="#" class="libro-btn-primary-mobile"><?php esc_html_e('Comprar en Amazon', 'avance-template'); ?></a>

		<!-- Buttons & Rating Section -->
		<div class="libro-rating-section">
			<div class="libro-rating">
				<span class="libro-stars">★★★★★</span>
				<span class="libro-rating-value">4.9</span>
			</div>
			<div class="libro-reviews"><?php esc_html_e('+48 reseñas verificadas en Amazon', 'avance-template'); ?></div>
		</div>

		<!-- Route Section Mobile -->
		<div class="libro-route-section-mobile">
			<div class="libro-route-label-mobile"><?php esc_html_e('QUÉ APRENDERÁ EL LECTOR', 'avance-template'); ?></div>
			<h2 class="libro-route-title-mobile"><?php esc_html_e('La ruta de transformación', 'avance-template'); ?></h2>
			<p class="libro-route-description-mobile"><?php esc_html_e('Imagina este libro como un mapa de navegación comercial. Cada capítulo es una etapa del recorrido, diseñada para llevarte desde la identificación precisa del problema hasta la implementación de un sistema comercial integral.', 'avance-template'); ?></p>
		</div>

		<!-- Chapters Section Mobile -->
		<div class="libro-chapters-section-mobile">
			<div class="libro-section-label-mobile"><?php esc_html_e('Este es el itinerario del viaje:', 'avance-template'); ?></div>
			<div class="libro-chapters-mobile">
				<div class="libro-chapter-mobile">
					<h3 class="libro-chapter-title-mobile"><?php esc_html_e('Diagnóstico de tu situación comercial actual', 'avance-template'); ?></h3>
					<p class="libro-chapter-description-mobile"><?php esc_html_e('Evalúa dónde estás hoy: estructura de ventas, pipeline, problemas clave y oportunidades ocultas.', 'avance-template'); ?></p>
				</div>
				<div class="libro-chapter-mobile">
					<h3 class="libro-chapter-title-mobile"><?php esc_html_e('Construcción de propuesta de valor diferenciada', 'avance-template'); ?></h3>
					<p class="libro-chapter-description-mobile"><?php esc_html_e('Crea un posicionamiento único que resuene con tus clientes ideales y te diferencie de la competencia.', 'avance-template'); ?></p>
				</div>
				<div class="libro-chapter-mobile">
					<h3 class="libro-chapter-title-mobile"><?php esc_html_e('Diseño del proceso comercial paso a paso', 'avance-template'); ?></h3>
					<p class="libro-chapter-description-mobile"><?php esc_html_e('Implementa un funnel estructurado: prospectar, calificar, demostrar valor y cerrar con precisión.', 'avance-template'); ?></p>
				</div>
				<div class="libro-chapter-mobile">
					<h3 class="libro-chapter-title-mobile"><?php esc_html_e('Gestión y motivación de equipos de ventas', 'avance-template'); ?></h3>
					<p class="libro-chapter-description-mobile"><?php esc_html_e('Lidera, desarrolla talento y crea una cultura de alto rendimiento en tu equipo comercial.', 'avance-template'); ?></p>
				</div>
				<div class="libro-chapter-mobile">
					<h3 class="libro-chapter-title-mobile"><?php esc_html_e('Métricas y KPIs que importan de verdad', 'avance-template'); ?></h3>
					<p class="libro-chapter-description-mobile"><?php esc_html_e('Mide lo que realmente importa y usa datos para tomar decisiones estratégicas inteligentes.', 'avance-template'); ?></p>
				</div>
				<div class="libro-chapter-mobile">
					<h3 class="libro-chapter-title-mobile"><?php esc_html_e('Plan de implementación en 90 días', 'avance-template'); ?></h3>
					<p class="libro-chapter-description-mobile"><?php esc_html_e('Ejecuta todo lo aprendido con un roadmap práctico: semana a semana, acción a acción.', 'avance-template'); ?></p>
				</div>
			</div>
		</div>

		<p class="libro-subdescription-mobile"><?php esc_html_e('Cada etapa es un paso lógico en tu camino de transformación y crecimiento como líder empresarial. No hay atajos: dejarás de improvisar para gestionar con claridad y enfoque, llevando a tu equipo de la confusión a una efectividad sostenida.', 'avance-template'); ?></p>

		<!-- Quote Section -->
		<div class="libro-quote-mobile">
			<p class="libro-quote-text-mobile"><?php esc_html_e('"El libro que ojalá hubiera tenido cuando empecé. Práctico, directo y con herramientas que funcionan."', 'avance-template'); ?></p>
			<div class="libro-quote-author-mobile"><?php esc_html_e('— Lector Amazon ★★★★★', 'avance-template'); ?></div>
		</div>

		<!-- CTA Section -->
		<div class="libro-cta-band-mobile">
			<div class="libro-cta-title-mobile"><?php esc_html_e('¿Quieres implementar el método en tu empresa?', 'avance-template'); ?></div>
			<div class="libro-cta-actions-mobile">
				<a href="#scheduling-section" class="libro-btn-mobile libro-btn--primary-mobile"><?php esc_html_e('Agendar consultoría', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('diagnostico')) . '#diagnosticoQuizView'); ?>" class="libro-btn-mobile libro-btn--secondary-mobile"><?php esc_html_e('Ir al Diagnóstico Gratuito', 'avance-template'); ?></a>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
