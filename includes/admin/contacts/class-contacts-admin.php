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

		add_action('admin_menu', [$this, 'register_menu']);
		add_action('wp_ajax_contacts_get_record', [$this, 'ajax_get_record']);
		add_action('wp_ajax_contacts_delete_record', [$this, 'ajax_delete_record']);
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

		$records = $wpdb->get_results("SELECT * FROM {$this->wpdb_table} ORDER BY created_at DESC LIMIT 50");
		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb_table}"));
		$nonce = wp_create_nonce('contacts_admin');

		$columns = [
			['field' => 'id', 'label' => 'ID'],
			['field' => 'nombre', 'label' => 'Nombre'],
			['field' => 'email', 'label' => 'Email'],
			['field' => 'numero', 'label' => 'WhatsApp'],
			['field' => 'asunto', 'label' => 'Asunto'],
			['field' => 'created_at', 'label' => 'Fecha'],
		];

		parent::__construct(
			'Formularios Recibidos',
			'Gestiona los contactos enviados desde el formulario',
			$records,
			$total,
			$columns,
			$nonce,
			$this->wpdb_table
		);

		$this->render();
	}

	protected function render_rows() {
		$records = $this->get_records();
		$columns = $this->get_columns();

		if (empty($records)) {
			?>
			<tr>
				<td colspan="<?php echo count($columns) + 1; ?>">
					<div class="admin-empty-state">
						<div class="admin-empty-title">Sin contactos</div>
						<div class="admin-empty-message">No hay formularios enviados</div>
					</div>
				</td>
			</tr>
			<?php
			return;
		}

		foreach ($records as $record) {
			?>
			<tr class="admin-row" data-id="<?php echo esc_attr($record->id); ?>">
				<td class="admin-col-id"><?php echo esc_html($record->id); ?></td>
				<td class="admin-col-nombre">
					<strong><?php echo esc_html($record->nombre); ?></strong>
				</td>
				<td class="admin-col-email">
					<a href="mailto:<?php echo esc_attr($record->email); ?>">
						<?php echo esc_html($record->email); ?>
					</a>
				</td>
				<td class="admin-col-numero">
					<a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $record->numero)); ?>" target="_blank">
						<?php echo esc_html($record->numero); ?>
					</a>
				</td>
				<td class="admin-col-asunto"><?php echo esc_html($record->asunto); ?></td>
				<td class="admin-col-created">
					<?php echo esc_html(wp_date('d/m/Y H:i', strtotime($record->created_at))); ?>
				</td>
				<td class="admin-col-actions">
					<button class="admin-btn admin-btn-view" data-id="<?php echo esc_attr($record->id); ?>" title="Ver detalles">Ver</button>
					<button class="admin-btn admin-btn-delete" data-id="<?php echo esc_attr($record->id); ?>" title="Eliminar">Eliminar</button>
				</td>
			</tr>
			<?php
		}
	}

	public function ajax_get_record() {
		check_ajax_referer('contacts_admin', 'nonce');

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
			wp_send_json_error(['message' => 'Contacto no encontrado']);
		}

		$html = '<div class="modal-content">';
		$html .= '<div class="modal-field"><strong>ID:</strong> ' . esc_html($record->id) . '</div>';
		$html .= '<div class="modal-field"><strong>Nombre:</strong> ' . esc_html($record->nombre) . '</div>';
		$html .= '<div class="modal-field"><strong>Email:</strong> <a href="mailto:' . esc_attr($record->email) . '">' . esc_html($record->email) . '</a></div>';
		$html .= '<div class="modal-field"><strong>WhatsApp:</strong> <a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $record->numero)) . '" target="_blank">' . esc_html($record->numero) . '</a></div>';
		$html .= '<div class="modal-field"><strong>Asunto:</strong> ' . esc_html($record->asunto) . '</div>';
		$html .= '<div class="modal-field"><strong>Mensaje:</strong> ' . nl2br(esc_html($record->mensaje)) . '</div>';
		$html .= '<div class="modal-field"><strong>Fecha:</strong> ' . esc_html(wp_date('d/m/Y H:i', strtotime($record->created_at))) . '</div>';
		$html .= '</div>';

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
}

new Avance_Contacts_Admin();
