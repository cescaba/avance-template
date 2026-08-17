<?php
/**
 * Admin Appointments Panel - Premium Design
 *
 * Panel de administración elegante para ver todas las citas agendadas
 *
 * @package Avance_Template
 */

if (!current_user_can('manage_options')) {
	wp_die('No tienes permiso para acceder a esta página.');
}

global $wpdb;

$table_name = $wpdb->prefix . 'avance_appointments';
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
$limit = 10;
$offset = ($paged - 1) * $limit;

// Construir query
$where = array('1=1');
if (!empty($status_filter)) {
	$where[] = $wpdb->prepare('status = %s', $status_filter);
}
if (!empty($search)) {
	$search_term = '%' . $wpdb->esc_like($search) . '%';
	$where[] = $wpdb->prepare('(nombre LIKE %s OR whatsapp LIKE %s OR servicio LIKE %s)', $search_term, $search_term, $search_term);
}

$query = "SELECT * FROM {$table_name} WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT {$offset}, {$limit}";
$appointments = $wpdb->get_results($query);

$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE " . implode(' AND ', $where)));

// Estadísticas
$stats = array(
	'total' => intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name}")),
	'confirmada' => intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'confirmada'")),
	'pendiente' => intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'pendiente'")),
);
?>

<div class="avance-admin-container">
	<!-- HEADER -->
	<div class="avance-admin-header">
		<div class="avance-admin-title-section">
			<h1 class="avance-admin-title">📅 Panel de Citas Agendadas</h1>
			<p class="avance-admin-subtitle">Gestiona todas las citas y agendamientos recibidos</p>
		</div>
	</div>

	<!-- ESTADÍSTICAS -->
	<div class="avance-stats-grid">
		<div class="avance-stat-card avance-stat-total">
			<div class="avance-stat-icon">📊</div>
			<div class="avance-stat-content">
				<div class="avance-stat-number"><?php echo esc_html($stats['total']); ?></div>
				<div class="avance-stat-label">Total de Citas</div>
			</div>
		</div>

		<div class="avance-stat-card avance-stat-success">
			<div class="avance-stat-icon">✓</div>
			<div class="avance-stat-content">
				<div class="avance-stat-number"><?php echo esc_html($stats['confirmada']); ?></div>
				<div class="avance-stat-label">Confirmadas</div>
			</div>
		</div>

		<div class="avance-stat-card avance-stat-warning">
			<div class="avance-stat-icon">⏳</div>
			<div class="avance-stat-content">
				<div class="avance-stat-number"><?php echo esc_html($stats['pendiente']); ?></div>
				<div class="avance-stat-label">Pendientes</div>
			</div>
		</div>
	</div>

	<!-- FILTROS -->
	<div class="avance-filter-section">
		<form method="get" class="avance-filter-form">
			<input type="hidden" name="page" value="avance-appointments">

			<div class="avance-filter-group">
				<input
					type="search"
					name="s"
					placeholder="Buscar por nombre, WhatsApp o servicio..."
					value="<?php echo esc_attr($search); ?>"
					class="avance-search-input"
				>
			</div>

			<div class="avance-filter-group">
				<select name="status" class="avance-select-input">
					<option value="">Todos los estados</option>
					<option value="confirmada" <?php selected($status_filter, 'confirmada'); ?>>✓ Confirmadas</option>
					<option value="pendiente" <?php selected($status_filter, 'pendiente'); ?>>⏳ Pendientes</option>
				</select>
			</div>

			<div class="avance-filter-group">
				<button type="submit" class="avance-btn-primary">🔍 Filtrar</button>
			</div>
		</form>
	</div>

	<!-- TABLA DE CITAS -->
	<div class="avance-table-wrapper">
		<table class="avance-contacts-table">
			<thead>
				<tr>
					<th class="col-id">ID</th>
					<th class="col-nombre">Nombre</th>
					<th class="col-whatsapp">WhatsApp</th>
					<th class="col-servicio">Servicio</th>
					<th class="col-fecha">Fecha</th>
					<th class="col-hora">Hora</th>
					<th class="col-estado">Estado</th>
					<th class="col-accion">Acción</th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($appointments)) : ?>
					<tr class="avance-empty-row">
						<td colspan="8">
							<div class="avance-empty-state">
								<div style="font-size: 48px; margin-bottom: 10px;">📭</div>
								<p>Sin citas agendadas</p>
							</div>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ($appointments as $apt) : ?>
						<tr class="avance-table-row">
							<td class="col-id"><span class="avance-id-badge">#<?php echo esc_html($apt->id); ?></span></td>
							<td class="col-nombre">
								<strong><?php echo esc_html($apt->nombre); ?></strong>
							</td>
							<td class="col-whatsapp">
								<a href="https://wa.me/<?php echo esc_attr($apt->whatsapp); ?>" target="_blank" rel="noopener" class="avance-link avance-whatsapp-link">
									<?php echo esc_html($apt->whatsapp); ?>
								</a>
							</td>
							<td class="col-servicio">
								<span class="avance-service-badge"><?php echo esc_html($apt->servicio); ?></span>
							</td>
							<td class="col-fecha">
								<strong><?php echo esc_html(wp_date('d/m/Y', strtotime($apt->fecha))); ?></strong>
							</td>
							<td class="col-hora">
								<?php echo esc_html($apt->hora); ?>
							</td>
							<td class="col-estado">
								<?php if ($apt->status === 'confirmada') : ?>
									<span class="avance-badge avance-badge-success">✓ Confirmada</span>
								<?php else : ?>
									<span class="avance-badge avance-badge-secondary">⏳ Pendiente</span>
								<?php endif; ?>
							</td>
							<td class="col-accion">
								<button class="avance-btn-view" onclick="avanceShowAppointmentDetail(<?php echo esc_attr($apt->id); ?>); return false;">
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
	<?php if ($total > $limit) : ?>
		<div class="avance-pagination">
			<?php
			$page_links = paginate_links(array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => '← Anterior',
				'next_text' => 'Siguiente →',
				'total' => ceil($total / $limit),
				'current' => $paged,
				'echo' => false,
			));
			echo wp_kses_post($page_links);
			?>
		</div>
	<?php endif; ?>
</div>

<!-- MODAL PARA VER DETALLES -->
<div id="avance-appointment-modal" class="avance-modal">
	<div class="avance-modal-content">
		<button class="avance-modal-close" onclick="document.getElementById('avance-appointment-modal').style.display='none'">×</button>
		<div id="avance-appointment-detail">
			<p style="text-align: center;">Cargando...</p>
		</div>
	</div>
</div>

<script>
	function avanceShowAppointmentDetail(id) {
		const modal = document.getElementById('avance-appointment-modal');
		const content = document.getElementById('avance-appointment-detail');

		modal.style.display = 'flex';

		fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: 'action=avance_get_appointment&id=' + id + '&nonce=<?php echo esc_attr(wp_create_nonce('avance_admin')); ?>',
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
	document.getElementById('avance-appointment-modal').addEventListener('click', function(e) {
		if (e.target === this) {
			this.style.display = 'none';
		}
	});
</script>
