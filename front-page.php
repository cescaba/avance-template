<?php
/**
 * Template para la página de inicio
 */

if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="container">
	<!-- Hero Section -->
	<section class="hero-section">
		<div class="hero-content">
			<?php
			if ( have_posts() ) {
				the_post();
				the_title( '<h1 class="hero-title">', '</h1>' );
				echo '<div class="hero-text">' . wp_kses_post( get_the_content() ) . '</div>';

				$button_text = get_theme_mod( 'hero_button_text', 'Conocer Más' );
				$button_url = get_theme_mod( 'hero_button_url', '#' );
				if ( $button_text && $button_url ) {
					echo '<a href="' . esc_url( $button_url ) . '" class="btn btn-primary">' . esc_html( $button_text ) . '</a>';
				}
			}
			?>
		</div>
		<?php
		if ( has_post_thumbnail() ) {
			echo '<div class="hero-image">';
			the_post_thumbnail( 'full', array( 'class' => 'hero-img' ) );
			echo '</div>';
		}
		?>
	</section>

	<!-- Características Section -->
	<section class="features-section">
		<h2 class="section-title">Nuestrasddd Características</h2>
		<div class="features-grid">
			<?php
			$features = get_theme_mod( 'homepage_features', array(
				array(
					'title' => '100% Nativo',
					'description' => 'Desarrollado completamente en WordPress sin plugins externos'
				),
				array(
					'title' => 'Responsivo',
					'description' => 'Se adapta perfectamente a todos los dispositivos y pantallas'
				),
				array(
					'title' => 'Seguro',
					'description' => 'Cumple con estándares de seguridad y mejores prácticas'
				),
				array(
					'title' => 'Rápido',
					'description' => 'Optimizado para velocidad y rendimiento máximo'
				),
			) );

			if ( ! empty( $features ) ) {
				foreach ( $features as $feature ) {
					echo '<div class="feature-card">';
					echo '<h3>' . esc_html( $feature['title'] ) . '</h3>';
					echo '<p>' . esc_html( $feature['description'] ) . '</p>';
					echo '</div>';
				}
			}
			?>
		</div>
	</section>

	<!-- Últimos Posts Section -->
	<section class="latest-posts-section">
		<h2 class="section-title">Últimas Publicaciones</h2>
		<div class="posts-grid">
			<?php
			$latest_posts = new WP_Query( array(
				'posts_per_page' => 3,
				'post_type' => 'post',
				'orderby' => 'date',
				'order' => 'DESC',
			) );

			if ( $latest_posts->have_posts() ) {
				while ( $latest_posts->have_posts() ) {
					$latest_posts->the_post();
					?>
					<article class="post-card">
						<?php if ( has_post_thumbnail() ) { ?>
							<div class="post-card-image">
								<?php the_post_thumbnail( 'avance-medium' ); ?>
							</div>
						<?php } ?>
						<div class="post-card-content">
							<h3 class="post-card-title">
								<a href="<?php echo esc_url( get_permalink() ); ?>">
									<?php the_title(); ?>
								</a>
							</h3>
							<div class="post-card-meta">
								<span class="post-date"><?php echo esc_html( get_the_date() ); ?></span>
								<span class="post-author">
									<?php echo esc_html__( 'Por ', 'avance-nativo' ); ?>
									<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
										<?php echo esc_html( get_the_author() ); ?>
									</a>
								</span>
							</div>
							<div class="post-card-excerpt">
								<?php the_excerpt(); ?>
							</div>
							<a href="<?php echo esc_url( get_permalink() ); ?>" class="read-more">
								<?php echo esc_html__( 'Leer más →', 'avance-nativo' ); ?>
							</a>
						</div>
					</article>
					<?php
				}
				wp_reset_postdata();
			}
			?>
		</div>
	</section>

	<!-- CTA Section -->
	<section class="cta-section">
		<div class="cta-content">
			<h2><?php echo esc_html( get_theme_mod( 'cta_title', '¿Listo para comenzar?' ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'cta_description', 'Descubre todo lo que podemos hacer por tu negocio' ) ); ?></p>
			<a href="<?php echo esc_url( get_theme_mod( 'cta_button_url', '#' ) ); ?>" class="btn btn-light">
				<?php echo esc_html( get_theme_mod( 'cta_button_text', 'Contactar Ahora' ) ); ?>
			</a>
		</div>
	</section>
</div>

<?php get_footer();
