<?php
/**
 * Template para páginas individuales
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
				<article <?php post_class( 'page' ); ?> id="post-<?php the_ID(); ?>">
					<header class="page-header">
						<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
					</header>

					<!-- Imagen destacada -->
					<?php
					if ( has_post_thumbnail() ) {
						echo '<div class="page-thumbnail">';
						the_post_thumbnail( 'full', array(
							'class' => 'featured-image',
						) );
						echo '</div>';
					}
					?>

					<!-- Contenido de la página -->
					<div class="page-content">
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

					<?php
					// Mostrar comentarios si están habilitados
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</article>
				<?php
			}
		}
		?>
	</div>
</div>

<?php
get_footer();
