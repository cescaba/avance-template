<?php
/**
 * Diagnostico Admin Panel - Premium Design
 *
 * Panel de administración elegante para ver todos los diagnósticos completados
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnostico_Admin {

	public function __construct() {
		add_action('admin_menu', array($this, 'register_admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
	}

	/**
	 * Registrar página en el admin
	 */
	public function register_admin_menu() {
		add_menu_page(
			'Diagnósticos Completados',
			'Diagnósticos',
			'manage_options',
			'avance-diagnosticos',
			array($this, 'render_admin_page'),
			'dashicons-chart-bar',
			31
		);
	}

	/**
	 * Enqueuar estilos del admin
	 */
	public function enqueue_admin_styles($hook) {
		if ($hook !== 'toplevel_page_avance-diagnosticos') {
			return;
		}

		wp_enqueue_style('wp-admin');
		wp_enqueue_style('avance-admin-premium', get_template_directory_uri() . '/assets/css/admin-premium.css', array(), wp_get_theme()->get('Version'));
	}

	/**
	 * Renderizar página del admin
	 */
	public function render_admin_page() {
		// Verificar permisos
		if (!current_user_can('manage_options')) {
			wp_die('No tienes permiso para acceder a esta página.');
		}

		// Obtener filtros
		$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

		// Obtener datos
		$diagnosticos = $this->get_diagnosticos($search, $paged);
		$total = $this->get_diagnosticos_count($search);
		$stats = $this->get_stats();

		?>
		<div class="avance-admin-container">
			<!-- HEADER -->
			<div class="avance-admin-header">
				<div class="avance-admin-title-section">
					<h1 class="avance-admin-title">📊 Panel de Diagnósticos</h1>
					<p class="avance-admin-subtitle">Gestiona todos los diagnósticos completados</p>
				</div>
			</div>

			<!-- ESTADÍSTICAS -->
			<div class="avance-stats-grid">
				<div class="avance-stat-card avance-stat-total">
					<div class="avance-stat-icon">📈</div>
					<div class="avance-stat-content">
						<div class="avance-stat-number"><?php echo esc_html($stats['total']); ?></div>
						<div class="avance-stat-label">Total de Diagnósticos</div>
					</div>
				</div>

				<div class="avance-stat-card avance-stat-success">
					<div class="avance-stat-icon">✓</div>
					<div class="avance-stat-content">
						<div class="avance-stat-number"><?php echo esc_html($stats['esta_semana']); ?></div>
						<div class="avance-stat-label">Esta Semana</div>
					</div>
				</div>

				<div class="avance-stat-card avance-stat-warning">
					<div class="avance-stat-icon">🆕</div>
					<div class="avance-stat-content">
						<div class="avance-stat-number"><?php echo esc_html($stats['hoy']); ?></div>
						<div class="avance-stat-label">Hoy</div>
					</div>
				</div>
			</div>

			<!-- FILTROS -->
			<div class="avance-filter-section">
				<form method="get" class="avance-filter-form">
					<input type="hidden" name="page" value="avance-diagnosticos">

					<div class="avance-filter-group">
						<input
							type="search"
							name="s"
							placeholder="Buscar por nombre, email o WhatsApp..."
							value="<?php echo esc_attr($search); ?>"
							class="avance-search-input"
						>
					</div>

					<div class="avance-filter-group">
						<button type="submit" class="avance-btn-primary">🔍 Buscar</button>
					</div>
				</form>
			</div>

			<!-- TABLA DE DIAGNÓSTICOS -->
			<div class="avance-table-wrapper">
				<table class="avance-contacts-table">
					<thead>
						<tr>
							<th class="col-id">ID</th>
							<th class="col-nombre">Nombre</th>
							<th class="col-email">Email</th>
							<th class="col-whatsapp">WhatsApp</th>
							<th class="col-fecha">Fecha</th>
							<th class="col-accion">Acción</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($diagnosticos)) : ?>
							<tr class="avance-empty-row">
								<td colspan="6">
									<div class="avance-empty-state">
										<div style="font-size: 48px; margin-bottom: 10px;">📭</div>
										<p>Sin diagnósticos</p>
									</div>
								</td>
							</tr>
						<?php else : ?>
							<?php foreach ($diagnosticos as $diag) : ?>
								<tr class="avance-table-row">
									<td class="col-id"><span class="avance-id-badge">#<?php echo esc_html($diag->id); ?></span></td>
									<td class="col-nombre">
										<strong><?php echo esc_html($diag->nombre_completo); ?></strong>
									</td>
									<td class="col-email">
										<a href="mailto:<?php echo esc_attr($diag->email); ?>" class="avance-link">
											<?php echo esc_html($diag->email); ?>
										</a>
									</td>
									<td class="col-whatsapp">
										<a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $diag->whatsapp)); ?>" target="_blank" rel="noopener" class="avance-link avance-whatsapp-link">
											<?php echo esc_html($diag->whatsapp); ?>
										</a>
									</td>
									<td class="col-fecha">
										<small><?php echo esc_html(wp_date('d/m/Y', strtotime($diag->fecha_creacion))); ?></small>
										<br>
										<small style="color: #999;"><?php echo esc_html(wp_date('H:i', strtotime($diag->fecha_creacion))); ?></small>
									</td>
									<td class="col-accion">
										<button class="avance-btn-view" onclick="avanceShowDiagnosticoDetail(<?php echo esc_attr($diag->id); ?>); return false;">
											Ver
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<!-- PAGINACIÓN -->
			<?php if ($total > 10) : ?>
				<div class="avance-pagination">
					<?php
					$page_links = paginate_links(array(
						'base' => add_query_arg('paged', '%#%'),
						'format' => '',
						'prev_text' => '← Anterior',
						'next_text' => 'Siguiente →',
						'total' => ceil($total / 10),
						'current' => $paged,
						'echo' => false,
					));
					echo wp_kses_post($page_links);
					?>
				</div>
			<?php endif; ?>
		</div>

		<!-- MODAL PARA VER DETALLES -->
		<div id="avance-diagnostico-modal" class="avance-modal">
			<div class="avance-modal-content">
				<button class="avance-modal-close" onclick="document.getElementById('avance-diagnostico-modal').style.display='none'">×</button>
				<div id="avance-diagnostico-detail">
					<p style="text-align: center;">Cargando...</p>
				</div>
			</div>
		</div>

		<script>
			function avanceShowDiagnosticoDetail(id) {
				const modal = document.getElementById('avance-diagnostico-modal');
				const content = document.getElementById('avance-diagnostico-detail');

				modal.style.display = 'flex';

				fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: 'action=avance_get_diagnostico&id=' + id + '&nonce=<?php echo esc_attr(wp_create_nonce('avance_admin')); ?>',
				})
					.then(r => r.json())
					.then(r => {
						if (r.success) {
							content.innerHTML = r.data.html;
						} else {
							content.innerHTML = '<p style="color: red;">Error al cargar los detalles</p>';
						}
					})
					.catch(e => {
						content.innerHTML = '<p style="color: red;">Error: ' + e.message + '</p>';
					});
			}

			// Cerrar modal al hacer click fuera
			document.getElementById('avance-diagnostico-modal').addEventListener('click', function(e) {
				if (e.target === this) {
					this.style.display = 'none';
				}
			});
		</script>
		<?php
	}

	/**
	 * Obtener diagnósticos con filtros
	 */
	private function get_diagnosticos($search = '', $paged = 1) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'avance_diagnosticos';
		$limit = 10;
		$offset = ($paged - 1) * $limit;

		$where = array('1=1');

		if (!empty($search)) {
			$search_term = '%' . $wpdb->esc_like($search) . '%';
			$where[] = $wpdb->prepare('(nombre_completo LIKE %s OR email LIKE %s OR whatsapp LIKE %s)', $search_term, $search_term, $search_term);
		}

		$query = "SELECT * FROM {$table_name} WHERE " . implode(' AND ', $where) . " ORDER BY fecha_creacion DESC LIMIT {$offset}, {$limit}";

		return $wpdb->get_results($query);
	}

	/**
	 * Contar diagnósticos con filtros
	 */
	private function get_diagnosticos_count($search = '') {
		global $wpdb;

		$table_name = $wpdb->prefix . 'avance_diagnosticos';
		$where = array('1=1');

		if (!empty($search)) {
			$search_term = '%' . $wpdb->esc_like($search) . '%';
			$where[] = $wpdb->prepare('(nombre_completo LIKE %s OR email LIKE %s OR whatsapp LIKE %s)', $search_term, $search_term, $search_term);
		}

		$query = "SELECT COUNT(*) FROM {$table_name} WHERE " . implode(' AND ', $where);

		return intval($wpdb->get_var($query));
	}

	/**
	 * Obtener estadísticas
	 */
	private function get_stats() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'avance_diagnosticos';

		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name}"));
		$hoy = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE DATE(fecha_creacion) = CURDATE()"));
		$esta_semana = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 7 DAY)"));

		return array(
			'total' => $total,
			'hoy' => $hoy,
			'esta_semana' => $esta_semana,
		);
	}
}

