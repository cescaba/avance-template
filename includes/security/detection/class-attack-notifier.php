<?php
/**
 * Attack Notifier
 * Notificaciones por email de ataques detectados
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Attack_Notifier {

	const TRANSIENT_KEY = 'avance_attack_notified';
	const NOTIFICATION_COOLDOWN = HOUR_IN_SECONDS; // Máx 1 notif/hora

	/**
	 * Enviar notificación de ataque
	 */
	public static function notify_attack($ip, $reason, $details = []) {
		// Verificar cooldown (no enviar múltiples en poco tiempo)
		$last_notif = get_transient(self::TRANSIENT_KEY . '_' . $ip);
		if ($last_notif !== false) {
			return;
		}

		// Obtener info de IP
		$ip_info = Avance_IP_Blacklist::get_ip_info($ip);

		// Construir email
		$to = get_option('admin_email');
		$subject = '🚨 Ataque Detectado - ' . $_SERVER['HTTP_HOST'];

		$message = self::build_email_body([
			'ip' => $ip,
			'reason' => $reason,
			'ip_info' => $ip_info,
			'details' => $details,
		]);

		// Enviar
		wp_mail($to, $subject, $message, [
			'Content-Type: text/html; charset=UTF-8',
		]);

		// Registrar cooldown
		set_transient(self::TRANSIENT_KEY . '_' . $ip, true, self::NOTIFICATION_COOLDOWN);

		// Registrar en log
		error_log("Ataque detectado: {$reason} desde {$ip}");
	}

	/**
	 * Construir cuerpo del email
	 */
	private static function build_email_body($data) {
		$ip = $data['ip'];
		$reason = $data['reason'];
		$info = $data['ip_info'];
		$details = $data['details'];

		$html = '
		<html>
		<head>
			<style>
				body { font-family: Arial, sans-serif; background: #f5f5f5; }
				.container { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
				.header { background: #dc3545; color: white; padding: 20px; border-radius: 4px; margin-bottom: 20px; }
				.section { margin-bottom: 20px; }
				.label { font-weight: bold; color: #333; }
				.value { color: #666; margin-bottom: 10px; }
				.alert { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
				.footer { color: #999; font-size: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h2>🚨 Alerta de Seguridad</h2>
					<p>Se detectó actividad sospechosa en tu sitio web</p>
				</div>

				<div class="alert">
					<strong>Razón:</strong> ' . esc_html($reason) . '
				</div>

				<div class="section">
					<div class="label">IP Atacante:</div>
					<div class="value">' . esc_html($ip) . '</div>

					<div class="label">País:</div>
					<div class="value">' . esc_html($info['country'] ?? 'Desconocido') . '</div>

					<div class="label">Ciudad:</div>
					<div class="value">' . esc_html($info['city'] ?? 'Desconocida') . '</div>

					<div class="label">ISP:</div>
					<div class="value">' . esc_html($info['isp'] ?? 'Desconocido') . '</div>
				</div>';

		if (!empty($details)) {
			$html .= '<div class="section">
				<div class="label">Detalles:</div>';
			foreach ($details as $key => $value) {
				$html .= '<div class="value">' . esc_html($key) . ': ' . esc_html($value) . '</div>';
			}
			$html .= '</div>';
		}

		$html .= '
				<div class="section">
					<div class="label">Acción Tomada:</div>
					<div class="value">✅ IP bloqueada automáticamente</div>
				</div>

				<div class="section">
					<a href="' . admin_url() . '" style="display: inline-block; background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
						Ver Panel de Admin
					</a>
				</div>

				<div class="footer">
					<p>Este email fue generado automáticamente por el sistema de seguridad de ' . esc_html($_SERVER['HTTP_HOST']) . '</p>
					<p>Correo enviado el ' . current_time('d/m/Y H:i:s') . '</p>
				</div>
			</div>
		</body>
		</html>';

		return $html;
	}

	/**
	 * Enviar reporte diario de intentos bloqueados
	 */
	public static function send_daily_report() {
		global $wpdb;
		$attempts_table = $wpdb->prefix . 'avance_form_attempts';
		$blacklist_table = $wpdb->prefix . 'avance_ip_blacklist';

		// Contar intentos bloqueados hoy
		$blocked_today = $wpdb->get_var("
			SELECT COUNT(*) FROM {$attempts_table}
			WHERE blocked = 1
			AND DATE(last_attempt) = CURDATE()
		");

		if ($blocked_today == 0) {
			return; // No enviar si no hay bloques
		}

		// Obtener IPs más activas bloqueadas
		$top_ips = $wpdb->get_results("
			SELECT ip_address, COUNT(*) as attempts
			FROM {$attempts_table}
			WHERE blocked = 1
			AND DATE(last_attempt) = CURDATE()
			GROUP BY ip_address
			ORDER BY attempts DESC
			LIMIT 5
		");

		// Construir email
		$to = get_option('admin_email');
		$subject = '📊 Reporte Diario de Seguridad - ' . $_SERVER['HTTP_HOST'];

		$html = '
		<html>
		<head>
			<style>
				body { font-family: Arial, sans-serif; background: #f5f5f5; }
				.container { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
				.header { background: #28a745; color: white; padding: 20px; border-radius: 4px; margin-bottom: 20px; }
				table { width: 100%; border-collapse: collapse; margin: 20px 0; }
				th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
				th { background: #f5f5f5; font-weight: bold; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h2>📊 Reporte Diario de Seguridad</h2>
					<p>' . current_time('d/m/Y') . '</p>
				</div>

				<div style="margin: 20px 0;">
					<p><strong>Intentos bloqueados hoy:</strong> ' . $blocked_today . '</p>
				</div>

				<h3>Top 5 IPs Bloqueadas</h3>
				<table>
					<thead>
						<tr>
							<th>IP</th>
							<th>Intentos</th>
						</tr>
					</thead>
					<tbody>';

		foreach ($top_ips as $row) {
			$html .= '<tr>
				<td>' . esc_html($row->ip_address) . '</td>
				<td>' . $row->attempts . '</td>
			</tr>';
		}

		$html .= '
					</tbody>
				</table>

				<div style="text-align: center; margin-top: 20px;">
					<a href="' . admin_url() . '" style="display: inline-block; background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
						Ver Estadísticas Completas
					</a>
				</div>
			</div>
		</body>
		</html>';

		wp_mail($to, $subject, $html, [
			'Content-Type: text/html; charset=UTF-8',
		]);
	}
}

// Programar reporte diario
add_action('wp_scheduled_delete', ['Avance_Attack_Notifier', 'send_daily_report']);
