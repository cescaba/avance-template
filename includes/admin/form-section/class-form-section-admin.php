<?php
/**
 * Form Section Admin - Gestor independiente de contactos
 * Cada admin es responsable de su propia tabla y lógica
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Form_Section_Admin {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'avance_contacts';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// AJAX handlers específicos de form-section
		add_action('wp_ajax_form_section_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_form_section_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_form_section_download_all', [$this, 'ajax_download_all']);
		add_action('wp_ajax_form_section_delete_record', [$this, 'ajax_delete_record']);
	}

	public function register_menu() {
		add_menu_page(
			'Formularios Recibidos',
			'Formularios',
			'manage_options',
			'form-section-admin',
			[$this, 'render_admin_page'],
			'dashicons-email-alt',
			25
		);
	}

	public function enqueue_admin_assets($hook) {
		// CSS encolado desde functions.php
	}

	public function render_admin_page() {
		global $wpdb;

		// Traer datos de la tabla
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY created_at DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"));

		require_once get_template_directory() . '/includes/admin/class-admin-table-builder.php';

		$config = array(
			'title' => 'Formularios Recibidos',
			'subtitle' => 'Contactos enviados desde form-section',

			'columns' => array(
				array('label' => 'ID', 'field' => 'id'),
				array('label' => 'Nombre', 'field' => 'nombre'),
				array('label' => 'Email', 'field' => 'email', 'type' => 'email'),
				array('label' => 'WhatsApp', 'field' => 'numero', 'type' => 'whatsapp'),
				array('label' => 'Asunto', 'field' => 'asunto'),
				array('label' => 'Fecha', 'field' => 'created_at', 'type' => 'date'),
			),

			'filters' => array('search' => true),

			'stats' => array(
				array(
					'label' => 'Contactos Recibidos',
					'value' => $total,
					'icon' => '📊',
					'class' => 'total'
				),
			),

			'data' => $records,
			'total' => $total,

			// Acciones AJAX específicas de este admin
			'ajax_actions' => array(
				'get_record' => 'form_section_get_record',
				'download_record' => 'form_section_download_record',
				'download_all' => 'form_section_download_all',
				'delete_record' => 'form_section_delete_record',
			),

			'nonce_action' => 'form_section_admin',
		);

		$builder = new Admin_Table_Builder($config);
		echo $builder->render();
	}

	// ========================================
	// AJAX HANDLERS - Independientes de form-section
	// ========================================

	public function ajax_get_record() {
		check_ajax_referer('form_section_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Contacto no encontrado']);
		}

		$html = '';

		// ID
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">ID</div>';
		$html .= '<div class="modal-value">#' . esc_html($record->id) . '</div>';
		$html .= '</div>';

		// Nombre y Email en fila
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Nombre</div>';
		$html .= '<div class="modal-value">' . esc_html($record->nombre) . '</div>';
		$html .= '</div>';

		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Email</div>';
		$html .= '<div class="modal-value"><a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email) . '</a></div>';
		$html .= '</div>';

		// WhatsApp y Asunto en fila
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">WhatsApp</div>';
		$clean_numero = preg_replace('/[^0-9]/', '', $record->numero);
		$html .= '<div class="modal-value"><a href="https://wa.me/' . esc_attr($clean_numero) . '" target="_blank">' . esc_html($record->numero) . '</a></div>';
		$html .= '</div>';

		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Asunto</div>';
		$html .= '<div class="modal-value">' . esc_html($record->asunto) . '</div>';
		$html .= '</div>';

		// Fecha
		$html .= '<div class="modal-field">';
		$html .= '<div class="modal-label">Fecha</div>';
		$html .= '<div class="modal-value">' . esc_html(wp_date('d/m/Y H:i', strtotime($record->created_at))) . '</div>';
		$html .= '</div>';

		// Mensaje ancho completo
		if (!empty($record->mensaje)) {
			$html .= '<div class="modal-field full-width">';
			$html .= '<div class="modal-label">Mensaje</div>';
			$html .= '<div class="modal-value">' . nl2br(esc_html($record->mensaje)) . '</div>';
			$html .= '</div>';
		}

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_download_record() {
		check_ajax_referer('form_section_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Contacto no encontrado']);
		}

		$csv = "ID,Nombre,Email,WhatsApp,Asunto,Mensaje,Fecha\n";
		$csv .= sprintf(
			'"%d","%s","%s","%s","%s","%s","%s"' . "\n",
			$record->id,
			str_replace('"', '""', $record->nombre),
			str_replace('"', '""', $record->email),
			str_replace('"', '""', $record->numero),
			str_replace('"', '""', $record->asunto),
			str_replace('"', '""', $record->mensaje),
			$record->created_at
		);

		$filename = 'contacto-' . $id . '-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_download_all() {
		check_ajax_referer('form_section_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY created_at DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay contactos para descargar']);
		}

		$csv = "ID,Nombre,Email,WhatsApp,Asunto,Mensaje,Fecha\n";
		foreach ($records as $record) {
			$csv .= sprintf(
				'"%d","%s","%s","%s","%s","%s","%s"' . "\n",
				$record->id,
				str_replace('"', '""', $record->nombre),
				str_replace('"', '""', $record->email),
				str_replace('"', '""', $record->numero),
				str_replace('"', '""', $record->asunto),
				str_replace('"', '""', $record->mensaje),
				$record->created_at
			);
		}

		$filename = 'contactos-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('form_section_admin', 'nonce');

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

		wp_send_json_success(['message' => 'Contacto eliminado correctamente']);
	}
}

new Avance_Form_Section_Admin();
