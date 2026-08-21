<?php
/**
 * Page Template - Plantilla para páginas individuales
 *
 * Renderiza el contenido de cada página específica
 * sin forzar un template fijo
 *
 * @package Avance_Template
 */

// Si es la página de inicio, usar page-inicio
if (is_front_page()) {
    get_template_part('templates/page-inicio');
    return;
}

$page_id = get_the_ID();

// Mapeo de IDs de página a templates específicos
$page_templates = [
    'mentorias'          => 'page-mentoria',
    'diagnostico'        => 'page-diagnostico',
    'contacto'           => 'page-contacto',
    'servicio-empresa'   => 'page-servicio-empresa',
    'sobre-mi'           => 'page-sobremi',
    'libro'              => 'page-libro',
];

// Buscar si la página tiene un slug que coincida
$page_slug = get_post_field('post_name', $page_id);
foreach ($page_templates as $slug => $template) {
    if ($page_slug === $slug) {
        get_template_part('templates/' . $template);
        return;
    }
}

// Para todas las demás páginas (incluyendo checkout), renderizar contenido normal
get_header();
?>

<main class="page-main">
    <div class="page-container">
        <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="page-content">
                        <?php
                        the_content();

                        // Si es una página de WooCommerce, no mostrar navegación
                        if (!is_cart() && !is_checkout() && !is_account_page()) {
                            wp_link_pages([
                                'before' => '<div class="page-links">',
                                'after'  => '</div>',
                            ]);
                        }
                        ?>
                    </div>
                </article>
                <?php
            }
        }
        ?>
    </div>
</main>

<?php
get_footer();
?>
