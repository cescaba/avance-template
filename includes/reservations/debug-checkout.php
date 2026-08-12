<?php
/**
 * DEBUG CHECKOUT - Ver qué URL devuelve WooCommerce
 */

if (!defined('ABSPATH')) {
	exit;
}

// Solo en admin
add_action('admin_init', function() {
	if (!current_user_can('manage_options')) {
		return;
	}

	// Crear página de debug en admin
	add_submenu_page(
		'tools.php',
		'Debug Checkout',
		'Debug Checkout',
		'manage_options',
		'avance-debug-checkout',
		function() {
			echo '<div class="wrap" style="max-width: 900px; font-family: Arial, sans-serif; line-height: 1.8;">';
			echo '<h1>🔍 Debug - URL de Checkout</h1>';

			if (!class_exists('WooCommerce')) {
				echo '<p style="color: red;"><strong>❌ WooCommerce no está activo</strong></p>';
				echo '</div>';
				return;
			}

			echo '<table style="width: 100%; border-collapse: collapse;">';
			echo '<tr style="background: #f0f0f0;">';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Verificación</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Resultado</th>';
			echo '</tr>';

			// 1. Página de checkout en BD
			$checkout_page = get_page_by_path('finalizar-compra');
			echo '<tr>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>Página existe</strong></td>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;">';
			if ($checkout_page) {
				echo '✅ SÍ (ID: ' . $checkout_page->ID . ')';
			} else {
				echo '❌ NO - La página no existe en la BD';
			}
			echo '</td></tr>';

			// 2. Estado de la página
			if ($checkout_page) {
				echo '<tr>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>Estado</strong></td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">';
				echo $checkout_page->post_status === 'publish' ? '✅ PUBLISH' : '❌ ' . strtoupper($checkout_page->post_status);
				echo '</td></tr>';

				// 3. Contenido
				echo '<tr>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>Contenido</strong></td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">';
				echo $checkout_page->post_content ?: '(vacío)';
				echo '</td></tr>';
			}

			// 4. Opción WooCommerce
			$wc_checkout_page_id = get_option('woocommerce_checkout_page_id');
			echo '<tr>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>WooCommerce checkout_page_id</strong></td>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;">';
			echo $wc_checkout_page_id ? $wc_checkout_page_id : '❌ NO CONFIGURADO';
			echo '</td></tr>';

			// 5. URL que devuelve wc_get_checkout_url()
			$checkout_url = wc_get_checkout_url();
			echo '<tr>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>wc_get_checkout_url()</strong></td>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;">';
			echo '<code style="background: #f5f5f5; padding: 8px; display: block; border-radius: 3px;">' . esc_html($checkout_url) . '</code>';
			echo '</td></tr>';

			// 6. Página de inicio
			$home_url = home_url();
			echo '<tr>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>Home URL</strong></td>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;">';
			echo '<code style="background: #f5f5f5; padding: 8px; display: block; border-radius: 3px;">' . esc_html($home_url) . '</code>';
			echo '</td></tr>';

			// 7. Son iguales?
			echo '<tr>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;"><strong>¿Checkout = Home?</strong></td>';
			echo '<td style="border: 1px solid #ddd; padding: 12px;">';
			if ($checkout_url === $home_url || $checkout_url === $home_url . '/') {
				echo '❌ SÍ - Ese es el problema';
			} else {
				echo '✅ NO - URLs diferentes';
			}
			echo '</td></tr>';

			echo '</table>';

			// Botón para reparar
			echo '<div style="margin-top: 30px; background: #fff8e5; border-left: 4px solid #ffb81c; padding: 15px; border-radius: 3px;">';
			echo '<h2>🔧 Si algo está mal:</h2>';
			echo '<ol>';
			echo '<li><strong>Página no existe:</strong> Ve a Páginas → Agregar nueva, crea "Finalizar Compra" con URL "finalizar-compra" y contenido "[woocommerce_checkout]", publica.</li>';
			echo '<li><strong>Estado no es PUBLISH:</strong> Edita la página, cambia estado a "Publicado" y guarda.</li>';
			echo '<li><strong>Contenido vacío:</strong> Edita la página y agrega el shortcode [woocommerce_checkout]</li>';
			echo '<li><strong>WooCommerce ID no configurado:</strong> Ve a WooCommerce → Ajustes → Avanzado → Página de checkout, selecciona "Finalizar Compra".</li>';
			echo '</ol>';
			echo '</div>';

			// Instrucciones para limpiar banderas
			echo '<div style="margin-top: 20px; background: #f0f0f0; border: 1px solid #ddd; padding: 15px; border-radius: 3px;">';
			echo '<h3>Para resetear el setup automático:</h3>';
			echo '<code style="background: #fff; padding: 10px; display: block; border-radius: 3px; border: 1px solid #ddd;margin-bottom: 10px;">wp eval \'delete_option("avance_checkout_page_created"); delete_option("avance_checkout_status_fixed"); delete_option("avance_payment_methods_configured");\'</code>';
			echo '<code style="background: #fff; padding: 10px; display: block; border-radius: 3px; border: 1px solid #ddd;">wp cache flush</code>';
			echo '</div>';

			echo '</div>';
		}
	);
});
