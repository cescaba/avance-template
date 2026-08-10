<?php
/**
 * Header Component
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<!-- Banner Promocional -->
	<section class="site-banner">
		<div class="site-banner__container">
			<span class="site-banner__label">Descarga gratis:</span>
			<a href="#" class="site-banner__link">B2B MANAGEMENT: Cómo construir sistemas comerciales coherentes, medibles y escalables (fragmento)</a>
		</div>
	</section>

	<header class="site-header">
		<section class="site-header__container">
			<!-- Logo -->
			<div class="site-header__logo animate-on-load">
				<a href="<?php echo esc_url(home_url('/')); ?>">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/frame.svg'); ?>" alt="<?php bloginfo('name'); ?>" width="166" height="47">
				</a>
			</div>

			<!-- Opciones (Menú + Botones) -->
			<div class="site-header__options">
				<!-- Menú de elecciones -->
				<nav class="site-header__menu animate-on-load">
					<ul class="site-header__menu-list">
						<li><a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a></li>
						<li><a href="<?php echo esc_url(home_url('/mentorias')); ?>">Mentorías</a></li>
						<li><a href="<?php echo esc_url(home_url('/servicio-empresa')); ?>">Servicio Empresa</a></li>
						<li><a href="<?php echo esc_url(home_url('/diagnostico')); ?>">Diagnóstico</a></li>
						<li><a href="<?php echo esc_url(home_url('/sobre-mi')); ?>">Sobre mí</a></li>
						<li><a href="<?php echo esc_url(home_url('/mi-libro')); ?>">Mi libro</a></li>
						<li><a href="<?php echo esc_url(home_url('/contacto')); ?>">Contacto</a></li>
					</ul>
				</nav>

				<!-- Botones de acción -->
				<div class="site-header__buttons animate-on-load">
					<a class="site-header__btn-whatsapp">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16">
						WhatsApp
					</a>
					<button class="site-header__btn-session">Agendar sesión</button>
				</div>
			</div>
		</section>
	</header>
