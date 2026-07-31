<?php
/**
 * Archivo de debug para verificar que el tema está funcionando correctamente
 *
 * INSTRUCCIONES DE USO:
 * 1. Copia el contenido de este archivo
 * 2. Pégalo en la parte superior de functions.php (después de la línea 8: if ( ! defined( 'ABSPATH' ) ) exit;)
 * 3. Recarga tu sitio
 * 4. Revisa wp-content/debug.log
 * 5. Luego elimina este código del functions.php
 */

// Debug: Verificar que el tema se está cargando
error_log( '=== AVANCE NATIVO THEME DEBUG ===' );
error_log( 'Tema: ' . get_template() );
error_log( 'Tema Actual: ' . wp_get_theme()->get( 'Name' ) );
error_log( 'Directorio Template: ' . get_template_directory_uri() );
error_log( 'Directorio Stylesheet: ' . get_stylesheet_uri() );
error_log( '=== FIN DEBUG ===' );

// Hook para verificar que el enqueue se está ejecutando
add_action( 'wp_enqueue_scripts', function() {
	error_log( '[Avance Nativo] wp_enqueue_scripts hook se ejecutó' );
}, 5 );

add_action( 'wp_head', function() {
	error_log( '[Avance Nativo] wp_head hook se ejecutó' );
	error_log( '[Avance Nativo] Estilos encolados: ' . print_r( wp_styles()->queue, true ) );
}, 99 );
