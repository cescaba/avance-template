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
		<div class="contacto-hero__bg--top" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Rectangle 16.png'); ?>')"></div>
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
	<section class="contacto-scheduling" aria-label="<?php esc_attr_e('Sección de Agendamiento', 'avance-template'); ?>" id="contacto-scheduling">
		<?php
		$scheduling = new Avance_Scheduling_Component();
		$scheduling->render();
		?>
	</section>

	<!-- Form Section -->
	<section class="contacto-form-section" aria-label="<?php esc_attr_e('Sección de Formulario', 'avance-template'); ?>" id="contacto-form">
		<?php
		$form = new Avance_Form_Component();
		$form->render();
		?>
	</section>

</main>

<?php
get_footer();
