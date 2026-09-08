<?php
/**
 * Proposals Admin - Gestor de propuestas
 * Tabla: wp_avance_proposals
 * Extiende Avance_Admin_Template para renderizado personalizado
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Proposals_Admin extends Avance_Admin_Template {

	private $wpdb_table;

	public function __construct() {
		global $wpdb;
		$this->wpdb_table = $wpdb->prefix . 'avance_proposals';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_proposals_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_proposals_delete_record', [$this, 'ajax_delete_record']);
		add_action('wp_ajax_proposals_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_proposals_download_all', [$this, 'ajax_download_all']);
	}

	public function register_menu() {
		add_menu_page(
			'Propuestas',
			'Propuestas',
			'manage_options',
			'proposals-admin',
			[$this, 'render_page'],
			'dashicons-document',
			26
		);
	}

	public function render_page() {
		global $wpdb;

		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY created_at DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb_table}"));
		$nonce = wp_create_nonce('proposals_admin');

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre', 'label' => 'Nombre'],
			['field' => 'empresa', 'label' => 'Empresa'],
			['field' => 'servicio_interes', 'label' => 'Servicio'],
			['field' => 'status', 'label' => 'Estado'],
			['field' => 'created_at', 'label' => 'Fecha'],
		];

		parent::__construct(
			'Propuestas',
			'Gestiona todas las propuestas creadas',
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
			case 'status':
				$status_class = 'status-' . sanitize_html_class($record->status ?? 'unknown');
				return '<span class="status-badge ' . esc_attr($status_class) . '">' . esc_html($record->status ?? '—') . '</span>';
			case 'created_at':
				return esc_html(wp_date('d/m/Y H:i', strtotime($record->created_at)));
			default:
				return esc_html($record->$field ?? '—');
		}
	}

	public function ajax_get_record() {
		check_ajax_referer('proposals_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Propuesta no encontrada']);
		}

		$html = '<div class="modal-content">';
		$html .= '<div class="modal-field"><strong>ID:</strong> ' . esc_html($record->id) . '</div>';
		$html .= '<div class="modal-field"><strong>Nombre:</strong> ' . esc_html($record->nombre ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Empresa:</strong> ' . esc_html($record->empresa ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Email:</strong> <a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email ?? '—') . '</a></div>';
		$html .= '<div class="modal-field"><strong>WhatsApp:</strong> <a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank">' . esc_html($record->whatsapp ?? '—') . '</a></div>';
		$html .= '<div class="modal-field"><strong>Servicio:</strong> ' . esc_html($record->servicio_interes ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Desafío:</strong> ' . esc_html(substr($record->desafio_comercial ?? '—', 0, 100)) . '</div>';
		$html .= '<div class="modal-field"><strong>Estado:</strong> ' . esc_html($record->status ?? '—') . '</div>';
		$html .= '<div class="modal-field"><strong>Fecha:</strong> ' . esc_html(wp_date('d/m/Y H:i', strtotime($record->created_at))) . '</div>';
		$html .= '</div>';

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('proposals_admin', 'nonce');

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

		wp_send_json_success(['message' => 'Propuesta eliminada correctamente']);
	}

	public function ajax_download_record() {
		check_ajax_referer('proposals_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Propuesta no encontrada']);
		}

		$csv = $this->generate_csv_record($record);
		$this->send_csv_download($csv, 'propuesta_' . $id . '_' . date('Y-m-d_H-i-s') . '.csv');
	}

	public function ajax_download_all() {
		check_ajax_referer('proposals_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY created_at DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay propuestas para descargar']);
		}

		$csv = $this->generate_csv_all($records);
		$this->send_csv_download($csv, 'propuestas_' . date('Y-m-d_H-i-s') . '.csv');
	}
}

new Avance_Proposals_Admin();
