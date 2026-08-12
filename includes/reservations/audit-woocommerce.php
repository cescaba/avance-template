<?php
/**
 * AUDITORÍA DE INTEGRACIÓN WOOCOMMERCE
 *
 * Este archivo verifica que todo esté bien conectado.
 * Acceso: wp-admin/admin.php?page=avance-audit-woocommerce
 */

if (!defined('ABSPATH')) {
	exit;
}

// Solo administradores pueden ver esto
if (!current_user_can('manage_options')) {
	wp_die('No tienes permiso');
}

// Crear página de auditoría en admin
add_action('admin_menu', function() {
	add_submenu_page(
		'tools.php',
		'Auditoría Reservas',
		'Auditoría Reservas',
		'manage_options',
		'avance-audit-woocommerce',
		function() {
			echo '<div class="wrap" style="max-width: 900px; font-family: Arial, sans-serif; line-height: 1.6;">';
			echo '<h1>🔍 Auditoría de Integración Reservas + WooCommerce</h1>';

			$checks = array();

			// ✅ Check 1: WooCommerce activo
			$woocommerce_active = class_exists('WooCommerce');
			$checks[] = array(
				'nombre' => 'WooCommerce Activo',
				'resultado' => $woocommerce_active ? '✅ SÍ' : '❌ NO',
				'detalle' => $woocommerce_active ? 'WooCommerce está instalado y activo' : 'WooCommerce no está activo. Instálalo desde Plugins.',
			);

			// ✅ Check 2: Clases de Reservas
			$classes = array(
				'Avance_Reservations_Manager' => 'Manager',
				'Avance_Reservation_Validator' => 'Validator',
				'Avance_Reservation_Cart_Handler' => 'Cart Handler',
			);

			foreach ($classes as $class => $nombre) {
				$exists = class_exists($class);
				$checks[] = array(
					'nombre' => "Clase: {$nombre}",
					'resultado' => $exists ? '✅ SÍ' : '❌ NO',
					'detalle' => $exists ? "Clase {$class} cargada correctamente" : "Falta {$class}",
				);
			}

			// ✅ Check 3: Productos de Mentoría
			if ($woocommerce_active) {
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => -1,
					'post_status' => 'publish',
				);
				$products = new WP_Query($args);
				$count = $products->found_posts;

				$checks[] = array(
					'nombre' => 'Productos en WooCommerce',
					'resultado' => $count > 0 ? "✅ {$count}" : '❌ NINGUNO',
					'detalle' => $count > 0 ? "{$count} productos encontrados" : 'No hay productos. Crea al menos 3 mentorías.',
				);
			}

			// ✅ Check 4: AJAX Handler registrado
			$handlers = array(
				'wp_ajax_nopriv_avance_add_reservation' => 'Sin login',
				'wp_ajax_avance_add_reservation' => 'Con login',
			);

			global $wp_filter;
			foreach ($handlers as $hook => $tipo) {
				$registered = isset($wp_filter[$hook]) ? true : false;
				$checks[] = array(
					'nombre' => "AJAX Handler ($tipo)",
					'resultado' => $registered ? '✅ SÍ' : '❌ NO',
					'detalle' => $registered ? "Handler registrado en {$hook}" : "AJAX handler no está registrado",
				);
			}

			// ✅ Check 5: CSS/JS enqueued
			$checks[] = array(
				'nombre' => 'CSS/JS Widget',
				'resultado' => '⚠️ Verificar en frontend',
				'detalle' => 'Abre la página de mentorías y verifica que los estilos se cargen bien (inspecciona con F12)',
			);

			// ✅ Check 6: Checkout URL
			if ($woocommerce_active) {
				$checkout_url = wc_get_checkout_url();
				$checks[] = array(
					'nombre' => 'URL Checkout',
					'resultado' => !empty($checkout_url) ? '✅ SÍ' : '❌ NO',
					'detalle' => $checkout_url ?: 'Checkout URL no disponible. Configura WooCommerce.',
				);
			}

			// Mostrar resultados
			echo '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
			echo '<tr style="background: #f0f0f0; font-weight: bold;">';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Verificación</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: center; width: 100px;">Resultado</th>';
			echo '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Detalle</th>';
			echo '</tr>';

			foreach ($checks as $check) {
				echo '<tr>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">' . $check['nombre'] . '</td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: bold;">' . $check['resultado'] . '</td>';
				echo '<td style="border: 1px solid #ddd; padding: 12px;">' . $check['detalle'] . '</td>';
				echo '</tr>';
			}

			echo '</table>';

			// Recomendaciones
			echo '<div style="background: #f9f9f9; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0;">';
			echo '<h2>📋 Recomendaciones</h2>';
			echo '<ol>';
			echo '<li><strong>Verifica WooCommerce:</strong> Ve a WooCommerce → Ajustes → General para confirmar que la URL de checkout está bien.</li>';
			echo '<li><strong>Crea los productos:</strong> Si no ves 3+ productos, ve a Productos y crea "Mentoría Básica", "Premium" y "VIP".</li>';
			echo '<li><strong>Prueba el flujo:</strong> Ve a la página de mentorías, completa el formulario y confirma que te redirija al checkout.</li>';
			echo '<li><strong>Inspecciona errores:</strong> Abre la consola de navegador (F12 → Console) para ver si hay errores JavaScript.</li>';
			echo '<li><strong>Revisa el servidor:</strong> En Herramientas → Registros, busca errores de PHP.</li>';
			echo '</ol>';
			echo '</div>';

			echo '</div>';
		}
	);
});

// IMPORTANTE: Este código se ejecuta SIEMPRE
// Para desactivarlo, simplemente elimina o comenta estas líneas en functions.php
