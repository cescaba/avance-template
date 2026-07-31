<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="container">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<div class="entry-meta">
						<span class="posted-on">
							<?php echo get_the_date(); ?>
						</span>
						<span class="byline">
							<?php echo esc_html__( 'Por', 'avance-nativo' ); ?>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
								<?php echo esc_html( get_the_author() ); ?>
							</a>
						</span>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) { ?>
					<div class="post-thumbnail">
						<?php the_post_thumbnail(); ?>
					</div>
				<?php } ?>

				<div class="entry-content">
					<?php
					if ( is_singular() ) {
						the_content();
					} else {
						the_excerpt();
						echo '<p><a href="' . esc_url( get_permalink() ) . '" class="read-more">Leer más →</a></p>';
					}
					?>
				</div>

				<?php if ( comments_open() ) { ?>
					<div class="entry-footer">
						<?php comments_popup_link( 'Sin comentarios', '1 comentario', '% comentarios' ); ?>
					</div>
				<?php } ?>
			</article>
			<?php
		}

		the_posts_pagination( array(
			'mid_size' => 2,
			'prev_text' => '← Anterior',
			'next_text' => 'Siguiente →',
		) );
	} else {
		?>
		<article>
			<h1><?php esc_html_e( 'No se encontró contenido', 'avance-nativo' ); ?></h1>
			<p><?php esc_html_e( 'Lo sentimos, no hay contenido disponible.', 'avance-nativo' ); ?></p>
		</article>
		<?php
	}
	?>
</div>

<?php get_footer();
