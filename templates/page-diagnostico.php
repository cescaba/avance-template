<?php

/**
 * Template Name: Diagnóstico
 * Template Post Type: page
 *
 * Página de diagnóstico
 *
 * @package Avance_Template
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="diagnostico-main">

	<!-- Hero Section -->
	<section class="diagnostico-hero" aria-label="<?php esc_attr_e('Sección Hero', 'avance-template'); ?>">
		<div class="diagnostico-hero__bg" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Rectangle 15.png'); ?>')">
			<div class="diagnostico-hero__container">
				<p class="diagnostico-hero__label"><?php esc_html_e('Diagnóstico comercial gratuito', 'avance-template'); ?></p>
				<h1 class="diagnostico-hero__title"><?php esc_html_e('¿Cuánto dinero está dejando sobre la mesa tu empresa?', 'avance-template'); ?></h1>
				<p class="diagnostico-hero__description"><?php esc_html_e('Responde 5 preguntas y recibe tu diagnóstico personalizado + ebook gratuito.', 'avance-template'); ?></p>
			</div>
		</div>
	</section>

	<!-- Quiz Section -->
	<section id="diagnostico-quiz" class="diagnostico-quiz" aria-label="<?php esc_attr_e('Quiz Diagnóstico', 'avance-template'); ?>">
		<div class="diagnostico-quiz__container">
			<div class="diagnostico-quiz__progress-track">
				<div class="diagnostico-quiz__progress-fill" id="diagnosticoProgressFill"></div>
			</div>

			<div id="diagnosticoQuizView" class="diagnostico-quiz__view">
				<div class="diagnostico-quiz__tag" id="diagnosticoQuestionTag"><?php esc_html_e('PREGUNTA 1 DE 5', 'avance-template'); ?></div>
				<h2 id="diagnosticoQuestionText" class="diagnostico-quiz__title"></h2>
				<div class="diagnostico-quiz__options" id="diagnosticoOptionsList"></div>
			</div>



			<!-- CTA Buttons -->
			<div class="diagnostico-cta">
				<p class="diagnostico-cta__text"><?php esc_html_e('¿Prefieres hablar directamente?', 'avance-template'); ?></p>
				<a href="<?php echo esc_url(AVANCE_WHATSAPP_URL); ?>" class="diagnostico-cta__btn diagnostico-cta__btn--secondary" <?php echo AVANCE_WHATSAPP_ATTRS; ?>>
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
					<?php esc_html_e('Escribir ahora', 'avance-template'); ?>
				</a>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
