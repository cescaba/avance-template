<?php
/**
 * RESET CHECKOUT - Limpiar banderas y recrear página
 * Se ejecuta UNA SOLA VEZ cuando se carga wp-admin
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('admin_init', function() {
	// Verificar si se debe resetear
	if (!get_option('avance_reset_checkout_setup')) {
		return;
	}

	// Solo admin
	if (!current_user_can('manage_options')) {
		return;
	}

	// Eliminar las banderas
	delete_option('avance_checkout_page_created');
	delete_option('avance_checkout_status_fixed');
	delete_option('avance_payment_methods_configured');

	// Marcar como reseteado
	delete_option('avance_reset_checkout_setup');

	// Log
	error_log('✅ Setup de checkout reseteado. Se recrearé en el próximo wp_loaded');
});

/**
 * Página de admin para triggear el reset
 */
add_action('admin_menu', function() {
	add_submenu_page(
		'tools.php',
		'Reset Checkout',
		'Reset Checkout',
		'manage_options',
		'avance-reset-checkout',
		function() {
			echo '<div class="wrap" style="max-width: 600px; font-family: Arial, sans-serif;">';
			echo '<h1>🔄 Reset Configuración de Checkout</h1>';

			// Si se hace click en el botón
			if (isset($_POST['reset_checkout']) && wp_verify_nonce($_POST['_wpnonce'], 'avance_reset_checkout_nonce')) {
				// Eliminar todas las páginas "finalizar-compra"
				$args = array(
					'post_type' => 'page',
					'posts_per_page' => -1,
					'post_status' => array('publish', 'draft', 'pending', 'future', 'private', 'trash'),
				);
				$all_pages = new WP_Query($args);

				$deleted_count = 0;
				foreach ($all_pages->posts as $page) {
					if ($page->post_name === 'finalizar-compra' || strpos($page->post_title, 'Finalizar Compra') !== false) {
						wp_delete_post($page->ID, true);
						$deleted_count++;
					}
				}

				// Limpiar opciones
				delete_option('avance_checkout_page_created');
				delete_option('avance_checkout_status_fixed');
				delete_option('avance_payment_methods_configured');

				echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 3px; margin: 20px 0;">';
				echo '<strong>✅ Reset completado!</strong><br>';
				echo '• Eliminadas ' . $deleted_count . ' página(s) vieja(s)<br>';
				echo '• Opciones limpiadas<br>';
				echo '<br><strong>Próximo paso:</strong> Recarga esta página (F5). El sistema recreará la página correctamente.';
				echo '</div>';
			}

			// Botón de reset
			echo '<form method="post" style="margin-top: 20px;">';
			wp_nonce_field('avance_reset_checkout_nonce');
			echo '<input type="hidden" name="reset_checkout" value="1">';
			echo '<button type="submit" class="button button-primary" style="padding: 10px 20px; font-size: 14px;">🔄 Resetear Checkout</button>';
			echo '</form>';

			echo '<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 3px;">';
			echo '<h3>ℹ️ ¿Qué hace esto?</h3>';
			echo '<ol>';
			echo '<li>Elimina TODAS las páginas "Finalizar Compra" existentes</li>';
			echo '<li>Limpia las opciones del setup automático</li>';
			echo '<li>La próxima vez que recargues, el sistema crea la página NUEVA y correcta</li>';
			echo '</ol>';
			echo '</div>';

			echo '</div>';
		}
	);
});
