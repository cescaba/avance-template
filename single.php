<?php
/**
 * Template para posts individuales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container">
	<div class="content-wrapper">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
				<article <?php post_class( 'single-post' ); ?> id="post-<?php the_ID(); ?>">
					<header class="post-header">
						<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>

						<!-- Meta información -->
						<div class="post-meta">
							<?php
							// Autor
							echo '<span class="post-author">';
							echo esc_html__( 'Por: ', 'avance-nativo' );
							echo '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">';
							echo esc_html( get_the_author() );
							echo '</a></span>';

							// Fecha
							echo '<span class="post-date">';
							echo esc_html( get_the_date() );
							echo '</span>';

							// Categorías
							if ( has_category() ) {
								echo '<span class="post-categories">';
								echo esc_html__( 'Categorías: ', 'avance-nativo' );
								the_category( ', ' );
								echo '</span>';
							}
							?>
						</div>
					</header>

					<!-- Imagen destacada -->
					<?php
					if ( has_post_thumbnail() ) {
						echo '<div class="post-thumbnail">';
						the_post_thumbnail( 'full', array(
							'class' => 'featured-image',
						) );
						echo '</div>';
					}
					?>

					<!-- Contenido del post -->
					<div class="post-content">
						<?php
						the_content();

						// Links de paginación para posts con múltiples páginas
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span>' . esc_html__( 'Páginas:', 'avance-nativo' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						) );
						?>
					</div>

					<!-- Tags -->
					<?php
					if ( has_tag() ) {
						echo '<div class="post-tags">';
						esc_html_e( 'Etiquetas: ', 'avance-nativo' );
						the_tags( '', ', ', '' );
						echo '</div>';
					}
					?>
				</article>

				<?php
				// Navegación entre posts
				echo '<nav class="post-navigation">';
				the_post_navigation( array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Anterior:', 'avance-nativo' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Siguiente:', 'avance-nativo' ) . '</span> <span class="nav-title">%title</span>',
				) );
				echo '</nav>';

				// Comentarios
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
				<?php
			}
		}
		?>
	</div>
</div>

<?php
get_footer();
