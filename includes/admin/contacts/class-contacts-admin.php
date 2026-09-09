<?php
/**
 * Contacts Admin - Gestor de formularios de contacto
 * Tabla: wp_avance_contacts
 * Extiende Avance_Admin_Template para renderizado personalizado
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Contacts_Admin extends Avance_Admin_Template {

	private $wpdb_table;

	public function __construct() {
		global $wpdb;
		$this->wpdb_table = $wpdb->prefix . 'avance_contacts';

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre', 'label' => 'Nombre'],
			['field' => 'email', 'label' => 'Email'],
			['field' => 'numero', 'label' => 'WhatsApp'],
			['field' => 'asunto', 'label' => 'Asunto'],
			['field' => 'created_at', 'label' => 'Fecha'],
		];

		$nonce = wp_create_nonce('contacts_admin');

		parent::__construct(
			'Formularios Recibidos',
			'Gestiona los contactos enviados desde el formulario',
			[],
			0,
			$columns,
			$nonce,
			$this->wpdb_table,
			'contacts'
		);

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_contacts_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_contacts_delete_record', [$this, 'ajax_delete_record']);
		add_action('wp_ajax_contacts_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_contacts_download_all', [$this, 'ajax_download_all']);
	}

	public function register_menu() {
		add_menu_page(
			'Formularios',
			'Formularios',
			'manage_options',
			'contacts-admin',
			[$this, 'render_page'],
			'dashicons-email-alt',
			25
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
				return '<strong>' . esc_html($record->nombre) . '</strong>';
			case 'email':
				return '<a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email) . '</a>';
			case 'numero':
				return '<a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->numero)) . '" target="_blank">' . esc_html($record->numero) . '</a>';
			case 'created_at':
				return esc_html(wp_date('d/m/Y H:i', strtotime($record->created_at)));
			default:
				return esc_html($record->$field ?? '—');
		}
	}

	public function ajax_get_record() {
		check_ajax_referer('contacts_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$query = "SELECT * FROM " . $this->wpdb_table . " WHERE id = %d";
		$record = $wpdb->get_row($wpdb->prepare($query, $id));

		if (!$record) {
			wp_send_json_error(['message' => 'Contacto no encontrado']);
		}

		$html = $this->generate_modal_html($record);

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('contacts_admin', 'nonce');

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

		wp_send_json_success(['message' => 'Contacto eliminado correctamente']);
	}

	public function ajax_download_record() {
		check_ajax_referer('contacts_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$id = intval($_POST['id'] ?? 0);

		$query = "SELECT * FROM " . $this->wpdb_table . " WHERE id = %d";
		$record = $wpdb->get_row($wpdb->prepare($query, $id));

		if (!$record) {
			wp_send_json_error(['message' => 'Contacto no encontrado']);
		}

		$csv = $this->generate_csv_record($record);
		$this->send_csv_download($csv, 'contacto_' . $id . '_' . date('Y-m-d_H-i-s') . '.csv');
	}

	public function ajax_download_all() {
		check_ajax_referer('contacts_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$query = "SELECT * FROM " . $this->wpdb_table . " ORDER BY id ASC";
		$records = $wpdb->get_results($query);

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay contactos para descargar']);
		}

		$csv = $this->generate_csv_all($records);
		$this->send_csv_download($csv, 'contactos_' . date('Y-m-d_H-i-s') . '.csv');
	}
}

new Avance_Contacts_Admin();
