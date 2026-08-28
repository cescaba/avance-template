<?php
/**
 * Admin Page - Agendamientos Contacto
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Admin_Agendamiento_Contacto {

	public function __construct() {
		add_action('admin_menu', [$this, 'add_menu']);
	}

	public function add_menu() {
		add_menu_page(
			'Agendamientos',
			'Agendamientos',
			'manage_options',
			'avance-agendamientos',
			[$this, 'render_page'],
			'dashicons-calendar-alt',
			25
		);
	}

	public function render_page() {
		if (!current_user_can('manage_options')) {
			wp_die('No tienes permisos para ver esta página.');
		}

		require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';

		$action = $_GET['action'] ?? 'list';
		$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
		$limit = 50;
		$offset = ($page - 1) * $limit;

		?>
		<div class="wrap">
			<h1>Agendamientos de Contacto</h1>

			<?php if (isset($_GET['actualizado'])): ?>
				<div class="notice notice-success is-dismissible">
					<p>Estado actualizado correctamente.</p>
				</div>
			<?php endif; ?>

			<?php if ($action === 'list'): ?>
				<?php $this->render_list($offset, $limit, $page); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_list($offset, $limit, $page) {
		require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';

		$agendamientos = Avance_Agendamiento_Contacto_DB::get_all($limit, $offset);
		$total = $this->get_total_count();
		$total_pages = ceil($total / $limit);

		?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th style="width: 50px;">ID</th>
					<th>Nombre</th>
					<th>WhatsApp</th>
					<th>Tema</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Estado</th>
					<th>WSP Enviado</th>
					<th>Fecha de Registro</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($agendamientos)): ?>
					<?php foreach ($agendamientos as $item): ?>
						<tr>
							<td><?php echo esc_html($item->id); ?></td>
							<td><?php echo esc_html($item->nombre); ?></td>
							<td>
								<a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', esc_attr($item->whatsapp)); ?>" target="_blank">
									<?php echo esc_html($item->whatsapp); ?>
								</a>
							</td>
							<td><?php echo esc_html($item->tema); ?></td>
							<td><?php echo esc_html($item->fecha); ?></td>
							<td><?php echo esc_html($item->hora); ?></td>
							<td>
								<span class="status status-<?php echo esc_attr($item->estado); ?>">
									<?php echo esc_html(ucfirst($item->estado)); ?>
								</span>
							</td>
							<td>
								<?php echo $item->mensaje_wsp_enviado ? '✓' : '✗'; ?>
							</td>
							<td><?php echo esc_html(date('d/m/Y H:i', strtotime($item->fecha_creacion))); ?></td>
							<td>
								<?php $this->render_actions($item); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="10" style="text-align: center; padding: 20px;">
							No hay agendamientos registrados.
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<!-- Paginación -->
		<?php if ($total_pages > 1): ?>
			<div class="tablenav">
				<div class="tablenav-pages">
					<span class="displaying-num"><?php echo esc_html($total); ?> agendamientos</span>
					<span class="pagination-links">
						<?php if ($page > 1): ?>
							<a class="first-page button" href="?page=avance-agendamientos&paged=1">«</a>
							<a class="prev-page button" href="?page=avance-agendamientos&paged=<?php echo $page - 1; ?>">‹</a>
						<?php endif; ?>

						<span class="paging-input">
							Página <?php echo $page; ?> de <?php echo $total_pages; ?>
						</span>

						<?php if ($page < $total_pages): ?>
							<a class="next-page button" href="?page=avance-agendamientos&paged=<?php echo $page + 1; ?>">›</a>
							<a class="last-page button" href="?page=avance-agendamientos&paged=<?php echo $total_pages; ?>">»</a>
						<?php endif; ?>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<style>
			.status {
				display: inline-block;
				padding: 4px 8px;
				border-radius: 4px;
				font-size: 12px;
				font-weight: 600;
			}
			.status-pendiente {
				background-color: #fff3cd;
				color: #856404;
			}
			.status-confirmado {
				background-color: #d4edda;
				color: #155724;
			}
			.status-completado {
				background-color: #d1ecf1;
				color: #0c5460;
			}
			.status-cancelado {
				background-color: #f8d7da;
				color: #721c24;
			}
		</style>
		<?php
	}

	private function render_actions($item) {
		?>
		<div style="display: flex; gap: 10px;">
			<a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', esc_attr($item->whatsapp)); ?>" class="button button-small" target="_blank">
				WhatsApp
			</a>
		</div>
		<?php
	}

	private function get_total_count() {
		global $wpdb;
		require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';
		$table = Avance_Agendamiento_Contacto_DB::table_name();

		return intval($wpdb->get_var("SELECT COUNT(*) FROM $table"));
	}
}

new Avance_Admin_Agendamiento_Contacto();
