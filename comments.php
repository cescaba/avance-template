<?php
/**
 * Template para comentarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Si los comentarios están cerrados y no hay comentarios, no mostrar nada
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php
	if ( have_comments() ) {
		echo '<h2 class="comments-title">';
		printf(
			/* translators: 1: number of comments, 2: post title */
			esc_html( _nx(
				'%1$s comentario en "%2$s"',
				'%1$s comentarios en "%2$s"',
				get_comments_number(),
				'comments title',
				'avance-nativo'
			) ),
			number_format_i18n( get_comments_number() ),
			'<span>' . get_the_title() . '</span>'
		);
		echo '</h2>';

		// Mostrar navegación si hay muchos comentarios
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
			echo '<nav class="comment-navigation">';
			previous_comments_link( esc_html__( 'Comentarios más antiguos', 'avance-nativo' ) );
			next_comments_link( esc_html__( 'Comentarios más nuevos', 'avance-nativo' ) );
			echo '</nav>';
		}

		// Lista de comentarios
		echo '<ol class="comment-list">';
		wp_list_comments( array(
			'style'       => 'ol',
			'short_ping'  => true,
			'avatar_size' => 50,
			'callback'    => 'avance_nativo_comment_callback',
		) );
		echo '</ol>';

		// Navegación de comentarios al final
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
			echo '<nav class="comment-navigation">';
			previous_comments_link( esc_html__( 'Comentarios más antiguos', 'avance-nativo' ) );
			next_comments_link( esc_html__( 'Comentarios más nuevos', 'avance-nativo' ) );
			echo '</nav>';
		}
	}

	// Formulario de comentarios
	if ( comments_open() ) {
		comment_form( array(
			'logged_in_as'      => '<p class="logged-in-as">' . sprintf(
				esc_html__( 'Conectado como %s. %s', 'avance-nativo' ),
				'<a href="' . esc_url( admin_url( 'profile.php' ) ) . '">' . esc_html( wp_get_current_user()->display_name ) . '</a>',
				'<a href="' . esc_url( wp_logout_url( get_permalink() ) ) . '">' . esc_html__( 'Desconectar', 'avance-nativo' ) . '</a>'
			) . '</p>',
			'title_reply'       => esc_html__( 'Deja un comentario', 'avance-nativo' ),
			'label_submit'      => esc_html__( 'Enviar comentario', 'avance-nativo' ),
			'comment_field'     => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comentario', 'avance-nativo' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>',
			'fields'            => array(
				'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( 'Nombre', 'avance-nativo' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ?? '' ) . '" required></p>',
				'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'avance-nativo' ) . '</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ?? '' ) . '" required></p>',
				'url'    => '<p class="comment-form-url"><label for="url">' . esc_html__( 'Sitio web', 'avance-nativo' ) . '</label><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ?? '' ) . '"></p>',
			),
		) );
	} elseif ( is_post_type_comment_open() ) {
		echo '<p class="no-comments-message">' . esc_html__( 'Los comentarios están cerrados.', 'avance-nativo' ) . '</p>';
	}
	?>
</div>

<?php

/**
 * Callback personalizado para mostrar comentarios
 *
 * @param WP_Comment $comment Objeto del comentario.
 * @param array      $args    Argumentos pasados a wp_list_comments().
 * @param int        $depth   Profundidad de anidamiento.
 */
function avance_nativo_comment_callback( $comment, $args, $depth ) {
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item', $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-content">
			<header class="comment-meta">
				<div class="comment-author vcard">
					<?php
					echo get_avatar( $comment, 50, '', '', array(
						'class' => 'avatar',
						'alt'   => esc_attr( $comment->comment_author ),
					) );
					?>
					<b class="comment-author-name"><?php comment_author_link( $comment ); ?></b>
					<span class="comment-time">
						<?php
						printf(
							esc_html__( 'el %s a las %s', 'avance-nativo' ),
							get_comment_date( 'd/m/Y', $comment ),
							get_comment_date( 'H:i', $comment )
						);
						?>
					</span>
				</div>
			</header>

			<?php if ( '0' === $comment->comment_approved ) { ?>
				<p class="comment-awaiting-moderation"><?php esc_html_e( 'Tu comentario está esperando aprobación.', 'avance-nativo' ); ?></p>
			<?php } ?>

			<div class="comment-text">
				<?php echo wp_kses_post( comment_text( $comment ) ); ?>
			</div>

			<div class="comment-reply">
				<?php
				comment_reply_link( array_merge(
					$args,
					array(
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '',
						'after'     => '',
					)
				), $comment->comment_ID );
				?>
			</div>
		</article>
	</li>
	<?php
}
