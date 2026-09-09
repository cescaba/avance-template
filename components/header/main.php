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

	<!-- Promotional Banner -->
	<div class="avance-banner">
		<span class="avance-banner__label">Descarga gratis</span>
		<a class="avance-banner__link" href="#">B2B Management: Cómo construir sistemas comerciales coherentes, medibles y escalables (fragmento)</a>
	</div>

	<!-- Primary Header -->
	<header class="avance-header" id="avance-header">
		<div class="avance-header__container">
			<!-- Logo / Branding -->
			<a class="avance-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?>">
				<img class="avance-logo__image" src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/Frame.svg'); ?>" alt="<?php bloginfo('name'); ?>" width="166" height="47">
			</a>

			<!-- Desktop Navigation Section -->
			<div class="avance-header__nav-wrapper">
				<!-- Primary Navigation -->
				<nav class="avance-nav-primary" aria-label="Navegación principal">
					<a href="<?php echo esc_url(home_url('/')); ?>" class="avance-nav-primary__link" <?php echo (is_home() || is_front_page()) ? 'aria-current="page"' : ''; ?>>Inicio</a>
					<a href="<?php echo esc_url(home_url('/mentorias')); ?>" class="avance-nav-primary__link" <?php echo is_page('mentorias') ? 'aria-current="page"' : ''; ?>>Mentorías</a>
					<a href="<?php echo esc_url(home_url('/servicio-empresa')); ?>" class="avance-nav-primary__link" <?php echo is_page('servicio-empresa') ? 'aria-current="page"' : ''; ?>>Servicio Empresa</a>
					<a href="<?php echo esc_url(home_url('/diagnostico')); ?>" class="avance-nav-primary__link" <?php echo is_page('diagnostico') ? 'aria-current="page"' : ''; ?>>Diagnóstico</a>
					<a href="<?php echo esc_url(home_url('/sobre-mi')); ?>" class="avance-nav-primary__link" <?php echo is_page('sobre-mi') ? 'aria-current="page"' : ''; ?>>Sobre mí</a>
					<a href="<?php echo esc_url(home_url('/mi-libro')); ?>" class="avance-nav-primary__link" <?php echo is_page('mi-libro') ? 'aria-current="page"' : ''; ?>>Mi libro</a>
					<a href="<?php echo esc_url(home_url('/contacto')); ?>" class="avance-nav-primary__link" <?php echo is_page('contacto') ? 'aria-current="page"' : ''; ?>>Contacto</a>
				</nav>

				<!-- Action Buttons -->
				<div class="avance-header__actions">
					<a class="avance-btn avance-btn--ghost" href="<?php echo esc_url(AVANCE_WHATSAPP_URL); ?>" aria-label="Contactar por WhatsApp" <?php echo AVANCE_WHATSAPP_ATTRS; ?>>
						<img class="avance-btn__icon" src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="15" height="15" aria-hidden="true">
						<span>WhatsApp</span>
					</a>
					<?php
						$booking_url = is_page('inicio') ? '#scheduling-section' : esc_url(home_url('/#scheduling-section'));
					?>
					<a class="avance-btn avance-btn--outline" href="<?php echo $booking_url; ?>">Agendar sesión</a>
				</div>
			</div>

			<!-- Mobile Menu Toggle -->
			<button class="avance-menu-toggle" id="avance-menu-toggle" type="button" aria-label="Menú" aria-expanded="false" aria-controls="avance-menu-panel">
				<svg class="avance-menu-toggle__icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line class="avance-menu-toggle__line avance-menu-toggle__line--1" x1="3" y1="6" x2="21" y2="6"></line>
					<line class="avance-menu-toggle__line avance-menu-toggle__line--2" x1="3" y1="12" x2="21" y2="12"></line>
					<line class="avance-menu-toggle__line avance-menu-toggle__line--3" x1="3" y1="18" x2="21" y2="18"></line>
					<line class="avance-menu-toggle__line avance-menu-toggle__x--1 avance-menu-toggle__x" x1="4" y1="4" x2="20" y2="20" opacity="0"></line>
					<line class="avance-menu-toggle__line avance-menu-toggle__x--2 avance-menu-toggle__x" x1="20" y1="4" x2="4" y2="20" opacity="0"></line>
				</svg>
			</button>
		</div>

		<!-- Mobile Menu Panel -->
		<nav class="avance-menu-panel" id="avance-menu-panel" aria-hidden="true">
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/')); ?>" <?php echo (is_home() || is_front_page()) ? 'aria-current="page"' : ''; ?>>Inicio</a>
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/mentorias')); ?>" <?php echo is_page('mentorias') ? 'aria-current="page"' : ''; ?>>Mentorías</a>
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/servicio-empresa')); ?>" <?php echo is_page('servicio-empresa') ? 'aria-current="page"' : ''; ?>>Servicio Empresa</a>
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/diagnostico')); ?>" <?php echo is_page('diagnostico') ? 'aria-current="page"' : ''; ?>>Diagnóstico</a>
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/sobre-mi')); ?>" <?php echo is_page('sobre-mi') ? 'aria-current="page"' : ''; ?>>Sobre mí</a>
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/mi-libro')); ?>" <?php echo is_page('mi-libro') ? 'aria-current="page"' : ''; ?>>Mi libro</a>
			<a class="avance-menu-panel__link" href="<?php echo esc_url(home_url('/contacto')); ?>" <?php echo is_page('contacto') ? 'aria-current="page"' : ''; ?>>Contacto</a>

			<div class="avance-menu-panel__actions">
				<?php
					$booking_url = is_page('inicio') ? '#scheduling-section' : esc_url(home_url('/#scheduling-section'));
				?>
				<a class="avance-btn avance-btn--primary" href="<?php echo $booking_url; ?>">Agendar sesión</a>
				<a class="avance-btn avance-btn--secondary" href="<?php echo esc_url(AVANCE_WHATSAPP_URL); ?>" <?php echo AVANCE_WHATSAPP_ATTRS; ?>>
					<img class="avance-btn__icon" src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="15" height="15" aria-hidden="true">
					<span>Escribir por WhatsApp</span>
				</a>
			</div>
		</nav>
	</header>

	<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/avance-menu.js'); ?>"></script>
</body>
</html>
