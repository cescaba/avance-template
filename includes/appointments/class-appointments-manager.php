<?php
/**
 * Appointments Admin - Gestor independiente de citas
 * Cada admin es responsable de su propia tabla y lógica
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Appointments_Manager {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'avance_agendamiento_contacto';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// AJAX handlers específicos de citas
		add_action('wp_ajax_appointments_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_appointments_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_appointments_download_all', [$this, 'ajax_download_all']);
		add_action('wp_ajax_appointments_delete_record', [$this, 'ajax_delete_record']);

		// Cargar clases de citas para el frontend
		$this->load_appointment_classes();

		// Crear tabla en BD con prioridad alta
		add_action('init', [$this, 'maybe_create_table'], 0);
		add_action('admin_init', [$this, 'maybe_create_table'], 0);
	}

	private function load_appointment_classes() {
		// Cargar clase de contacto agendamiento para el admin
		require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';
	}

	public function maybe_create_table() {
		// Crear tabla de agendamientos contacto
		if (class_exists('Avance_Agendamiento_Contacto_DB')) {
			Avance_Agendamiento_Contacto_DB::create_table();
		}
	}

	public function register_menu() {
		add_menu_page(
			'Citas Agendadas',
			'Citas',
			'manage_options',
			'appointments-admin',
			[$this, 'render_admin_page'],
			'dashicons-calendar-alt',
			30
		);
	}

	public function enqueue_admin_assets($hook) {
		// CSS encolado desde functions.php
	}

	public function render_admin_page() {
		global $wpdb;

		// Asegurar que la tabla existe
		$this->maybe_create_table();

		// Traer datos de la tabla
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY fecha_creacion DESC LIMIT 50");
		if ($records === null) {
			$records = array();
		}
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"));

		require_once get_template_directory() . '/includes/admin/class-admin-table-builder.php';

		$config = array(
			'title' => 'Citas Agendadas',
			'subtitle' => 'Gestiona todas las citas y agendamientos',

			'columns' => array(
				array('label' => 'ID', 'field' => 'id'),
				array('label' => 'Nombre', 'field' => 'nombre'),
				array('label' => 'WhatsApp', 'field' => 'whatsapp', 'type' => 'whatsapp'),
				array('label' => 'Tema', 'field' => 'tema'),
				array('label' => 'Fecha', 'field' => 'fecha'),
				array('label' => 'Hora', 'field' => 'hora'),
				array('label' => 'Estado', 'field' => 'estado'),
			),

			'filters' => array('search' => true),

			'stats' => array(
				array(
					'label' => 'Total Citas',
					'value' => $total,
					'icon' => '📊',
					'class' => 'total'
				),
			),

			'data' => $records,
			'total' => $total,

			// Acciones AJAX específicas de este admin
			'ajax_actions' => array(
				'get_record' => 'appointments_get_record',
				'download_record' => 'appointments_download_record',
				'download_all' => 'appointments_download_all',
				'delete_record' => 'appointments_delete_record',
			),

			'nonce_action' => 'appointments_admin',
		);

		$builder = new Admin_Table_Builder($config);
		echo '<div class="avance-admin-container">';
		echo $builder->render();
		echo '</div>';
	}

	// ========================================
	// AJAX HANDLERS - Independientes de citas
	// ========================================

	public function ajax_get_record() {
		check_ajax_referer('appointments_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$record = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$this->table_name} WHERE id = %d",
			$id
		));

		if (!$record) {
			wp_send_json_error(['message' => 'Cita no encontrada']);
		}

		$clean_whatsapp = preg_replace('/[^0-9]/', '', $record->whatsapp);

		$html = '<div class="avance-detail-view">';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">ID</div><div class="avance-detail-value">#' . esc_html($record->id) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Nombre</div><div class="avance-detail-value">' . esc_html($record->nombre) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">WhatsApp</div><div class="avance-detail-value"><a href="https://wa.me/' . esc_attr($clean_whatsapp) . '" target="_blank">' . esc_html($record->whatsapp) . '</a></div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Tema</div><div class="avance-detail-value">' . esc_html($record->tema) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Fecha</div><div class="avance-detail-value">' . esc_html(wp_date('d/m/Y', strtotime($record->fecha))) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Hora</div><div class="avance-detail-value">' . esc_html($record->hora) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Estado</div><div class="avance-detail-value">' . esc_html(ucfirst($record->estado)) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Fecha Registro</div><div class="avance-detail-value">' . esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion))) . '</div></div>';
		$html .= '</div>';

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_download_record() {
		check_ajax_referer('appointments_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$record = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$this->table_name} WHERE id = %d",
			$id
		));

		if (!$record) {
			wp_send_json_error(['message' => 'Cita no encontrada']);
		}

		$csv = "ID,Nombre,WhatsApp,Tema,Fecha,Hora,Estado,Fecha Registro\n";
		$csv .= sprintf(
			'"%d","%s","%s","%s","%s","%s","%s","%s"' . "\n",
			$record->id,
			str_replace('"', '""', $record->nombre),
			str_replace('"', '""', $record->whatsapp),
			str_replace('"', '""', $record->tema),
			$record->fecha,
			$record->hora,
			$record->estado,
			$record->fecha_creacion
		);

		$filename = 'cita-' . $id . '-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_download_all() {
		check_ajax_referer('appointments_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY fecha_creacion DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay citas para descargar']);
		}

		$csv = "ID,Nombre,WhatsApp,Tema,Fecha,Hora,Estado,Fecha Registro\n";
		foreach ($records as $record) {
			$csv .= sprintf(
				'"%d","%s","%s","%s","%s","%s","%s","%s"' . "\n",
				$record->id,
				str_replace('"', '""', $record->nombre),
				str_replace('"', '""', $record->whatsapp),
				str_replace('"', '""', $record->tema),
				$record->fecha,
				$record->hora,
				$record->estado,
				$record->fecha_creacion
			);
		}

		$filename = 'citas-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('appointments_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		if (!$id) {
			wp_send_json_error(['message' => 'ID inválido']);
		}

		$result = $wpdb->delete($this->table_name, ['id' => $id], ['%d']);

		if ($result === false) {
			wp_send_json_error(['message' => 'Error al eliminar: ' . $wpdb->last_error]);
		}

		wp_send_json_success(['message' => 'Cita eliminada correctamente']);
	}
}

new Avance_Appointments_Manager();