// Inicializar admin
new Avance_Diagnostico_Admin();

/**
 * AJAX handler para obtener detalles del diagnóstico
 */
add_action('wp_ajax_avance_get_diagnostico', function() {
	if (!current_user_can('manage_options')) {
		wp_send_json_error('No tienes permiso');
	}

	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_admin')) {
		wp_send_json_error('Nonce inválido');
	}

	global $wpdb;

	$diag_id = intval($_POST['id'] ?? 0);
	$diagnostico = $wpdb->get_row($wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}avance_diagnosticos WHERE id = %d",
		$diag_id
	));

	if (!$diagnostico) {
		wp_send_json_error('Diagnóstico no encontrado');
	}

	$respuestas = is_string($diagnostico->respuestas) ? json_decode($diagnostico->respuestas, true) : array();

	ob_start();
	?>
	<div class="avance-modal-header">
		<h2>Diagnóstico #<?php echo esc_html($diagnostico->id); ?></h2>
	</div>

	<div class="avance-modal-body">
		<div class="avance-detail-grid">
			<div class="avance-detail-field">
				<label>Nombre Completo</label>
				<p class="avance-detail-value"><?php echo esc_html($diagnostico->nombre_completo); ?></p>
			</div>

			<div class="avance-detail-field">
				<label>Email</label>
				<p class="avance-detail-value">
					<a href="mailto:<?php echo esc_attr($diagnostico->email); ?>" class="avance-link">
						<?php echo esc_html($diagnostico->email); ?>
					</a>
				</p>
			</div>

			<div class="avance-detail-field">
				<label>WhatsApp</label>
				<p class="avance-detail-value">
					<a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $diagnostico->whatsapp)); ?>" target="_blank" rel="noopener" class="avance-link">
						<?php echo esc_html($diagnostico->whatsapp); ?>
					</a>
				</p>
			</div>

			<div class="avance-detail-field full-width">
				<label>Respuestas del Diagnóstico</label>
				<div class="avance-message-box">
					<?php if (!empty($respuestas) && is_array($respuestas)) : ?>
						<ol style="margin: 10px 0; padding-left: 20px;">
							<?php foreach ($respuestas as $index => $respuesta) : ?>
								<li style="margin: 8px 0;">
									<strong><?php echo esc_html($respuesta); ?></strong>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php else : ?>
						<p>No hay respuestas disponibles</p>
					<?php endif; ?>
				</div>
			</div>

			<div class="avance-detail-field">
				<label>Fecha y Hora</label>
				<p class="avance-detail-value"><?php echo esc_html(wp_date('d/m/Y H:i:s', strtotime($diagnostico->fecha_creacion))); ?></p>
			</div>
		</div>
	</div>
	<?php

	$html = ob_get_clean();

	wp_send_json_success(array('html' => $html));
});
