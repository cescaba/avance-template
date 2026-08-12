<?php
/**
 * CLEANUP - Identificar y eliminar página duplicada de checkout
 */

if (!defined('ABSPATH')) {
	exit;
}

// Solo en admin
add_action('admin_init', function() {
	if (!current_user_can('manage_options')) {
		return;
	}

	// Crear página de cleanup en admin
	add_submenu_page(
		'tools.php',
		'Limpiar Duplicados',
		'Limpiar Duplicados',
		'manage_options',
		'avance-cleanup-duplicates',
		function() {
			echo '<div class="wrap" style="max-width: 900px; font-family: Arial, sans-serif; line-height: 1.8;">';
			echo '<h1>🧹 Limpiar Páginas Duplicadas</h1>';

			// Buscar TODAS las páginas con "finalizar-compra" en el título o URL
			$args = array(
				'post_type' => 'page',
				'post_status' => array('publish', 'draft', 'pending', 'future', 'private', 'trash'),
				'posts_per_page' => -1,
				's' => 'finalizar-compra',
			);
			$pages = new WP_Query($args);

			echo '<h2>Páginas encontradas:</h2>';
			echo '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
			echo '<tr style="background: #f0f0f0; font-weight: bold;">';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">ID</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Título</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">URL</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Estado</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Acción</th>';
			echo '</tr>';

			$wc_checkout_id = get_option('woocommerce_checkout_page_id');

			foreach ($pages->posts as $page) {
				$is_used = ($page->ID == $wc_checkout_id);
				$icon = $is_used ? '✅ USADA' : '⚠️ Duplicada';

				echo '<tr>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>' . $page->ID . '</strong></td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">' . $page->post_title . '</td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;"><code>' . $page->post_name . '</code></td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">' . $page->post_status . '</td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">';
				echo $icon;

				// Si es la página que NO se usa, mostrar botón para eliminar
				if (!$is_used && $page->post_status !== 'trash') {
					echo ' <a href="' . wp_nonce_url(
						admin_url('admin.php?page=avance-cleanup-duplicates&delete_page=' . $page->ID),
						'delete_page_' . $page->ID
					) . '" style="color: red; margin-left: 10px; text-decoration: none;">❌ Eliminar</a>';
				}

				echo '</td></tr>';
			}

			echo '</table>';

			// Procesar eliminación si se solicita
			if (isset($_GET['delete_page'])) {
				$page_id = intval($_GET['delete_page']);
				$nonce = $_GET['_wpnonce'] ?? '';

				if (wp_verify_nonce($nonce, 'delete_page_' . $page_id)) {
					// Eliminar permanentemente
					wp_delete_post($page_id, true);
					echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 3px; margin: 20px 0;">';
					echo '<strong>✅ Página eliminada correctamente (ID: ' . $page_id . ')</strong>';
					echo '</div>';
				}
			}

			// Configuración actual
			echo '<div style="background: #e7f3ff; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0; border-radius: 3px;">';
			echo '<h3>📋 Configuración Actual</h3>';
			echo '<p><strong>WooCommerce usa la página ID:</strong> ' . ($wc_checkout_id ?: '❌ NO CONFIGURADA') . '</p>';
			echo '<p><strong>URL de checkout:</strong> <code>' . esc_html(wc_get_checkout_url()) . '</code></p>';
			echo '</div>';

			echo '</div>';
		}
	);
});
