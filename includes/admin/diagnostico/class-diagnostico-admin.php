<?php
/**
 * Diagnostico Admin - Gestor independiente de diagnósticos
 * Cada admin es responsable de su propia tabla y lógica
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnostico_Admin {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'avance_diagnosticos';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// AJAX handlers específicos de diagnóstico
		add_action('wp_ajax_diagnostico_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_diagnostico_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_diagnostico_download_all', [$this, 'ajax_download_all']);
		add_action('wp_ajax_diagnostico_delete_record', [$this, 'ajax_delete_record']);
	}

	public function register_menu() {
		add_menu_page(
			'Diagnósticos',
			'Diagnósticos',
			'manage_options',
			'diagnostico-admin',
			[$this, 'render_admin_page'],
			'dashicons-format-status',
			26
		);
	}

	public function enqueue_admin_assets($hook) {
		if (strpos($hook, 'diagnostico-admin') === false) {
			return;
		}

		wp_enqueue_style(
			'avance-admin-premium',
			get_template_directory_uri() . '/assets/css/admin-premium.css',
			['wp-admin'],
			wp_get_theme()->get('Version')
		);
	}

	public function render_admin_page() {
		global $wpdb;

		// Traer datos de la tabla
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY fecha_creacion DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"));

		require_once get_template_directory() . '/includes/admin/class-admin-table-builder.php';

		$config = array(
			'title' => 'Diagnósticos Recibidos',
			'subtitle' => 'Resultados del quiz de diagnóstico',

			'columns' => array(
				array('label' => 'ID', 'field' => 'id'),
				array('label' => 'Nombre', 'field' => 'nombre_completo'),
				array('label' => 'Email', 'field' => 'email', 'type' => 'email'),
				array('label' => 'WhatsApp', 'field' => 'whatsapp', 'type' => 'whatsapp'),
				array('label' => 'Fecha', 'field' => 'fecha_creacion', 'type' => 'date'),
			),

			'filters' => array('search' => true),

			'stats' => array(
				array(
					'label' => 'Diagnósticos Recibidos',
					'value' => $total,
					'icon' => '📊',
					'class' => 'total'
				),
			),

			'data' => $records,
			'total' => $total,

			// Acciones AJAX específicas de este admin
			'ajax_actions' => array(
				'get_record' => 'diagnostico_get_record',
				'download_record' => 'diagnostico_download_record',
				'download_all' => 'diagnostico_download_all',
				'delete_record' => 'diagnostico_delete_record',
			),

			'nonce_action' => 'diagnostico_admin',
		);

		$builder = new Admin_Table_Builder($config);
		echo $builder->render();
	}

	// ========================================
	// AJAX HANDLERS - Independientes de diagnóstico
	// ========================================

	public function ajax_get_record() {
		check_ajax_referer('diagnostico_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Diagnóstico no encontrado']);
		}

		$respuestas = json_decode($record->respuestas, true) ?? [];

		$html = '<div class="avance-detail-view">';
		$html .= '<div class="avance-detail-row">';
		$html .= '<div class="avance-detail-label">ID</div>';
		$html .= '<div class="avance-detail-value">#' . esc_html($record->id) . '</div>';
		$html .= '</div>';

		$html .= '<div class="avance-detail-row">';
		$html .= '<div class="avance-detail-label">Nombre</div>';
		$html .= '<div class="avance-detail-value">' . esc_html($record->nombre_completo) . '</div>';
		$html .= '</div>';

		$html .= '<div class="avance-detail-row">';
		$html .= '<div class="avance-detail-label">Email</div>';
		$html .= '<div class="avance-detail-value"><a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email) . '</a></div>';
		$html .= '</div>';

		$html .= '<div class="avance-detail-row">';
		$html .= '<div class="avance-detail-label">WhatsApp</div>';
		$clean_whatsapp = preg_replace('/[^0-9]/', '', $record->whatsapp);
		$html .= '<div class="avance-detail-value"><a href="https://wa.me/' . esc_attr($clean_whatsapp) . '" target="_blank">' . esc_html($record->whatsapp) . '</a></div>';
		$html .= '</div>';

		$html .= '<div class="avance-detail-row">';
		$html .= '<div class="avance-detail-label">Fecha</div>';
		$html .= '<div class="avance-detail-value">' . esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion))) . '</div>';
		$html .= '</div>';

		if (!empty($respuestas)) {
			$html .= '<div class="avance-detail-row">';
			$html .= '<div class="avance-detail-label">Respuestas</div>';
			$html .= '<div class="avance-detail-value">';
			foreach ($respuestas as $idx => $resp) {
				$html .= '<div>P' . ($idx + 1) . ': ' . esc_html($resp) . '</div>';
			}
			$html .= '</div></div>';
		}

		$html .= '</div>';

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_download_record() {
		check_ajax_referer('diagnostico_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Diagnóstico no encontrado']);
		}

		$respuestas = json_decode($record->respuestas, true) ?? [];
		$respuestas_text = implode(' | ', $respuestas);

		$csv = "ID,Nombre,Email,WhatsApp,Respuestas,Fecha\n";
		$csv .= sprintf(
			'"%d","%s","%s","%s","%s","%s"' . "\n",
			$record->id,
			str_replace('"', '""', $record->nombre_completo),
			str_replace('"', '""', $record->email),
			str_replace('"', '""', $record->whatsapp),
			str_replace('"', '""', $respuestas_text),
			$record->fecha_creacion
		);

		$filename = 'diagnostico-' . $id . '-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_download_all() {
		check_ajax_referer('diagnostico_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY fecha_creacion DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay diagnósticos para descargar']);
		}

		$csv = "ID,Nombre,Email,WhatsApp,Respuestas,Fecha\n";
		foreach ($records as $record) {
			$respuestas = json_decode($record->respuestas, true) ?? [];
			$respuestas_text = implode(' | ', $respuestas);
			$csv .= sprintf(
				'"%d","%s","%s","%s","%s","%s"' . "\n",
				$record->id,
				str_replace('"', '""', $record->nombre_completo),
				str_replace('"', '""', $record->email),
				str_replace('"', '""', $record->whatsapp),
				str_replace('"', '""', $respuestas_text),
				$record->fecha_creacion
			);
		}

		$filename = 'diagnosticos-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('diagnostico_admin', 'nonce');

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

		wp_send_json_success(['message' => 'Diagnóstico eliminado correctamente']);
	}
}

new Avance_Diagnostico_Admin();
