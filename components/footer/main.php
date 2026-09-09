<?php

/**
 * Footer Component
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<!-- Final CTA Section -->
<section class="home-final-cta" aria-label="<?php esc_attr_e('Llamada Final a la Acción', 'avance-template'); ?>">
	<div class="home-final-cta__bg">
		<div class="home-final-cta__subtitle">
			<h2 class="home-final-cta__title"><?php esc_html_e('¿Listo para transformar tu empresa?', 'avance-template'); ?></h2>
			<p class="home-final-cta__subtitle-text"><?php esc_html_e('Primera sesión sin costo · Sin compromiso', 'avance-template'); ?></p>
		</div>
		<div class="home-final-cta__actions">
			<?php $whatsapp_url = avance_get_whatsapp_url(); ?>
			<?php if ($whatsapp_url): ?>
				<a href="<?php echo esc_url($whatsapp_url); ?>" class="home-final-cta__btn-whatsapp" target="_blank" rel="noopener">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-final-cta__icon" aria-hidden="true">
					<?php esc_html_e('Hablar por WhatsApp', 'avance-template'); ?>
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url(avance_get_page_link('contacto')); ?>" class="home-final-cta__btn-primary"><?php esc_html_e('Ir a contacto', 'avance-template'); ?></a>
		</div>
	</div>
</section>

<footer class="site-footer" role="contentinfo">
	<div class="site-footer__container">
		<div class="site-footer__grid">
			<div class="site-footer__brand">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__logo-link">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/Frame.svg'); ?>" alt="<?php bloginfo('name'); ?>" class="site-footer__logo" width="166" height="47">
					<span class="site-footer__logo-text">AVANCE</span>
				</a>
				<p class="site-footer__brand-text"><?php esc_html_e('Capacitación ejecutiva, consultoría comercial y mentoría para empresas que quieren resultados reales. Lima, Perú.', 'avance-template'); ?></p>
			</div>

			<nav class="site-footer__col" aria-label="<?php esc_attr_e('Navegación', 'avance-template'); ?>">
				<h4 class="site-footer__col-title"><?php esc_html_e('Navegación', 'avance-template'); ?></h4>
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Inicio', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('mentorias')); ?>"><?php esc_html_e('Mentorías', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('servicio-empresa')); ?>"><?php esc_html_e('Servicio Empresa', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('diagnostico')); ?>"><?php esc_html_e('Diagnóstico', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('sobre-mi')); ?>"><?php esc_html_e('Sobre mí', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('mi-libro')); ?>"><?php esc_html_e('Mi libro', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('contacto')); ?>"><?php esc_html_e('Contacto', 'avance-template'); ?></a>
			</nav>

			<div class="site-footer__col">
				<h4 class="site-footer__col-title"><?php esc_html_e('Contacto', 'avance-template'); ?></h4>
				<?php $contact_email = avance_get_contact_email(); ?>
				<?php if ($contact_email): ?>
					<a href="<?php echo esc_url('mailto:' . $contact_email); ?>" class="site-footer__contact-item" target="_blank" rel="noopener">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/correo.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
						<span><?php echo esc_html($contact_email); ?></span>
					</a>
				<?php endif; ?>
				<?php $whatsapp_link = avance_get_whatsapp_link(); ?>
				<?php if ($whatsapp_link): ?>
					<a href="<?php echo esc_url($whatsapp_link); ?>" class="site-footer__contact-item" target="_blank" rel="noopener">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
						<span><?php echo esc_html(avance_get_contact_phone_primary()); ?></span>
					</a>
				<?php endif; ?>
				<div class="site-footer__contact-item">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/ubicacion.svg'); ?>" alt="" width="16" height="16" aria-hidden="true">
					<span><?php esc_html_e('Santiago de Surco, Lima', 'avance-template'); ?></span>
				</div>
			</div>
		</div>

		<div class="site-footer__bottom">
			<div class="site-footer__copyright-text">
				&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?> · <?php esc_html_e('Santiago de Surco, Lima, Perú', 'avance-template'); ?>
			</div>
			<nav class="site-footer__legal-links" aria-label="<?php esc_attr_e('Enlaces legales', 'avance-template'); ?>">
				<a href="<?php echo esc_url(avance_get_page_link('politica-privacidad')); ?>"><?php esc_html_e('Política de Privacidad', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(avance_get_page_link('terminos-uso')); ?>"><?php esc_html_e('Términos de Uso', 'avance-template'); ?></a>
			</nav>
		</div>
	</div>
</footer>

<?php get_template_part('template-parts/modal-pdf-download'); ?>

<?php
wp_footer();
?>
</body>
</html>