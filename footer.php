<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
	</main>

	<footer class="site-footer">
		<div class="container">
			<div class="footer-content">
				<?php if ( is_active_sidebar( 'footer-1' ) ) dynamic_sidebar( 'footer-1' ); ?>
				<?php if ( is_active_sidebar( 'footer-2' ) ) dynamic_sidebar( 'footer-2' ); ?>
				<?php if ( is_active_sidebar( 'footer-3' ) ) dynamic_sidebar( 'footer-3' ); ?>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) wp_nav_menu( array( 'theme_location' => 'footer', 'container' => 'nav', 'container_class' => 'footer-navigation' ) ); ?>

			<div class="footer-bottom">
				<p>&copy; <?php echo avance_nativo_get_copyright_year(); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. Todos los derechos reservados.</p>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
