<?php
/**
 * Appointments Manager - Orquestador Principal
 *
 * Gestiona la inicialización del sistema de agendamiento
 * incluyendo clases, hooks y admin panel.
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Appointments_Manager {

	/**
	 * Inicializar sistema de agendamiento
	 */
	public static function init() {
		// Cargar clases
		self::load_classes();

		// Crear tabla en BD
		self::create_tables();

		// Registrar AJAX handlers
		self::register_ajax_handlers();

		// Registrar admin panel
		self::register_admin_panel();
	}

	/**
	 * Cargar clases del sistema
	 */
	private static function load_classes() {
		$appointments_dir = get_template_directory() . '/includes/appointments/';

		require_once $appointments_dir . 'class-appointment-validator.php';
		require_once $appointments_dir . 'class-appointment-db.php';
		require_once $appointments_dir . 'class-appointment-handler.php';
	}

	/**
	 * Crear tablas en la BD
	 */
	private static function create_tables() {
		if (class_exists('Avance_Appointment_DB')) {
			Avance_Appointment_DB::create_table();
		}
	}

	/**
	 * Registrar AJAX handlers
	 */
	private static function register_ajax_handlers() {
		if (class_exists('Avance_Appointment_Handler')) {
			new Avance_Appointment_Handler();
		}
	}

	/**
	 * Registrar panel de admin
	 */
	private static function register_admin_panel() {
		add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));
	}

	/**
	 * Enqueue de estilos del admin
	 */
	public static function enqueue_admin_styles($hook) {
		if ($hook !== 'toplevel_page_avance-appointments') {
			return;
		}

		wp_enqueue_style('wp-admin');
		wp_enqueue_style(
			'avance-admin-premium',
			get_template_directory_uri() . '/assets/css/admin-premium.css',
			array(),
			wp_get_theme()->get('Version')
		);
	}

	/**
	 * Agregar menú en admin
	 */
	public static function add_admin_menu() {
		add_menu_page(
			'Citas Agendadas',
			'Citas',
			'manage_options',
			'avance-appointments',
			array(__CLASS__, 'render_admin_page'),
			'dashicons-calendar-alt',
			30
		);
	}

	/**
	 * Renderizar página admin
	 */
	public static function render_admin_page() {
		if (!current_user_can('manage_options')) {
			wp_die('No tienes permiso');
		}

		$template_path = get_template_directory() . '/includes/admin/appointments/admin-appointments.php';

		if (file_exists($template_path)) {
			include $template_path;
		}
	}
}

// Inicializar al cargar WordPress
add_action('wp_loaded', array('Avance_Appointments_Manager', 'init'));

/**
 * AJAX handler para obtener detalles de la cita
 */
add_action('wp_ajax_avance_get_appointment', function() {
	if (!current_user_can('manage_options')) {
		wp_send_json_error('No tienes permiso');
	}

	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_admin')) {
		wp_send_json_error('Nonce inválido');
	}

	global $wpdb;

	$appointment_id = intval($_POST['id'] ?? 0);
	$appointment = $wpdb->get_row($wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}avance_appointments WHERE id = %d",
		$appointment_id
	));

	if (!$appointment) {
		wp_send_json_error('Cita no encontrada');
	}

	ob_start();
	?>
	<div class="avance-modal-header">
		<h2>Cita #<?php echo esc_html($appointment->id); ?></h2>
	</div>

	<div class="avance-modal-body">
		<div class="avance-detail-grid">
			<div class="avance-detail-field">
				<label>Nombre</label>
				<p class="avance-detail-value"><?php echo esc_html($appointment->nombre); ?></p>
			</div>

			<div class="avance-detail-field">
				<label>WhatsApp</label>
				<p class="avance-detail-value">
					<a href="https://wa.me/<?php echo esc_attr($appointment->whatsapp); ?>" target="_blank" rel="noopener" class="avance-link">
						<?php echo esc_html($appointment->whatsapp); ?>
					</a>
				</p>
			</div>

			<div class="avance-detail-field">
				<label>Servicio</label>
				<p class="avance-detail-value"><?php echo esc_html($appointment->servicio); ?></p>
			</div>

			<div class="avance-detail-field">
				<label>Fecha</label>
				<p class="avance-detail-value"><strong><?php echo esc_html(wp_date('d/m/Y', strtotime($appointment->fecha))); ?></strong></p>
			</div>

			<div class="avance-detail-field">
				<label>Hora</label>
				<p class="avance-detail-value"><?php echo esc_html($appointment->hora); ?></p>
			</div>

			<div class="avance-detail-field">
				<label>Estado</label>
				<p class="avance-detail-value">
					<?php if ($appointment->status === 'confirmada') : ?>
						<span class="avance-badge avance-badge-success">✓ Confirmada</span>
					<?php else : ?>
						<span class="avance-badge avance-badge-secondary">⏳ Pendiente</span>
					<?php endif; ?>
				</p>
			</div>

			<div class="avance-detail-field">
				<label>IP</label>
				<p class="avance-detail-value"><code><?php echo esc_html($appointment->ip_address); ?></code></p>
			</div>

			<div class="avance-detail-field">
				<label>Fecha y Hora de Registro</label>
				<p class="avance-detail-value"><?php echo esc_html(wp_date('d/m/Y H:i:s', strtotime($appointment->created_at))); ?></p>
			</div>
		</div>
	</div>
	<?php

	$html = ob_get_clean();

	wp_send_json_success(array('html' => $html));
});
