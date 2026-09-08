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

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_agendamientos_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_agendamientos_delete_record', [$this, 'ajax_delete_record']);
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
			27
		);
	}

	public function render_page() {
		global $wpdb;

		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY fecha DESC, hora DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb_table}"));
		$nonce = wp_create_nonce('agendamientos_admin');

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre', 'label' => 'Nombre'],
			['field' => 'whatsapp', 'label' => 'WhatsApp'],
			['field' => 'tema', 'label' => 'Tema'],
			['field' => 'fecha', 'label' => 'Fecha'],
			['field' => 'hora', 'label' => 'Hora'],
		];

		parent::__construct(
			'Agendamientos de Sesiones',
			'Gestiona todos los agendamientos de sesiones',
			$records,
			$total,
			$columns,
			$nonce,
			$this->wpdb_table
		);

		$this->render();
	}

	protected function format_field($field, $record) {
		switch ($field) {
			case 'nombre':
				return '<strong>' . esc_html($record->nombre ?? '—') . '</strong>';
			case 'whatsapp':
				return '<a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank">' . esc_html($record->whatsapp ?? '—') . '</a>';
			case 'fecha':
				return esc_html(wp_date('d/m/Y', strtotime($record->fecha)));
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

		$html = '<div class="modal-content">';
		$html .= '<div class="modal-field"><strong>ID:</strong> ' . esc_html($record->id) . '</div>';
		$html .= '<div class="modal-field"><strong>Nombre:</strong> ' . esc_html($record->nombre ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>WhatsApp:</strong> <a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank">' . esc_html($record->whatsapp ?? '—') . '</a></div>';
		$html .= '<div class="modal-field"><strong>Tema:</strong> ' . esc_html($record->tema ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Fecha:</strong> ' . esc_html(wp_date('d/m/Y', strtotime($record->fecha))) . '</div>';
		$html .= '<div class="modal-field"><strong>Hora:</strong> ' . esc_html($record->hora ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Estado:</strong> ' . esc_html($record->estado ?? 'pendiente') . '</div>';
		$html .= '</div>';

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
