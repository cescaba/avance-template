<?php
/**
 * Agendamientos Admin - Gestor de agendamientos de contacto
 * Muestra agendamientos con fecha y hora desde calendario
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Agendamientos_Admin {

	private $agendamientos_table;
	private $calendario_table;

	public function __construct() {
		global $wpdb;
		$this->agendamientos_table = $wpdb->prefix . 'avance_agendamiento_contacto';
		$this->calendario_table = $wpdb->prefix . 'avance_calendario_reservas';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// AJAX handlers
		add_action('wp_ajax_agendamientos_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_agendamientos_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_agendamientos_download_all', [$this, 'ajax_download_all']);
		add_action('wp_ajax_agendamientos_delete_record', [$this, 'ajax_delete_record']);
	}

	public function register_menu() {
		add_menu_page(
			'Agendamientos',
			'Agendamientos',
			'manage_options',
			'agendamientos-admin',
			[$this, 'render_admin_page'],
			'dashicons-calendar',
			28
		);
	}

	public function enqueue_admin_assets($hook) {
		// CSS encolado desde functions.php
	}

	public function render_admin_page() {
		global $wpdb;

		// JOIN con la tabla de calendario para traer fecha y hora
		$query = $wpdb->prepare("
			SELECT
				a.id,
				a.nombre,
				a.whatsapp,
				a.tema,
				a.estado,
				c.fecha,
				c.hora,
				a.fecha_creacion
			FROM {$this->agendamientos_table} a
			LEFT JOIN {$this->calendario_table} c ON a.calendario_reserva_id = c.id
			ORDER BY a.fecha_creacion DESC
			LIMIT 100
		");

		$records = $wpdb->get_results($query);
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->agendamientos_table}"));

		require_once get_template_directory() . '/includes/admin/class-admin-table-builder.php';

		$config = array(
			'title' => 'Agendamientos',
			'subtitle' => 'Gestiona las sesiones agendadas',

			'columns' => array(
				array('label' => 'ID', 'field' => 'id'),
				array('label' => 'Cliente', 'field' => 'nombre'),
				array('label' => 'WhatsApp', 'field' => 'whatsapp'),
				array('label' => 'Tema', 'field' => 'tema'),
				array('label' => 'Fecha', 'field' => 'fecha'),
				array('label' => 'Hora', 'field' => 'hora'),
				array('label' => 'Estado', 'field' => 'estado'),
			),

			'filters' => array('search' => true),

			'stats' => array(
				array(
					'label' => 'Total Agendamientos',
					'value' => $total,
					'icon' => '📅',
					'class' => 'total'
				),
			),

			'data' => $records,
			'total' => $total,

			// Acciones AJAX
			'ajax_actions' => array(
				'get_record' => 'agendamientos_get_record',
				'download_record' => 'agendamientos_download_record',
				'download_all' => 'agendamientos_download_all',
				'delete_record' => 'agendamientos_delete_record',
			),

			'nonce_action' => 'agendamientos_admin',
		);

		$builder = new Admin_Table_Builder($config);
		echo $builder->render();
	}

	public function ajax_get_record() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$record = $wpdb->get_row($wpdb->prepare("
			SELECT
				a.id,
				a.nombre,
				a.whatsapp,
				a.tema,
				a.estado,
				c.fecha,
				c.hora,
				a.fecha_creacion
			FROM {$this->agendamientos_table} a
			LEFT JOIN {$this->calendario_table} c ON a.calendario_reserva_id = c.id
			WHERE a.id = %d
		", $id));

		if (!$record) {
			wp_send_json_error(['message' => 'Agendamiento no encontrado']);
		}

		$html = '';

		// ID
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">ID</div>';
		$html .= '<div class="modal-value">#' . esc_html($record->id) . '</div>';
		$html .= '</div>';

		// Cliente y WhatsApp
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Cliente</div>';
		$html .= '<div class="modal-value">' . esc_html($record->nombre) . '</div>';
		$html .= '</div>';

		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">WhatsApp</div>';
		$html .= '<div class="modal-value">' . esc_html($record->whatsapp) . '</div>';
		$html .= '</div>';

		// Tema y Estado
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Tema</div>';
		$html .= '<div class="modal-value">' . esc_html($record->tema) . '</div>';
		$html .= '</div>';

		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Estado</div>';
		$html .= '<div class="modal-value"><strong>' . esc_html($record->estado) . '</strong></div>';
		$html .= '</div>';

		// Fecha y Hora
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Fecha</div>';
		$html .= '<div class="modal-value">' . ($record->fecha ? esc_html(wp_date('d/m/Y', strtotime($record->fecha))) : '—') . '</div>';
		$html .= '</div>';

		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Hora</div>';
		$html .= '<div class="modal-value">' . ($record->hora ? esc_html($record->hora) : '—') . '</div>';
		$html .= '</div>';

		// Fecha de creación
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Registrado</div>';
		$html .= '<div class="modal-value">' . esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion))) . '</div>';
		$html .= '</div>';

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_download_record() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$record = $wpdb->get_row($wpdb->prepare("
			SELECT
				a.id,
				a.nombre,
				a.whatsapp,
				a.tema,
				a.estado,
				c.fecha,
				c.hora,
				a.fecha_creacion
			FROM {$this->agendamientos_table} a
			LEFT JOIN {$this->calendario_table} c ON a.calendario_reserva_id = c.id
			WHERE a.id = %d
		", $id));

		if (!$record) {
			wp_send_json_error(['message' => 'Agendamiento no encontrado']);
		}

		$csv = "ID,Cliente,WhatsApp,Tema,Fecha,Hora,Estado,Registrado\n";
		$csv .= sprintf(
			'"%d","%s","%s","%s","%s","%s","%s","%s"' . "\n",
			$record->id,
			str_replace('"', '""', $record->nombre),
			str_replace('"', '""', $record->whatsapp),
			str_replace('"', '""', $record->tema),
			$record->fecha ?? '',
			$record->hora ?? '',
			str_replace('"', '""', $record->estado),
			$record->fecha_creacion
		);

		$filename = 'agendamiento-' . $id . '-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_download_all() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("
			SELECT
				a.id,
				a.nombre,
				a.whatsapp,
				a.tema,
				a.estado,
				c.fecha,
				c.hora,
				a.fecha_creacion
			FROM {$this->agendamientos_table} a
			LEFT JOIN {$this->calendario_table} c ON a.calendario_reserva_id = c.id
			ORDER BY a.fecha_creacion DESC
		");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay agendamientos para descargar']);
		}

		$csv = "ID,Cliente,WhatsApp,Tema,Fecha,Hora,Estado,Registrado\n";
		foreach ($records as $record) {
			$csv .= sprintf(
				'"%d","%s","%s","%s","%s","%s","%s","%s"' . "\n",
				$record->id,
				str_replace('"', '""', $record->nombre),
				str_replace('"', '""', $record->whatsapp),
				str_replace('"', '""', $record->tema),
				$record->fecha ?? '',
				$record->hora ?? '',
				str_replace('"', '""', $record->estado),
				$record->fecha_creacion
			);
		}

		$filename = 'agendamientos-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		if (!$id) {
			wp_send_json_error(['message' => 'ID inválido']);
		}

		$result = $wpdb->delete($this->agendamientos_table, ['id' => $id], ['%d']);

		if ($result === false) {
			wp_send_json_error(['message' => 'Error al eliminar: ' . $wpdb->last_error]);
		}

		wp_send_json_success(['message' => 'Agendamiento eliminado correctamente']);
	}
}

new Avance_Agendamientos_Admin();
