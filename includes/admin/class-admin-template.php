<?php
/**
 * Admin Template - Generador de interfaces admin reutilizables
 * Genera HTML dinámico para cualquier tabla de datos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Admin_Template {

	private $title;
	private $subtitle;
	private $records;
	private $total;
	private $columns;
	private $nonce;
	private $table_name;

	/**
	 * Constructor
	 *
	 * @param string $title Título del admin
	 * @param string $subtitle Subtítulo
	 * @param array $records Registros de la BD
	 * @param int $total Total de registros
	 * @param array $columns Array de columnas ['field' => 'nombre', 'label' => 'Nombre']
	 * @param string $nonce Token de seguridad
	 */
	public function __construct($title, $subtitle, $records, $total, $columns, $nonce, $table_name = '') {
		$this->title = $title;
		$this->subtitle = $subtitle;
		$this->records = $records ?? [];
		$this->total = intval($total);
		$this->columns = $columns;
		$this->nonce = $nonce;
		$this->table_name = $table_name;
	}

	/**
	 * Renderiza el HTML del admin
	 */
	public function render() {
		?>
		<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/admin-universal.css'); ?>">

		<div class="admin-container">
			<!-- Header -->
			<div class="admin-header">
				<div class="admin-header-content">
					<h1 class="admin-title"><?php echo esc_html($this->title); ?></h1>
					<p class="admin-subtitle"><?php echo esc_html($this->subtitle); ?></p>
				</div>

				<div class="admin-stats">
					<div class="admin-stat">
						<div class="admin-stat-content">
							<div class="admin-stat-label">Total</div>
							<div class="admin-stat-value"><?php echo intval($this->total); ?></div>
						</div>
					</div>
				</div>
			</div>

			<!-- Search -->
			<div class="admin-search-section">
				<input
					type="search"
					class="admin-search-input"
					placeholder="Buscar..."
					aria-label="Buscar registros"
				>
				<span class="admin-search-results"></span>
			</div>

			<!-- Downloads Section -->
			<div class="admin-downloads-section">
				<button class="admin-btn admin-btn-download-all" title="Descargar todos los datos">Descargar Todo (CSV)</button>
			</div>

			<!-- Table -->
			<div class="admin-table-section">
				<div class="admin-table-wrapper">
					<table class="admin-table">
						<thead>
							<tr>
								<?php $this->render_headers(); ?>
								<th class="admin-col-actions">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php $this->render_rows(); ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Pagination -->
			<div class="admin-pagination">
				<span class="admin-pagination-info">
					Mostrando <?php echo count($this->records); ?> de <?php echo intval($this->total); ?> registros
				</span>
				<div class="admin-pagination-buttons">
					<button class="admin-btn-pagination admin-btn-prev" disabled>Anterior</button>
					<button class="admin-btn-pagination admin-btn-next">Siguiente</button>
				</div>
			</div>

			<input type="hidden" class="admin-nonce" value="<?php echo esc_attr($this->nonce); ?>">
			<input type="hidden" class="admin-table-name" value="<?php echo esc_attr($this->table_name); ?>">
		</div>

		<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/admin.js'); ?>"></script>
		<?php
	}

	/**
	 * Renderiza headers de la tabla
	 */
	protected function render_headers() {
		foreach ($this->columns as $col) {
			$class = 'admin-col-' . sanitize_html_class($col['field']);
			echo '<th class="' . esc_attr($class) . '">' . esc_html($col['label']) . '</th>';
		}
	}

	/**
	 * Renderiza filas de la tabla - puede ser sobrescrito en subclases
	 */
	protected function render_rows() {
		if (empty($this->records)) {
			?>
			<tr>
				<td colspan="<?php echo count($this->columns) + 1; ?>">
					<div class="admin-empty-state">
						<div class="admin-empty-title">Sin registros</div>
						<div class="admin-empty-message">No hay datos para mostrar</div>
					</div>
				</td>
			</tr>
			<?php
			return;
		}

		foreach ($this->records as $record) {
			?>
			<tr class="admin-row" data-id="<?php echo esc_attr($record->id); ?>">
				<?php foreach ($this->columns as $col) {
					$field = $col['field'];
					$value = $this->format_field($field, $record);
					$class = 'admin-col-' . sanitize_html_class($field);
					echo '<td class="' . esc_attr($class) . '">' . $value . '</td>';
				} ?>
				<td class="admin-col-actions">
					<button class="admin-btn admin-btn-view" data-id="<?php echo esc_attr($record->id); ?>" title="Ver">Ver</button>
					<button class="admin-btn admin-btn-download" data-id="<?php echo esc_attr($record->id); ?>" title="Descargar">Descargar</button>
					<button class="admin-btn admin-btn-delete" data-id="<?php echo esc_attr($record->id); ?>" title="Eliminar">Eliminar</button>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Formatea el valor de un campo - puede ser sobrescrito en subclases
	 * para personalizar ciertos campos sin duplicar render_rows()
	 */
	protected function format_field($field, $record) {
		$value = $record->$field ?? '—';
		return esc_html($value);
	}

	protected function get_records() {
		return $this->records;
	}

	protected function get_columns() {
		return $this->columns;
	}

	protected function get_total() {
		return $this->total;
	}

	protected function get_nonce() {
		return $this->nonce;
	}

	protected function get_table_name() {
		return $this->table_name;
	}

	/**
	 * Genera archivo CSV de un registro
	 */
	protected function generate_csv_record($record) {
		$csv = '';
		$csv .= "ID,Campo,Valor\n";

		foreach ($this->columns as $col) {
			$field = $col['field'];
			$label = $col['label'];
			$value = $record->$field ?? '';
			$value = str_replace('"', '""', $value);
			$csv .= '"' . $record->id . '","' . $label . '","' . $value . "\"\n";
		}

		return $csv;
	}

	/**
	 * Genera archivo CSV de todos los registros
	 */
	protected function generate_csv_all($records) {
		$csv = '';
		$headers = ['ID'];

		foreach ($this->columns as $col) {
			$headers[] = $col['label'];
		}

		$csv .= implode(',', array_map(function($h) {
			return '"' . str_replace('"', '""', $h) . '"';
		}, $headers)) . "\n";

		foreach ($records as $record) {
			$row = ['"' . $record->id . '"'];
			foreach ($this->columns as $col) {
				$field = $col['field'];
				$value = $record->$field ?? '';
				$value = str_replace('"', '""', $value);
				$row[] = '"' . $value . '"';
			}
			$csv .= implode(',', $row) . "\n";
		}

		return $csv;
	}

	/**
	 * Descarga archivo CSV
	 */
	protected function send_csv_download($csv, $filename) {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo $csv;
		exit;
	}
}
