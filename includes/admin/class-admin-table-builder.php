<?php
/**
 * Admin Table Builder - Constructor genérico de tablas de admin
 * Proporciona estructura HTML/CSS unificada para todos los admin
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Admin_Table_Builder {

	private $config;

	public function __construct($config) {
		$this->config = array_merge($this->get_defaults(), $config);
	}

	private function get_defaults() {
		return array(
			'title' => 'Admin Panel',
			'subtitle' => 'Gestiona los registros',
			'icon' => '📋',
			'columns' => array(),
			'filters' => array(),
			'stats' => array(),
			'data' => array(),
			'total' => 0,
			'search_placeholder' => 'Buscar...',
			'ajax_actions' => array(
				'get_record' => 'avance_get_record',
				'download_record' => 'avance_download_record',
				'download_all' => 'avance_download_all',
				'delete_record' => 'avance_delete_record',
			),
			'nonce_action' => 'avance_admin',
			'modal_title' => 'Detalles',
		);
	}

	public function render() {
		global $wpdb;

		$this->config['total'] = isset($this->config['total']) ? $this->config['total'] : count($this->config['data']);
		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
		$limit = 50;

		// Localizar script con datos correctos (URL relativa para evitar CORS en Local by Flywheel)
		wp_localize_script('jquery', 'avanceAdminConfig_' . uniqid(), array(
			'ajaxUrl' => '/wp-admin/admin-ajax.php',
			'nonce' => wp_create_nonce($this->config['nonce_action'] ?? 'avance_admin'),
			'actions' => $this->config['ajax_actions'] ?? array(),
		));

		ob_start();
		?>
		<div class="avance-admin-container">
			<!-- HEADER -->
			<div class="avance-admin-header">
				<div class="avance-admin-title-section">
					<h1 class="avance-admin-title"><?php echo esc_html($this->config['title']); ?></h1>
					<p class="avance-admin-subtitle"><?php echo esc_html($this->config['subtitle']); ?></p>
				</div>
			</div>

			<!-- ESTADÍSTICAS -->
			<?php if (!empty($this->config['stats'])): ?>
				<div class="avance-stats-grid">
					<?php foreach ($this->config['stats'] as $stat): ?>
						<div class="avance-stat-card avance-stat-<?php echo esc_attr($stat['class'] ?? 'total'); ?>">
							<div class="avance-stat-content">
								<div class="avance-stat-number"><?php echo esc_html($stat['value']); ?></div>
								<div class="avance-stat-label"><?php echo esc_html($stat['label']); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<!-- FILTROS -->
			<?php if (!empty($this->config['filters'])): ?>
				<div class="avance-filter-section">
					<form method="get" class="avance-filter-form">
						<input type="hidden" name="page" value="<?php echo esc_attr(isset($_GET['page']) ? $_GET['page'] : ''); ?>">

						<?php if (isset($this->config['filters']['search'])): ?>
							<div class="avance-filter-group">
								<input type="search" name="s" placeholder="<?php echo esc_attr($this->config['search_placeholder']); ?>" value="<?php echo esc_attr(isset($_GET['s']) ? $_GET['s'] : ''); ?>" class="avance-search-input">
							</div>
						<?php endif; ?>

						<?php if (isset($this->config['filters']['custom'])): ?>
							<?php foreach ($this->config['filters']['custom'] as $filter): ?>
								<div class="avance-filter-group">
									<select name="<?php echo esc_attr($filter['name']); ?>" class="avance-select-input">
										<option value=""><?php echo esc_html($filter['label']); ?></option>
										<?php foreach ($filter['options'] as $value => $label): ?>
											<option value="<?php echo esc_attr($value); ?>" <?php selected(isset($_GET[$filter['name']]) ? $_GET[$filter['name']] : '', $value); ?>>
												<?php echo esc_html($label); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>

						<div class="avance-filter-group">
							<button type="submit" class="avance-btn-primary">Filtrar</button>
						</div>

						<div class="avance-filter-group">
							<button type="button" class="avance-btn-primary" onclick="adminTableBuilder.downloadAll(); return false;">Descargar Todo</button>
						</div>
					</form>
				</div>
			<?php endif; ?>

			<!-- TABLA -->
			<div class="avance-table-wrapper">
				<table class="avance-contacts-table">
					<thead>
						<tr>
							<?php foreach ($this->config['columns'] as $column): ?>
								<th class="col-<?php echo esc_attr($column['field']); ?>"><?php echo esc_html($column['label']); ?></th>
							<?php endforeach; ?>
							<th class="col-accion">Acción</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($this->config['data'])): ?>
							<tr class="avance-empty-row">
								<td colspan="<?php echo esc_attr(count($this->config['columns']) + 1); ?>">
									<div class="avance-empty-state">
										<div style="font-size: 48px; margin-bottom: 10px;">📭</div>
										<p>Sin registros</p>
									</div>
								</td>
							</tr>
						<?php else: ?>
							<?php foreach ($this->config['data'] as $row): ?>
								<tr class="avance-table-row">
									<?php foreach ($this->config['columns'] as $column): ?>
										<td class="col-<?php echo esc_attr($column['field']); ?>">
											<?php echo $this->render_cell($row, $column); ?>
										</td>
									<?php endforeach; ?>
									<td class="col-accion">
										<button class="avance-btn-view" onclick="adminTableBuilder.view(<?php echo esc_attr($row->id); ?>); return false;">Ver</button>
										<button class="avance-btn-view" onclick="adminTableBuilder.download(<?php echo esc_attr($row->id); ?>); return false;">Descargar</button>
										<button class="avance-btn-view avance-btn-danger" onclick="adminTableBuilder.delete(<?php echo esc_attr($row->id); ?>); return false;">Eliminar</button>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<script>
			const adminTableBuilder = {
				getConfig: function() {
					// Buscar en objetos localizados (wp_localize_script)
					for (let key in window) {
						if (key.startsWith('avanceAdminConfig_') && window[key].ajaxUrl) {
							return window[key];
						}
					}
					// Fallback a valores inline (para compatibilidad)
					return {
						ajaxUrl: '/wp-admin/admin-ajax.php',
						nonce: '<?php echo esc_attr(wp_create_nonce($this->config['nonce_action'])); ?>',
						actions: <?php echo wp_json_encode($this->config['ajax_actions'] ?? []); ?>
					};
				},

				ensureModalExists: function() {
					if (!document.getElementById('avance-admin-modal')) {
						const modal = document.createElement('div');
						modal.id = 'avance-admin-modal';
						modal.className = 'avance-modal';
						modal.innerHTML = '<div class="avance-modal-content"><div class="avance-modal-header"><h2>Detalles del Registro</h2><button class="avance-modal-close" onclick="document.getElementById(\'avance-admin-modal\').style.display=\'none\'">×</button></div><div id="avance-admin-detail"><p style="text-align: center; padding: 40px; color: #999;">Cargando...</p></div></div>';
						document.body.appendChild(modal);

						// Cerrar al hacer click fuera
						modal.addEventListener('click', function(e) {
							if (e.target === this) this.style.display = 'none';
						});
					}
				},

				view: function(id) {
					const cfg = this.getConfig();
					this.ensureModalExists();
					const modal = document.getElementById('avance-admin-modal');
					const content = document.getElementById('avance-admin-detail');
					modal.style.display = 'flex';

					const params = new URLSearchParams();
					params.append('action', cfg.actions.get_record);
					params.append('id', id);
					params.append('nonce', cfg.nonce);

					fetch(cfg.ajaxUrl, {
						method: 'POST',
						headers: {'Content-Type': 'application/x-www-form-urlencoded'},
						body: params.toString(),
					})
						.then(r => r.json())
						.then(r => {
							content.innerHTML = r.success ? r.data.html : '<p style="color: red;">Error al cargar</p>';
						})
						.catch(() => { content.innerHTML = '<p style="color: red;">Error</p>'; });
				},

				download: function(id) {
					const cfg = this.getConfig();
					const params = new URLSearchParams();
					params.append('action', cfg.actions.download_record);
					params.append('id', id);
					params.append('nonce', cfg.nonce);

					fetch(cfg.ajaxUrl, {
						method: 'POST',
						headers: {'Content-Type': 'application/x-www-form-urlencoded'},
						body: params.toString(),
					})
						.then(r => r.json())
						.then(r => {
							if (r.success) this.downloadCSV(r.data.csv, r.data.filename);
							else alert('Error: ' + (r.data?.message || 'Desconocido'));
						})
						.catch(() => alert('Error de conexión'));
				},

				downloadAll: function() {
					const cfg = this.getConfig();
					const params = new URLSearchParams();
					params.append('action', cfg.actions.download_all);
					params.append('nonce', cfg.nonce);

					fetch(cfg.ajaxUrl, {
						method: 'POST',
						headers: {'Content-Type': 'application/x-www-form-urlencoded'},
						body: params.toString(),
					})
						.then(r => r.json())
						.then(r => {
							if (r.success) this.downloadCSV(r.data.csv, r.data.filename);
							else alert('Error: ' + (r.data?.message || 'Desconocido'));
						})
						.catch(() => alert('Error de conexión'));
				},

				delete: function(id) {
					if (!confirm('¿Estás seguro de que deseas eliminar este registro?')) return;

					const cfg = this.getConfig();
					const params = new URLSearchParams();
					params.append('action', cfg.actions.delete_record);
					params.append('id', id);
					params.append('nonce', cfg.nonce);

					fetch(cfg.ajaxUrl, {
						method: 'POST',
						headers: {'Content-Type': 'application/x-www-form-urlencoded'},
						body: params.toString(),
					})
						.then(r => r.json())
						.then(r => {
							if (r.success) {
								alert('Registro eliminado');
								location.reload();
							} else {
								alert('Error: ' + (r.data?.message || 'Desconocido'));
							}
						})
						.catch(() => alert('Error de conexión'));
				},

				downloadCSV: function(csv, filename) {
					const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
					const link = document.createElement('a');
					const url = URL.createObjectURL(blob);
					link.setAttribute('href', url);
					link.setAttribute('download', filename);
					link.click();
					URL.revokeObjectURL(url);
				}
			};
		</script>
		<?php
		return ob_get_clean();
	}

	private function render_cell($row, $column) {
		$value = $row->{$column['field']} ?? '';

		if (isset($column['type'])) {
			switch ($column['type']) {
				case 'email':
					return '<a href="mailto:' . esc_attr($value) . '" class="avance-link">' . esc_html($value) . '</a>';

				case 'whatsapp':
					if ($value) {
						$clean = preg_replace('/[^0-9]/', '', $value);
						return '<a href="https://wa.me/' . esc_attr($clean) . '" target="_blank" rel="noopener" class="avance-link">' . esc_html($value) . '</a>';
					}
					return '—';

				case 'badge':
					$badges = $column['badges'] ?? array();
					$badge = $badges[$value] ?? array('Desconocido', '');
					return '<span class="avance-badge ' . esc_attr($badge[1]) . '">' . esc_html($badge[0]) . '</span>';

				case 'date':
					return esc_html(wp_date('d/m/Y H:i', strtotime($value)));

				default:
					return esc_html($value);
			}
		}

		return esc_html($value);
	}
}
