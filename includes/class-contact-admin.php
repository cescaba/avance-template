<?php
/**
 * Contact Form Admin Panel - Premium Design
 *
 * Panel de administración elegante para ver todos los contactos del formulario
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Contact_Admin {

	public function __construct() {
		add_action('admin_menu', array($this, 'register_admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
	}

	/**
	 * Registrar página en el admin
	 */
	public function register_admin_menu() {
		add_menu_page(
			'Contactos del Formulario',
			'Contactos',
			'manage_options',
			'avance-contacts',
			array($this, 'render_admin_page'),
			'dashicons-email-alt',
			30
		);
	}

	/**
	 * Enqueuar estilos del admin
	 */
	public function enqueue_admin_styles($hook) {
		if ($hook !== 'toplevel_page_avance-contacts') {
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
		$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
		$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

		// Obtener datos
		$contacts = $this->get_contacts($status_filter, $search, $paged);
		$total = $this->get_contacts_count($status_filter, $search);
		$stats = $this->get_stats();

		?>
		<div class="avance-admin-container">
			<!-- HEADER -->
			<div class="avance-admin-header">
				<div class="avance-admin-title-section">
					<h1 class="avance-admin-title">📧 Panel de Contactos</h1>
					<p class="avance-admin-subtitle">Gestiona todos los contactos recibidos del formulario</p>
				</div>
			</div>

			<!-- ESTADÍSTICAS -->
			<div class="avance-stats-grid">
				<div class="avance-stat-card avance-stat-total">
					<div class="avance-stat-icon">📊</div>
					<div class="avance-stat-content">
						<div class="avance-stat-number"><?php echo esc_html($stats['total']); ?></div>
						<div class="avance-stat-label">Total de Contactos</div>
					</div>
				</div>

				<div class="avance-stat-card avance-stat-success">
					<div class="avance-stat-icon">✓</div>
					<div class="avance-stat-content">
						<div class="avance-stat-number"><?php echo esc_html($stats['enviado']); ?></div>
						<div class="avance-stat-label">Enviados a WhatsApp</div>
					</div>
				</div>

				<div class="avance-stat-card avance-stat-warning">
					<div class="avance-stat-icon">⚠️</div>
					<div class="avance-stat-content">
						<div class="avance-stat-number"><?php echo esc_html($stats['bloqueado']); ?></div>
						<div class="avance-stat-label">Bloqueados/Spam</div>
					</div>
				</div>
			</div>

			<!-- FILTROS -->
			<div class="avance-filter-section">
				<form method="get" class="avance-filter-form">
					<input type="hidden" name="page" value="avance-contacts">

					<div class="avance-filter-group">
						<input
							type="search"
							name="s"
							placeholder="Buscar por nombre, email o teléfono..."
							value="<?php echo esc_attr($search); ?>"
							class="avance-search-input"
						>
					</div>

					<div class="avance-filter-group">
						<select name="status" class="avance-select-input">
							<option value="">Todos los estados</option>
							<option value="enviado" <?php selected($status_filter, 'enviado'); ?>>✓ Enviados</option>
							<option value="bloqueado" <?php selected($status_filter, 'bloqueado'); ?>>✗ Bloqueados</option>
							<option value="spam" <?php selected($status_filter, 'spam'); ?>>🚫 Spam</option>
							<option value="error" <?php selected($status_filter, 'error'); ?>>⚠️ Error</option>
						</select>
					</div>

					<div class="avance-filter-group">
						<button type="submit" class="avance-btn-primary">🔍 Filtrar</button>
					</div>
				</form>
			</div>

			<!-- TABLA DE CONTACTOS -->
			<div class="avance-table-wrapper">
				<table class="avance-contacts-table">
					<thead>
						<tr>
							<th class="col-id">ID</th>
							<th class="col-nombre">Nombre</th>
							<th class="col-email">Email</th>
							<th class="col-whatsapp">WhatsApp</th>
							<th class="col-servicio">Servicio</th>
							<th class="col-estado">Estado</th>
							<th class="col-fecha">Fecha</th>
							<th class="col-accion">Acción</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($contacts)) : ?>
							<tr class="avance-empty-row">
								<td colspan="8">
									<div class="avance-empty-state">
										<div style="font-size: 48px; margin-bottom: 10px;">📭</div>
										<p>Sin contactos</p>
									</div>
								</td>
							</tr>
						<?php else : ?>
							<?php foreach ($contacts as $contact) : ?>
								<tr class="avance-table-row">
									<td class="col-id"><span class="avance-id-badge">#<?php echo esc_html($contact->id); ?></span></td>
									<td class="col-nombre">
										<strong><?php echo esc_html($contact->nombre); ?></strong>
									</td>
									<td class="col-email">
										<a href="mailto:<?php echo esc_attr($contact->email); ?>" class="avance-link">
											<?php echo esc_html($contact->email); ?>
										</a>
									</td>
									<td class="col-whatsapp">
										<a href="https://wa.me/<?php echo esc_attr($contact->numero); ?>" target="_blank" rel="noopener" class="avance-link avance-whatsapp-link">
											<?php echo esc_html($contact->numero); ?>
										</a>
									</td>
									<td class="col-servicio">
										<span class="avance-service-badge"><?php echo esc_html($contact->asunto); ?></span>
									</td>
									<td class="col-estado">
										<?php $this->render_status_badge($contact->status, $contact->validation_reason); ?>
									</td>
									<td class="col-fecha">
										<small><?php echo esc_html(wp_date('d/m/Y', strtotime($contact->created_at))); ?></small>
										<br>
										<small style="color: #999;"><?php echo esc_html(wp_date('H:i', strtotime($contact->created_at))); ?></small>
									</td>
									<td class="col-accion">
										<button class="avance-btn-view" onclick="avanceShowContactDetail(<?php echo esc_attr($contact->id); ?>); return false;">
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
		<div id="avance-contact-modal" class="avance-modal">
			<div class="avance-modal-content">
				<button class="avance-modal-close" onclick="document.getElementById('avance-contact-modal').style.display='none'">×</button>
				<div id="avance-contact-detail">
					<p style="text-align: center;">Cargando...</p>
				</div>
			</div>
		</div>

		<script>
			function avanceShowContactDetail(id) {
				const modal = document.getElementById('avance-contact-modal');
				const content = document.getElementById('avance-contact-detail');

				modal.style.display = 'flex';

				fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: 'action=avance_get_contact&id=' + id + '&nonce=<?php echo esc_attr(wp_create_nonce('avance_admin')); ?>',
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
			document.getElementById('avance-contact-modal').addEventListener('click', function(e) {
				if (e.target === this) {
					this.style.display = 'none';
				}
			});
		</script>
		<?php
	}

	/**
	 * Obtener contactos con filtros
	 */
	private function get_contacts($status = '', $search = '', $paged = 1) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'avance_contacts';
		$limit = 10;
		$offset = ($paged - 1) * $limit;

		$where = array('1=1');

		if (!empty($status)) {
			$where[] = $wpdb->prepare('status = %s', $status);
		}

		if (!empty($search)) {
			$search_term = '%' . $wpdb->esc_like($search) . '%';
			$where[] = $wpdb->prepare('(nombre LIKE %s OR email LIKE %s OR numero LIKE %s)', $search_term, $search_term, $search_term);
		}

		$query = "SELECT * FROM {$table_name} WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT {$offset}, {$limit}";

		return $wpdb->get_results($query);
	}

	/**
	 * Contar contactos con filtros
	 */
	private function get_contacts_count($status = '', $search = '') {
		global $wpdb;

		$table_name = $wpdb->prefix . 'avance_contacts';
		$where = array('1=1');

		if (!empty($status)) {
			$where[] = $wpdb->prepare('status = %s', $status);
		}

		if (!empty($search)) {
			$search_term = '%' . $wpdb->esc_like($search) . '%';
			$where[] = $wpdb->prepare('(nombre LIKE %s OR email LIKE %s OR numero LIKE %s)', $search_term, $search_term, $search_term);
		}

		$query = "SELECT COUNT(*) FROM {$table_name} WHERE " . implode(' AND ', $where);

		return intval($wpdb->get_var($query));
	}

	/**
	 * Obtener estadísticas
	 */
	private function get_stats() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'avance_contacts';

		$total = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name}"));
		$enviado = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'enviado'"));
		$bloqueado = intval($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status IN ('bloqueado', 'spam', 'error')"));

		return array(
			'total' => $total,
			'enviado' => $enviado,
			'bloqueado' => $bloqueado,
		);
	}

	/**
	 * Renderizar badge de estado
	 */
	private function render_status_badge($status, $reason = '') {
		$badges = array(
			'enviado' => array('icon' => '✓', 'class' => 'avance-badge-success', 'text' => 'Enviado'),
			'bloqueado' => array('icon' => '✗', 'class' => 'avance-badge-danger', 'text' => 'Bloqueado'),
			'spam' => array('icon' => '🚫', 'class' => 'avance-badge-warning', 'text' => 'Spam'),
			'error' => array('icon' => '⚠️', 'class' => 'avance-badge-error', 'text' => 'Error'),
			'pendiente' => array('icon' => '⏳', 'class' => 'avance-badge-secondary', 'text' => 'Pendiente'),
		);

		$badge = $badges[$status] ?? $badges['pendiente'];

		echo sprintf(
			'<span class="avance-badge %s" title="%s">%s %s</span>',
			esc_attr($badge['class']),
			esc_attr(!empty($reason) ? $reason : $badge['text']),
			esc_html($badge['icon']),
			esc_html($badge['text'])
		);
	}
}

