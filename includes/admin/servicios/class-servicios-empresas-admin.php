<?php
/**
 * Servicios Empresas Admin - Gestor independiente de propuestas
 * Cada admin es responsable de su propia tabla y lógica
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Servicios_Empresas_Admin {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'avance_servicios_empresas';

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// AJAX handlers específicos de servicios
		add_action('wp_ajax_servicios_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_servicios_download_record', [$this, 'ajax_download_record']);
		add_action('wp_ajax_servicios_download_all', [$this, 'ajax_download_all']);
		add_action('wp_ajax_servicios_delete_record', [$this, 'ajax_delete_record']);
	}

	public function register_menu() {
		add_menu_page(
			'Servicios Empresas',
			'Servicios',
			'manage_options',
			'servicios-admin',
			[$this, 'render_admin_page'],
			'dashicons-portfolio',
			55
		);
	}

	public function enqueue_admin_assets($hook) {
		// CSS encolado desde functions.php
	}

	public function render_admin_page() {
		global $wpdb;

		// Traer datos de la tabla
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY fecha_creacion DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"));

		require_once get_template_directory() . '/includes/admin/class-admin-table-builder.php';

		$config = array(
			'title' => 'Servicios Empresas',
			'subtitle' => 'Gestiona todas las propuestas y solicitudes',

			'columns' => array(
				array('label' => 'ID', 'field' => 'id'),
				array('label' => 'Nombre', 'field' => 'nombre'),
				array('label' => 'Empresa', 'field' => 'empresa'),
				array('label' => 'Email', 'field' => 'email', 'type' => 'email'),
				array('label' => 'WhatsApp', 'field' => 'whatsapp', 'type' => 'whatsapp'),
				array('label' => 'Servicio', 'field' => 'servicio_interes'),
				array('label' => 'Fecha', 'field' => 'fecha_creacion', 'type' => 'date'),
			),

			'filters' => array('search' => true),

			'stats' => array(
				array(
					'label' => 'Total Solicitudes',
					'value' => $total,
					'icon' => '📊',
					'class' => 'total'
				),
			),

			'data' => $records,
			'total' => $total,

			// Acciones AJAX específicas de este admin
			'ajax_actions' => array(
				'get_record' => 'servicios_get_record',
				'download_record' => 'servicios_download_record',
				'download_all' => 'servicios_download_all',
				'delete_record' => 'servicios_delete_record',
			),

			'nonce_action' => 'servicios_admin',
		);

		$builder = new Admin_Table_Builder($config);
		echo '<div class="avance-admin-container">';
		echo $builder->render();
		echo '</div>';
	}

	// ========================================
	// AJAX HANDLERS - Independientes de servicios
	// ========================================

	public function ajax_get_record() {
		check_ajax_referer('servicios_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Propuesta no encontrada']);
		}

		$html = '<div class="avance-detail-view">';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">ID</div><div class="avance-detail-value">#' . esc_html($record->id) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Nombre</div><div class="avance-detail-value">' . esc_html($record->nombre) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Cargo</div><div class="avance-detail-value">' . esc_html($record->cargo ?: '—') . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Empresa</div><div class="avance-detail-value">' . esc_html($record->empresa) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Email</div><div class="avance-detail-value"><a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email) . '</a></div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">WhatsApp</div><div class="avance-detail-value">' . esc_html($record->whatsapp) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Servicio</div><div class="avance-detail-value">' . esc_html($record->servicio_interes) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Desafío</div><div class="avance-detail-value">' . nl2br(esc_html($record->desafio_comercial)) . '</div></div>';
		$html .= '<div class="avance-detail-row"><div class="avance-detail-label">Fecha</div><div class="avance-detail-value">' . esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion))) . '</div></div>';
		$html .= '</div>';

		wp_send_json_success(['html' => $html]);
	}

	public function ajax_download_record() {
		check_ajax_referer('servicios_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Propuesta no encontrada']);
		}

		$csv = "ID,Nombre,Cargo,Empresa,Email,WhatsApp,Servicio,Desafío,Fecha\n";
		$csv .= sprintf(
			'"%d","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
			$record->id,
			str_replace('"', '""', $record->nombre),
			str_replace('"', '""', $record->cargo),
			str_replace('"', '""', $record->empresa),
			str_replace('"', '""', $record->email),
			str_replace('"', '""', $record->whatsapp),
			str_replace('"', '""', $record->servicio_interes),
			str_replace('"', '""', $record->desafio_comercial),
			$record->fecha_creacion
		);

		$filename = 'propuesta-' . $id . '-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_download_all() {
		check_ajax_referer('servicios_admin', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Sin permiso']);
		}

		global $wpdb;
		$records = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY fecha_creacion DESC");

		if (empty($records)) {
			wp_send_json_error(['message' => 'No hay propuestas para descargar']);
		}

		$csv = "ID,Nombre,Cargo,Empresa,Email,WhatsApp,Servicio,Desafío,Fecha\n";
		foreach ($records as $record) {
			$csv .= sprintf(
				'"%d","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
				$record->id,
				str_replace('"', '""', $record->nombre),
				str_replace('"', '""', $record->cargo),
				str_replace('"', '""', $record->empresa),
				str_replace('"', '""', $record->email),
				str_replace('"', '""', $record->whatsapp),
				str_replace('"', '""', $record->servicio_interes),
				str_replace('"', '""', $record->desafio_comercial),
				$record->fecha_creacion
			);
		}

		$filename = 'propuestas-' . gmdate('Y-m-d-His') . '.csv';
		wp_send_json_success(['csv' => $csv, 'filename' => $filename]);
	}

	public function ajax_delete_record() {
		check_ajax_referer('servicios_admin', 'nonce');

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

		wp_send_json_success(['message' => 'Propuesta eliminada correctamente']);
	}
}

new Avance_Servicios_Empresas_Admin();
