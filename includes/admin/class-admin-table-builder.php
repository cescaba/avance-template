<?php
/**
 * Admin Table Builder - Constructor genérico de tablas de admin
 * Renderiza tabla con datos dinámicos
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
		// Convertir datos a array simple (sin objetos complejos)
		$simple_data = [];
		if (is_array($this->config['data']) || is_object($this->config['data'])) {
			foreach ((array)$this->config['data'] as $row) {
				$simple_row = [];
				if (is_object($row)) {
					foreach ((array)$row as $key => $value) {
						// Convertir a string simple para evitar problemas de encoding
						$simple_row[$key] = is_scalar($value) ? $value : (string)$value;
					}
				} else {
					$simple_row = (array)$row;
				}
				$simple_data[] = $simple_row;
			}
		}

		// Codificar SOLO lo necesario, sin 'config'
		$data_json = wp_json_encode([
			'title' => (string)$this->config['title'],
			'subtitle' => (string)$this->config['subtitle'],
			'columns' => (array)$this->config['columns'],
			'data' => $simple_data,
			'total' => intval($this->config['total']),
			'stats' => (array)$this->config['stats']
		]);

		if ($data_json === false) {
			error_log('Admin Table Builder: JSON encoding failed for ' . $this->config['title']);
			$data_json = '{"data":[],"title":"Error","subtitle":"No se pudieron cargar los datos","columns":[],"stats":[],"total":0}';
		}

		$nonce = wp_create_nonce($this->config['nonce_action'] ?? 'avance_admin');
		$ajax_url = admin_url('admin-ajax.php');

		// Encolada CSS desde functions.php
		ob_start();
		?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/admin-premium.css">

<div class="admin-panel">
  <div class="admin-header">
    <div class="admin-header-content">
      <h2 id="admin-title"><?php echo esc_html($this->config['title']); ?></h2>
      <p id="admin-subtitle"><?php echo esc_html($this->config['subtitle']); ?></p>
    </div>
    <div class="admin-header-buttons">
      <button id="btn-download-all">Descargar todo</button>
    </div>
  </div>

  <div class="stats-container" id="stats-container">
    <div class="stat-card">
      <h3 class="stat-label">Total de datos</h3>
      <div class="stat-number" id="stat-total">0</div>
    </div>
    <div class="stat-card">
      <h3 class="stat-label">Datos denegado</h3>
      <div class="stat-number" id="stat-denegado">0</div>
    </div>
    <div class="stat-card">
      <h3 class="stat-label">Datos en proceso</h3>
      <div class="stat-number" id="stat-proceso">0</div>
    </div>
  </div>

  <div class="admin-search">
    <input type="search" id="search-input" placeholder="Buscar por cualquier campo...">
  </div>

  <div class="admin-table-wrap">
    <table id="admin-table">
      <thead id="admin-thead"></thead>
      <tbody id="admin-tbody"></tbody>
    </table>
    <div id="admin-empty" class="empty-state" hidden>Sin datos</div>
  </div>
</div>

<!-- Modal de detalles -->
<div id="modal-overlay" class="modal-overlay" hidden>
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title">Detalles del Registro</h3>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body" id="modal-body">
      <!-- Los detalles se cargan aquí -->
    </div>
  </div>
</div>

<script type="application/json" id="admin-data-json"><?php echo $data_json; ?></script>
<script type="application/json" id="admin-config-json"><?php echo wp_json_encode($this->config['ajax_actions'] ?? []); ?></script>
<script>
// Datos del panel admin inyectados desde PHP
try {
	const dataEl = document.getElementById('admin-data-json');
	window.adminData = dataEl ? JSON.parse(dataEl.textContent) : { data: [], columns: [] };
} catch (e) {
	console.error('Error parsing admin data:', e);
	window.adminData = { data: [], columns: [] };
}

// Configuración de AJAX y seguridad
try {
	const configEl = document.getElementById('admin-config-json');
	const actions = configEl ? JSON.parse(configEl.textContent) : {};
	window.adminConfig = {
		ajaxUrl: '<?php echo esc_url($ajax_url); ?>',
		nonce: '<?php echo esc_attr($nonce); ?>',
		actions: actions
	};
} catch (e) {
	console.error('Error parsing admin config:', e);
	window.adminConfig = { ajaxUrl: '', nonce: '', actions: {} };
}

// Manejadores de acciones AJAX
window.adminActions = {
	async view(id) {
		const params = new URLSearchParams({
			action: window.adminConfig.actions.get_record,
			id: id,
			nonce: window.adminConfig.nonce
		});
		const res = await fetch(window.adminConfig.ajaxUrl, { method: 'POST', body: params });
		const json = await res.json();
		return json.success ? json.data : null;
	},

	async download(id) {
		const params = new URLSearchParams({
			action: window.adminConfig.actions.download_record,
			id: id,
			nonce: window.adminConfig.nonce
		});
		const res = await fetch(window.adminConfig.ajaxUrl, { method: 'POST', body: params });
		return await res.json();
	},

	async downloadAll() {
		const params = new URLSearchParams({
			action: window.adminConfig.actions.download_all,
			nonce: window.adminConfig.nonce
		});
		const res = await fetch(window.adminConfig.ajaxUrl, { method: 'POST', body: params });
		return await res.json();
	},

	async delete(id) {
		const params = new URLSearchParams({
			action: window.adminConfig.actions.delete_record,
			id: id,
			nonce: window.adminConfig.nonce
		});
		const res = await fetch(window.adminConfig.ajaxUrl, { method: 'POST', body: params });
		return await res.json();
	}
};
</script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/admin-table.js"></script>
		<?php
		return ob_get_clean();
	}
}
