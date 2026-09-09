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

require_once(get_template_directory() . '/includes/core/diagnostico-config.php');

class Avance_Diagnosticos_Admin extends Avance_Admin_Template {

	private $wpdb_table;

	public function __construct() {
		global $wpdb;
		$this->wpdb_table = $wpdb->prefix . 'avance_diagnosticos';

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre_completo', 'label' => 'Nombre'],
			['field' => 'email', 'label' => 'Email'],
			['field' => 'whatsapp', 'label' => 'WhatsApp'],
			['field' => 'respuestas', 'label' => 'Respuestas del Diagnóstico'],
			['field' => 'fecha_creacion', 'label' => 'Fecha'],
		];

		$nonce = wp_create_nonce('diagnosticos_admin');

		parent::__construct(
			'Diagnósticos',
			'Gestiona todos los diagnósticos completados',
			[],
			0,
			$columns,
			$nonce,
			$this->wpdb_table,
			'diagnosticos'
		);

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

		$this->records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY fecha_creacion DESC LIMIT 50");
		$this->total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb_table}"));

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
			case 'respuestas':
				return $this->get_respuestas_score($record->respuestas ?? '');
			default:
				return esc_html($record->$field ?? '—');
		}
	}

	private function get_respuestas_score($respuestas_json) {
		if (empty($respuestas_json)) {
			return '—';
		}

		$respuestas = json_decode($respuestas_json, true);
		if (!is_array($respuestas)) {
			return '—';
		}

		$total = count($respuestas);
		return '<span class="diagnostico-score">' . $total . '/' . $total . '</span>';
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

		$html = $this->generate_diagnostico_modal($record);
		$preguntas = avance_get_diagnostico_questions();

		wp_send_json_success(['html' => $html, 'preguntas' => $preguntas]);
	}

	private function generate_diagnostico_modal($record) {
		$html = '<div class="modal-header">';
		$html .= '<h2 class="modal-title">Diagnóstico Completo</h2>';
		$html .= '</div>';

		$html .= '<div class="modal-content">';

		$html .= '<div class="modal-section">';
		$html .= '<h3 class="modal-section-title">Información del Contacto</h3>';
		$html .= '<div class="modal-row">';
		$html .= '<div class="modal-field"><div class="modal-label">Nombre</div><div class="modal-value">' . esc_html($record->nombre_completo ?? '—') . '</div></div>';
		$html .= '<div class="modal-field"><div class="modal-label">Email</div><div class="modal-value"><a href="mailto:' . esc_attr($record->email) . '" class="modal-link">' . esc_html($record->email) . '</a></div></div>';
		$html .= '</div>';
		$html .= '<div class="modal-row">';
		$html .= '<div class="modal-field"><div class="modal-label">WhatsApp</div><div class="modal-value"><a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->whatsapp)) . '" target="_blank" class="modal-link">' . esc_html($record->whatsapp) . '</a></div></div>';
		$html .= '<div class="modal-field"><div class="modal-label">Fecha</div><div class="modal-value modal-date">' . esc_html(wp_date('d/m/Y H:i', strtotime($record->fecha_creacion))) . '</div></div>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="modal-section">';
		$html .= '<h3 class="modal-section-title">Respuestas del Diagnóstico</h3>';
		$preguntas = avance_get_diagnostico_questions();
		$html .= $this->format_respuestas_detailed($record->respuestas ?? '', $preguntas);
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	private function format_respuestas_detailed($respuestas_json, $preguntas = []) {
		if (empty($respuestas_json)) {
			return '<p>Sin respuestas registradas</p>';
		}

		$respuestas = json_decode($respuestas_json, true);
		if (!is_array($respuestas)) {
			return '<p>Error al procesar respuestas</p>';
		}

		$html = '<div class="diagnostico-respuestas">';
		foreach ($respuestas as $index => $item) {
			$pregunta = '';
			$respuesta = '';

			if (is_array($item)) {
				$pregunta = $item['pregunta'] ?? '';
				$respuesta = $item['respuesta'] ?? '';
			} else {
				$respuesta = $item;
				if (isset($preguntas[$index])) {
					$pregunta = $preguntas[$index];
				}
			}

			$html .= '<div class="diagnostico-respuesta-item">';
			if ($pregunta) {
				$html .= '<strong class="diagnostico-pregunta">' . esc_html($pregunta) . '</strong>';
			}
			$html .= '<span class="diagnostico-respuesta">' . esc_html($respuesta) . '</span>';
			$html .= '</div>';
		}
		$html .= '</div>';

		return $html;
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
