<?php
/**
 * Agendamientos Admin - Gestor de agendamientos de contacto
 * Tabla: wp_avance_agendamiento_contacto
 * Extiende Avance_Admin_Template para renderizado personalizado
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Agendamientos_Admin extends Avance_Admin_Template {

	private $wpdb_table;

	public function __construct() {
		global $wpdb;
		$this->wpdb_table = $wpdb->prefix . 'avance_agendamiento_contacto';

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre', 'label' => 'Nombre'],
			['field' => 'whatsapp', 'label' => 'WhatsApp'],
			['field' => 'tema', 'label' => 'Tema'],
			['field' => 'fecha', 'label' => 'Fecha'],
			['field' => 'hora', 'label' => 'Hora'],
			['field' => 'estado', 'label' => 'Estado'],
			['field' => 'fecha_creacion', 'label' => 'Creado'],
		];

		$nonce = wp_create_nonce('agendamientos_admin');

		parent::__construct(
			'Agendamientos Recibidos',
			'Gestiona los agendamientos de sesiones',
			[],
			0,
			$columns,
			$nonce,
			$this->wpdb_table,
			'agendamientos'
		);

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_agendamientos_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_agendamientos_delete_record', [$this, 'ajax_delete_record']);
		add_action('wp_ajax_agendamientos_update_status', [$this, 'ajax_update_status']);
		add_action('wp_ajax_agendamientos_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_agendamientos_download_all', [$this, 'ajax_download_all']);
	}

	public function register_menu() {
		add_menu_page(
			'Agendamientos',
			'Agendamientos',
			'manage_options',
			'agendamientos-admin',
			[$this, 'render_page'],
			'dashicons-calendar-alt',
			26
		);
	}

	public function render_page() {
		global $wpdb;

		$this->records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY fecha DESC, hora DESC LIMIT 100");
		$this->total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb_table}"));

		$this->render();
	}

	protected function format_field($field, $record) {
		switch ($field) {
			case 'nombre':
				return '<strong>' . esc_html($record->nombre) . '</strong>';
			case 'whatsapp':
				return '<a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank">' . esc_html($record->whatsapp) . '</a>';
			case 'tema':
				return esc_html($record->tema);
			case 'fecha':
				return '<strong>' . esc_html(wp_date('d/m/Y', strtotime($record->fecha))) . '</strong>';
			case 'hora':
				return esc_html(wp_date('H:i', strtotime($record->hora)));
			case 'estado':
				$status_class = 'status-' . esc_attr($record->estado);
				return '<span class="admin-badge ' . $status_class . '">' . esc_html(ucfirst($record->estado)) . '</span>';
			case 'fecha_creacion':
				return esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion)));
			default:
				return esc_html($record->$field ?? '—');
		}
	}

	public function ajax_get_record() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$record = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$this->wpdb_table} WHERE id = %d",
			$id
		));

		if (!$record) {
			wp_send_json_error(['message' => 'Agendamiento no encontrado']);
		}

		$html = $this->generate_modal_html($record);

		wp_send_json_success(['html' => $html]);
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

		$result = $wpdb->delete($this->wpdb_table, ['id' => $id], ['%d']);

		if ($result === false) {
			wp_send_json_error(['message' => 'Error al eliminar']);
		}

		wp_send_json_success(['message' => 'Agendamiento eliminado correctamente']);
	}

	public function ajax_update_status() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);
		$status = sanitize_text_field($_POST['status'] ?? '');

		if (!$id || !$status) {
			wp_send_json_error(['message' => 'Datos inválidos']);
		}

		$valid_statuses = ['pendiente', 'confirmado', 'completado', 'cancelado'];
		if (!in_array($status, $valid_statuses)) {
			wp_send_json_error(['message' => 'Estado inválido']);
		}

		$result = $wpdb->update(
			$this->wpdb_table,
			['estado' => $status],
			['id' => $id],
			['%s'],
			['%d']
		);

		if ($result === false) {
			wp_send_json_error(['message' => 'Error al actualizar']);
		}

		wp_send_json_success(['message' => 'Estado actualizado correctamente']);
	}

	public function ajax_download_record() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$record = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$this->wpdb_table} WHERE id = %d",
			$id
		));

		if (!$record) {
			wp_send_json_error(['message' => 'Agendamiento no encontrado']);
		}

		$csv = $this->generate_csv_record($record);
		$this->send_csv_download($csv, 'agendamiento_' . $id . '_' . date('Y-m-d_H-i-s') . '.csv');
	}

	public function ajax_download_all() {
		check_ajax_referer('agendamientos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY fecha DESC, hora DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay agendamientos para descargar']);
		}

		$csv = $this->generate_csv_all($records);
		$this->send_csv_download($csv, 'agendamientos_' . date('Y-m-d_H-i-s') . '.csv');
	}
}

new Avance_Agendamientos_Admin();
