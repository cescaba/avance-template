<?php
/**
 * Diagnosticos Admin - Gestor de diagnósticos
 * Tabla: wp_avance_diagnosticos
 * Extiende Avance_Admin_Template para renderizado personalizado
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnosticos_Admin extends Avance_Admin_Template {

	private $wpdb_table;

	public function __construct() {
		global $wpdb;
		$this->wpdb_table = $wpdb->prefix . 'avance_diagnosticos';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_diagnosticos_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_diagnosticos_delete_record', [$this, 'ajax_delete_record']);
		add_action('wp_ajax_diagnosticos_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_diagnosticos_download_all', [$this, 'ajax_download_all']);
	}

	public function register_menu() {
		add_menu_page(
			'Diagnósticos',
			'Diagnósticos',
			'manage_options',
			'diagnosticos-admin',
			[$this, 'render_page'],
			'dashicons-clipboard',
			28
		);
	}

	public function render_page() {
		global $wpdb;

		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY fecha_creacion DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb_table}"));
		$nonce = wp_create_nonce('diagnosticos_admin');

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre_completo', 'label' => 'Nombre'],
			['field' => 'email', 'label' => 'Email'],
			['field' => 'whatsapp', 'label' => 'WhatsApp'],
			['field' => 'fecha_creacion', 'label' => 'Fecha'],
		];

		parent::__construct(
			'Diagnósticos',
			'Gestiona todos los diagnósticos completados',
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
			case 'nombre_completo':
				return '<strong>' . esc_html($record->nombre_completo ?? '—') . '</strong>';
			case 'email':
				return '<a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email ?? '—') . '</a>';
			case 'whatsapp':
				return '<a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank">' . esc_html($record->whatsapp ?? '—') . '</a>';
			case 'fecha_creacion':
				return esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion)));
			default:
				return esc_html($record->$field ?? '—');
		}
	}

	public function ajax_get_record() {
		check_ajax_referer('diagnosticos_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Diagnóstico no encontrado']);
		}

		$html = '<div class="modal-content">';
		$html .= '<div class="modal-field"><strong>ID:</strong> ' . esc_html($record->id) . '</div>';
		$html .= '<div class="modal-field"><strong>Nombre:</strong> ' . esc_html($record->nombre_completo ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Email:</strong> <a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email ?? '—') . '</a></div>';
		$html .= '<div class="modal-field"><strong>WhatsApp:</strong> <a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank">' . esc_html($record->whatsapp ?? '—') . '</a></div>';
		$html .= '<div class="modal-field"><strong>Fecha:</strong> ' . esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion))) . '</div>';
		$html .= '</div>';

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('diagnosticos_admin', 'nonce');

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

		wp_send_json_success(['message' => 'Diagnóstico eliminado correctamente']);
	}

	public function ajax_download_record() {
		check_ajax_referer('diagnosticos_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Diagnóstico no encontrado']);
		}

		$csv = $this->generate_csv_record($record);
		$this->send_csv_download($csv, 'diagnostico_' . $id . '_' . date('Y-m-d_H-i-s') . '.csv');
	}

	public function ajax_download_all() {
		check_ajax_referer('diagnosticos_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY fecha_creacion DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay diagnósticos para descargar']);
		}

		$csv = $this->generate_csv_all($records);
		$this->send_csv_download($csv, 'diagnosticos_' . date('Y-m-d_H-i-s') . '.csv');
	}
}

new Avance_Diagnosticos_Admin();
