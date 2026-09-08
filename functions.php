<?php
/**
 * Avance Template - Functions
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

// Versión de base de datos (aumentar cuando cambien tablas)
if (!defined('AVANCE_DB_VERSION')) {
	define('AVANCE_DB_VERSION', '1.0.7');
}

// Configuración de límites de agendamiento (Issue 15)
if (!defined('AVANCE_MAX_BOOKING_DAYS')) {
	define('AVANCE_MAX_BOOKING_DAYS', 180);
}

// Invalidar caché de calendario cuando se completa una reserva
add_action('wp_footer', function() {
	?>
	<script>
	(function() {
		const originalFetch = window.fetch;
		window.fetch = function(...args) {
			return originalFetch.apply(this, args).then(response => {
				const clonedResponse = response.clone();

				clonedResponse.json().then(data => {
					// Si la respuesta tiene invalidate_cache, limpiar caché
					if (data.success && data.invalidate_cache && window.invalidateAllHoursCache) {
						window.invalidateAllHoursCache();
					}
				}).catch(() => {}); // Ignorar errores de parsing

				return response;
			});
		};
	})();
	</script>
	<?php
});

// Registrar admin CSS una sola vez para todos los admins
add_action('admin_enqueue_scripts', function($hook) {
	// DEBUG: Ver exactamente qué hook está siendo pasado
	error_log('=== Admin Enqueue Hook: ' . $hook);

	wp_register_style(
		'avanem-admin-premium',
		get_template_directory_uri() . '/assets/css/admin-premium.css',
		['wp-admin'],
		wp_get_theme()->get('Version')
	);

	error_log('CSS Registrado: avanem-admin-premium');

	// Enqueue en todos los admins del tema
	if (strpos($hook, 'toplevel_page_form-section-admin') !== false ||
		strpos($hook, 'toplevel_page_appointments-admin') !== false ||
		strpos($hook, 'toplevel_page_diagnostico-admin') !== false ||
		strpos($hook, 'toplevel_page_servicios-admin') !== false) {
		error_log('Encolando CSS para: ' . $hook);
		wp_enqueue_style('avanem-admin-premium');
	} else {
		error_log('Screen no coincide. Hook: ' . $hook);
	}
}, 1);

// Incluir configuración centralizada
require_once get_template_directory() . '/config/settings.php';
require_once get_template_directory() . '/config/theme-config.php';

// Incluir servicios core
require_once get_template_directory() . '/includes/core/class-logger.php';  // Logger con levels
require_once get_template_directory() . '/includes/core/class-core-service.php';
require_once get_template_directory() . '/includes/core/class-ajax-handler.php';

// Instanciar servicios core
new Avance_Core_Service();
new Avance_Ajax_Handler();

// Optimizaciones de rendimiento
require_once get_template_directory() . '/includes/performance/class-performance-optimizer.php';
new Avance_Performance_Optimizer();

// Setup y performance de base de datos
require_once get_template_directory() . '/includes/database/setup/class-database-setup.php';
require_once get_template_directory() . '/includes/database/setup/class-database-performance.php';
new Avance_Database_Setup();
new Avance_Database_Performance();

// Customización de checkout
require_once get_template_directory() . '/includes/checkout/class-checkout-customizer.php';
new Avance_Checkout_Customizer();

// Validación SMTP asincrónica
require_once get_template_directory() . '/includes/smtp/class-smtp-validator.php';
new Avance_Smtp_Validator();

// ============================================================
// SEGURIDAD Y VALIDACIÓN GLOBAL
// ============================================================

// Manejador central (orquesta toda la seguridad)
require_once get_template_directory() . '/includes/security/handlers/class-global-form-handler.php';

// Validadores custom por formulario
require_once get_template_directory() . '/includes/validators/forms/class-form-validators.php';

// ============================================================
// SEGURIDAD: RATE LIMITING
// ============================================================

require_once get_template_directory() . '/includes/security/rate-limiting/class-rate-limiter.php';

// ============================================================
// SEGURIDAD: DETECCIÓN DE AMENAZAS
// ============================================================

require_once get_template_directory() . '/includes/security/detection/class-ip-blacklist.php';
require_once get_template_directory() . '/includes/security/detection/class-spam-detector.php';
require_once get_template_directory() . '/includes/security/detection/class-attack-notifier.php';

// ============================================================
// SEGURIDAD: CACHÉ Y OPTIMIZACIÓN
// ============================================================

require_once get_template_directory() . '/includes/security/cache/class-validation-cache.php';

// ============================================================
// JOBS Y QUEUE
// ============================================================

require_once get_template_directory() . '/includes/queue/class-job-queue.php';

// ============================================================
// CLASES DE CONTACTOS
// ============================================================

require_once get_template_directory() . '/includes/validators/contact/class-contact-validator.php';
require_once get_template_directory() . '/includes/database/contacts/class-contact-db.php';
require_once get_template_directory() . '/includes/database/contacts/class-contact-form-handler.php';

// CLASES DE PDF DOWNLOADS
// ============================================================

require_once get_template_directory() . '/includes/validators/pdf-downloads/class-pdf-download-validator.php';
require_once get_template_directory() . '/includes/database/pdf-downloads/class-pdf-download-db.php';
require_once get_template_directory() . '/includes/database/pdf-downloads/class-pdf-download-handler.php';

// ============================================================
// ADMIN SYSTEM
// ============================================================

require_once get_template_directory() . '/includes/admin/class-admin-table-builder.php';

// Admin - Form Section, Diagnósticos, Servicios
require_once get_template_directory() . '/includes/admin/form-section/class-form-section-admin.php';
require_once get_template_directory() . '/includes/admin/diagnostico/class-diagnostico-admin.php';
require_once get_template_directory() . '/includes/admin/servicios/class-servicios-empresas-admin.php';

// Incluir orquestador de AGENDAMIENTO
require_once get_template_directory() . '/includes/appointments/class-appointments-manager.php';

// Incluir orquestador de DIAGNÓSTICO
require_once get_template_directory() . '/includes/managers/diagnostico/class-diagnostico-manager.php';

// Incluir orquestador de SERVICIOS EMPRESAS
require_once get_template_directory() . '/includes/managers/servicios/class-servicios-empresas-manager.php';

// Incluir clases de AGENDAMIENTO DE SESIONES
require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';

// Incluir clases de CALENDARIO (disponibilidad)
require_once get_template_directory() . '/includes/database/calendario/class-calendario-reservas-db.php';
require_once get_template_directory() . '/includes/database/calendario/class-calendario-reservas-mentoria-db.php';
require_once get_template_directory() . '/includes/database/calendario/handler-mentoria.php';
require_once get_template_directory() . '/includes/database/calendario/handler-mentoria-booking.php';

// Incluir clases de PROPUESTAS
require_once get_template_directory() . '/includes/database/proposals/class-proposal-db.php';
require_once get_template_directory() . '/includes/database/proposals/class-proposal-handler.php';

// Incluir actualización de precios de mentoría
require_once get_template_directory() . '/includes/mentoria/update-mentoria-prices.php';

// Incluir checkout de mentoría
require_once get_template_directory() . '/includes/mentoria/class-mentoria-checkout.php';

// Incluir WhatsApp
require_once get_template_directory() . '/includes/whatsapp/floating-button.php';

// Incluir COMPONENTES REUTILIZABLES
require_once get_template_directory() . '/includes/database/form-sesiones/class-form-component.php';

// ============================================================
// COMPRA DE LIBRO - OBTENER POR CATEGORÍA "LIBRO" + NOMBRE "B2B"
// ============================================================

add_action('wp_ajax_nopriv_comprar_libro', function() {
	$productos = wc_get_products([
		'category' => 'libro',
		's' => 'B2B',
		'limit' => 1
	]);

	if (empty($productos)) {
		wp_send_json_error(['message' => 'Producto B2B no encontrado en categoría Libro']);
		return;
	}

	$producto_id = $productos[0]->get_id();
	WC()->cart->empty_cart();
	WC()->cart->add_to_cart($producto_id, 1);
	wp_send_json_success(['message' => 'Producto añadido']);
});

add_action('wp_ajax_comprar_libro', function() {
	$productos = wc_get_products([
		'category' => 'libro',
		's' => 'B2B',
		'limit' => 1
	]);

	if (empty($productos)) {
		wp_send_json_error(['message' => 'Producto B2B no encontrado en categoría Libro']);
		return;
	}

	$producto_id = $productos[0]->get_id();
	WC()->cart->empty_cart();
	WC()->cart->add_to_cart($producto_id, 1);
	wp_send_json_success(['message' => 'Producto añadido']);
});

