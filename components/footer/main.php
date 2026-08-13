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
<section class="home-final-cta animate-on-scroll" data-animate aria-label="<?php esc_attr_e('Llamada Final a la Acción', 'avance-template'); ?>">
	<div class="home-final-cta__bg">
		<div class="home-final-cta__subtitle">
			<h2 class="home-final-cta__title"><?php esc_html_e('¿Listo para transformar tu empresa?', 'avance-template'); ?></h2>
			<p class="home-final-cta__subtitle-text"><?php esc_html_e('Primera sesión sin costo · Sin compromiso', 'avance-template'); ?></p>
		</div>
		<div class="home-final-cta__actions">
			<a href="#" class="home-final-cta__btn-whatsapp">
				<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/wsp.svg'); ?>" alt="" width="16" height="16" class="home-final-cta__icon" aria-hidden="true">
				<?php esc_html_e('Hablar por WhatsApp', 'avance-template'); ?>
			</a>
			<a href="#" class="home-final-cta__btn-primary"><?php esc_html_e('Ir a contacto', 'avance-template'); ?></a>
		</div>
	</div>
</section>

<footer class="site-footer" role="contentinfo">
	<div class="site-footer__container">
		<div class="site-footer__grid">
			<div class="site-footer__brand animate-on-scroll" data-animate>
				<a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__logo-link">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/frame.svg'); ?>" alt="<?php bloginfo('name'); ?>" class="site-footer__logo" width="166" height="47">
				</a>
				<p class="site-footer__brand-text"><?php esc_html_e('Capacitación ejecutiva, consultoría comercial y mentoría para empresas que quieren resultados reales. Lima, Perú.', 'avance-template'); ?></p>
			</div>

			<nav class="site-footer__col animate-on-scroll" data-animate aria-label="<?php esc_attr_e('Navegación', 'avance-template'); ?>">
				<h4 class="site-footer__col-title"><?php esc_html_e('Navegación', 'avance-template'); ?></h4>
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Inicio', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/mentorias')); ?>"><?php esc_html_e('Mentorías', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/servicio-empresa')); ?>"><?php esc_html_e('Servicio Empresa', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/diagnostico')); ?>"><?php esc_html_e('Diagnóstico', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/sobre-mi')); ?>"><?php esc_html_e('Sobre mí', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/mi-libro')); ?>"><?php esc_html_e('Mi libro', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/contacto')); ?>"><?php esc_html_e('Contacto', 'avance-template'); ?></a>
			</nav>

			<div class="site-footer__col animate-on-scroll" data-animate>
				<h4 class="site-footer__col-title"><?php esc_html_e('Contacto', 'avance-template'); ?></h4>
				<a href="mailto:hola@avanceempresarial.com">hola@avanceempresarial.com</a>
				<a href="tel:+51999000000">+51 999 000 000</a>
				<p><?php esc_html_e('Lima, Perú', 'avance-template'); ?></p>
			</div>
		</div>

		<div class="site-footer__bottom animate-on-scroll" data-animate>
			<div class="site-footer__copyright-text">
				&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?> · <?php esc_html_e('Santiago de Surco, Lima, Perú', 'avance-template'); ?>
			</div>
			<nav class="site-footer__legal-links" aria-label="<?php esc_attr_e('Enlaces legales', 'avance-template'); ?>">
				<a href="<?php echo esc_url(home_url('/politica-privacidad')); ?>"><?php esc_html_e('Política de Privacidad', 'avance-template'); ?></a>
				<a href="<?php echo esc_url(home_url('/terminos-uso')); ?>"><?php esc_html_e('Términos de Uso', 'avance-template'); ?></a>
			</nav>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>

</html>