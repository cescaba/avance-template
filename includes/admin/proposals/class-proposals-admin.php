<?php
/**
 * Proposals Admin - Gestor de solicitudes de servicios empresariales
 * Tabla: wp_avance_servicios_empresas
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
		$this->wpdb_table = $wpdb->prefix . 'avance_servicios_empresas';

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre', 'label' => 'Nombre'],
			['field' => 'email', 'label' => 'Email'],
			['field' => 'empresa', 'label' => 'Empresa'],
			['field' => 'servicio_interes', 'label' => 'Servicio'],
			['field' => 'fecha_creacion', 'label' => 'Fecha'],
		];

		$nonce = wp_create_nonce('proposals_admin');

		parent::__construct(
			'Propuestas',
			'Gestiona todas las propuestas creadas',
			[],
			0,
			$columns,
			$nonce,
			$this->wpdb_table,
			'proposals'
		);

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

		$query = "SELECT * FROM " . $this->wpdb_table . " ORDER BY id ASC LIMIT 50";
		$this->records = $wpdb->get_results($query);

		$count_query = "SELECT COUNT(*) FROM " . $this->wpdb_table;
		$this->total = intval($wpdb->get_var($count_query));

		$this->render();
	}

	protected function format_field($field, $record) {
		switch ($field) {
			case 'nombre':
				return '<strong>' . esc_html($record->nombre ?? '—') . '</strong>';
			case 'email':
				return '<a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email ?? '—') . '</a>';
			case 'fecha_creacion':
				return esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion)));
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

		$query = "SELECT * FROM " . $this->wpdb_table . " WHERE id = %d";
		$record = $wpdb->get_row($wpdb->prepare($query, $id));

		if (!$record) {
			wp_send_json_error(['message' => 'Solicitud no encontrada']);
		}

		$html = $this->generate_modal_html($record);

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

		wp_send_json_success(['message' => 'Solicitud eliminada correctamente']);
	}

	public function ajax_download_record() {
		check_ajax_referer('proposals_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$query = "SELECT * FROM " . $this->wpdb_table . " WHERE id = %d";
		$record = $wpdb->get_row($wpdb->prepare($query, $id));

		if (!$record) {
			wp_send_json_error(['message' => 'Solicitud no encontrada']);
		}

		$csv = $this->generate_csv_record($record);
		$this->send_csv_download($csv, 'solicitud_' . $id . '_' . date('Y-m-d_H-i-s') . '.csv');
	}

	public function ajax_download_all() {
		check_ajax_referer('proposals_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$query = "SELECT * FROM " . $this->wpdb_table . " ORDER BY id ASC";
		$records = $wpdb->get_results($query);

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay solicitudes para descargar']);
		}

		$csv = $this->generate_csv_all($records);
		$this->send_csv_download($csv, 'solicitudes_' . date('Y-m-d_H-i-s') . '.csv');
	}
}

new Avance_Proposals_Admin();