// Inicializar admin
new Avance_Contact_Admin();

/**
 * AJAX handler para obtener detalles del contacto
 */
add_action('wp_ajax_avance_get_contact', function() {
	if (!current_user_can('manage_options')) {
		wp_send_json_error('No tienes permiso');
	}

	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_admin')) {
		wp_send_json_error('Nonce inválido');
	}

	global $wpdb;

	$contact_id = intval($_POST['id'] ?? 0);
	$contact = $wpdb->get_row($wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}avance_contacts WHERE id = %d",
		$contact_id
	));

	if (!$contact) {
		wp_send_json_error('Contacto no encontrado');
	}

	ob_start();
	?>
	<div class="avance-modal-header">
		<h2>Contacto #<?php echo esc_html($contact->id); ?></h2>
	</div>

	<div class="avance-modal-body">
		<div class="avance-detail-grid">
			<div class="avance-detail-field">
				<label>Nombre</label>
				<p class="avance-detail-value"><?php echo esc_html($contact->nombre); ?></p>
			</div>

			<div class="avance-detail-field">
				<label>Email</label>
				<p class="avance-detail-value">
					<a href="mailto:<?php echo esc_attr($contact->email); ?>" class="avance-link">
						<?php echo esc_html($contact->email); ?>
					</a>
				</p>
			</div>

			<div class="avance-detail-field">
				<label>WhatsApp</label>
				<p class="avance-detail-value">
					<a href="https://wa.me/<?php echo esc_attr($contact->numero); ?>" target="_blank" rel="noopener" class="avance-link">
						<?php echo esc_html($contact->numero); ?>
					</a>
				</p>
			</div>

			<div class="avance-detail-field">
				<label>Servicio</label>
				<p class="avance-detail-value"><?php echo esc_html($contact->asunto); ?></p>
			</div>

			<div class="avance-detail-field full-width">
				<label>Mensaje</label>
				<div class="avance-message-box">
					<?php echo nl2br(esc_html($contact->mensaje)); ?>
				</div>
			</div>

			<div class="avance-detail-field">
				<label>Estado</label>
				<p class="avance-detail-value"><?php echo esc_html($contact->status); ?></p>
			</div>

			<?php if (!empty($contact->validation_reason)) : ?>
				<div class="avance-detail-field">
					<label>Razón de Bloqueo</label>
					<p class="avance-detail-value avance-danger"><?php echo esc_html($contact->validation_reason); ?></p>
				</div>
			<?php endif; ?>

			<div class="avance-detail-field">
				<label>IP</label>
				<p class="avance-detail-value"><code><?php echo esc_html($contact->ip_address); ?></code></p>
			</div>

			<div class="avance-detail-field">
				<label>Fecha y Hora</label>
				<p class="avance-detail-value"><?php echo esc_html(wp_date('d/m/Y H:i:s', strtotime($contact->created_at))); ?></p>
			</div>
		</div>
	</div>
	<?php

	$html = ob_get_clean();

	wp_send_json_success(array('html' => $html));
});
