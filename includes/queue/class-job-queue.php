<?php
/**
 * Job Queue
 * Sistema de cola para procesar trabajos asincronicamente
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Job_Queue {

	const TABLE_NAME = 'avance_job_queue';
	const STATUS_PENDING = 'pending';
	const STATUS_PROCESSING = 'processing';
	const STATUS_COMPLETED = 'completed';
	const STATUS_FAILED = 'failed';

	/**
	 * Crear tabla de cola
	 */
	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			job_type VARCHAR(50) NOT NULL,
			contact_id BIGINT(20) UNSIGNED,
			data LONGTEXT NOT NULL,
			status VARCHAR(20) DEFAULT 'pending',
			attempts INT DEFAULT 0,
			max_attempts INT DEFAULT 3,
			error_message TEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			INDEX status_idx (status),
			INDEX job_type_idx (job_type),
			INDEX created_idx (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	/**
	 * Agregar trabajo a la cola
	 */
	public static function add($job_type, $contact_id, $data = []) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		try {
			$result = $wpdb->insert(
				$table_name,
				[
					'job_type' => $job_type,
					'contact_id' => $contact_id,
					'data' => wp_json_encode($data),
					'status' => self::STATUS_PENDING,
				]
			);

			if ($result) {
				$job_id = $wpdb->insert_id;
				Avance_Logger::info('Queue: Nuevo job agregado (job_id=' . $job_id . ', type=' . $job_type . ', contact_id=' . $contact_id . ')');
				wp_schedule_single_event(time(), 'avance_process_queue');
				return $job_id;
			} else {
				error_log('❌ ERROR: Fallo al agregar job a queue - ' . $wpdb->last_error);
				return false;
			}
		} catch (Exception $e) {
			error_log('❌ ERROR en Queue::add(): ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Procesar trabajos pendientes
	 */
	public static function process() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Obtener trabajo pendiente
		$job = $wpdb->get_row("
			SELECT * FROM {$table_name}
			WHERE status = '" . self::STATUS_PENDING . "'
			ORDER BY created_at ASC
			LIMIT 1
		");

		if (!$job) {
			return;
		}

		// Marcar como procesando
		$wpdb->update(
			$table_name,
			['status' => self::STATUS_PROCESSING],
			['id' => $job->id]
		);

		// Procesar según tipo
		$result = self::handle_job($job);

		if ($result['success']) {
			Avance_Logger::info('Queue: Job completado exitosamente (job_id=' . $job->id . ')');
			$wpdb->update(
				$table_name,
				[
					'status' => self::STATUS_COMPLETED,
					'updated_at' => current_time('mysql'),
				],
				['id' => $job->id]
			);
		} else {
			// ISSUE 16: Logging mejorado para fallos
			// Reintentar si no ha alcanzado máximo
			if ($job->attempts < $job->max_attempts) {
				$next_attempt = $job->attempts + 1;
				Avance_Logger::warning('Queue: Job falló, reintentando. Intento ' . $next_attempt . '/' . $job->max_attempts .
							' (job_id=' . $job->id . ') - Error: ' . $result['message']);

				$wpdb->update(
					$table_name,
					[
						'status' => self::STATUS_PENDING,
						'attempts' => $next_attempt,
						'error_message' => $result['message'],
						'updated_at' => current_time('mysql'),
					],
					['id' => $job->id]
				);
				wp_schedule_single_event(time() + 300, 'avance_process_queue');
			} else {
				// Máximo de intentos alcanzado → FALLÓ PERMANENTEMENTE
				Avance_Logger::critical(' Job falló permanentemente después de ' . $job->max_attempts .
							' intentos (job_id=' . $job->id . ', type=' . $job->job_type . ') - Error final: ' . $result['message']);

				$wpdb->update(
					$table_name,
					[
						'status' => self::STATUS_FAILED,
						'error_message' => $result['message'],
						'updated_at' => current_time('mysql'),
					],
					['id' => $job->id]
				);

				// Hook para notificaciones/alertas (puede implementarse después)
				do_action('avance_queue_job_permanently_failed', $job, $result);
			}
		}
	}

	/**
	 * Manejar trabajo según tipo
	 * ISSUE 16: Error Handling con try/catch + logging
	 * Previene silent failures y captura excepciones inesperadas
	 */
	private static function handle_job($job) {
		try {
			// 1. Validar y parsear JSON
			$data = json_decode($job->data, true);
			if (!is_array($data)) {
				throw new Exception('Datos JSON inválidos o no es un array: ' . $job->data);
			}

			Avance_Logger::warning('Queue: Iniciando job_id=' . $job->id . ', type=' . $job->job_type . ', attempt=' . ($job->attempts + 1));

			// 2. Procesar según tipo
			switch ($job->job_type) {
				case 'send_whatsapp':
					error_log('📱 Queue: Procesando send_whatsapp para contact_id=' . $job->contact_id);
					$result = self::send_whatsapp_job($data, $job->contact_id);
					if ($result['success']) {
						Avance_Logger::info('Queue: send_whatsapp exitoso para contact_id=' . $job->contact_id);
					} else {
						Avance_Logger::warning('Queue: send_whatsapp falló - ' . $result['message']);
					}
					return $result;

				case 'send_email':
					$email = $data['email'] ?? 'unknown';
					error_log('✉️ Queue: Procesando send_email para ' . $email);
					$result = self::send_email_job($data);
					if ($result['success']) {
						Avance_Logger::info('Queue: send_email exitoso para ' . $email);
					} else {
						Avance_Logger::warning('Queue: send_email falló - ' . $result['message']);
					}
					return $result;

				default:
					throw new Exception('Tipo de trabajo desconocido: ' . $job->job_type);
			}
		} catch (Exception $e) {
			// Capturar CUALQUIER excepción no manejada
			error_log('❌ ERROR en Queue (job_id=' . $job->id . '): ' . $e->getMessage() .
						' | Stacktrace: ' . $e->getTraceAsString());

			return [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
	}

	/**
	 * Enviar WhatsApp (asincrónico)
	 * ISSUE 16: Logging detallado de entrada/salida
	 */
	private static function send_whatsapp_job($data, $contact_id) {
		try {
			// Validar campos requeridos
			if (empty($data['mensaje'])) {
				throw new Exception('Campo mensaje vacío en WhatsApp job');
			}
			if (empty($data['numero'])) {
				throw new Exception('Campo numero vacío en WhatsApp job');
			}

			$message = urlencode($data['mensaje']);
			$phone = $data['numero'];

			// Validar formato de phone (básico)
			if (!preg_match('/^\d{9,15}$/', preg_replace('/[^0-9]/', '', $phone))) {
				throw new Exception('Formato de teléfono inválido: ' . $phone);
			}

			$url = "https://wa.me/{$phone}?text={$message}";

			// Validar que sea una URL válida
			if (!filter_var($url, FILTER_VALIDATE_URL)) {
				throw new Exception('URL de WhatsApp inválida construida');
			}

			Avance_Logger::info('WhatsApp Job: Enviando a contact_id=' . $contact_id . ', phone=' . substr($phone, -4));

			// Aquí se puede hacer log o webhook si es necesario
			do_action('avance_whatsapp_sent', $contact_id, $data);

			return ['success' => true];
		} catch (Exception $e) {
			error_log('❌ WhatsApp Job Error (contact_id=' . $contact_id . '): ' . $e->getMessage());
			return ['success' => false, 'message' => $e->getMessage()];
		}
	}

	/**
	 * Enviar email (asincrónico)
	 * ISSUE 16: Logging detallado de entrada/salida
	 */
	private static function send_email_job($data) {
		try {
			// Validar campos requeridos
			if (empty($data['email'])) {
				throw new Exception('Campo email vacío en email job');
			}

			$to = $data['email'];
			$subject = $data['subject'] ?? 'Confirmación de contacto';
			$body = $data['body'] ?? "Hola,\n\nRecibimos tu mensaje. Te contactaremos pronto.\n\nSaludos";

			// Validar email
			if (!is_email($to)) {
				throw new Exception('Email inválido: ' . $to);
			}

			Avance_Logger::info('Email Job: Enviando a ' . $to . ', subject: ' . $subject);

			$result = wp_mail($to, $subject, $body);

			if ($result) {
				error_log('✅ Email enviado exitosamente a ' . $to);
				return ['success' => true];
			} else {
				throw new Exception('wp_mail retornó false para ' . $to);
			}
		} catch (Exception $e) {
			Avance_Logger::error('Email Job Error: ' . $e->getMessage());
			return ['success' => false, 'message' => $e->getMessage()];
		}
	}
}

// Crear tabla al cargar
add_action('wp_loaded', ['Avance_Job_Queue', 'create_table']);

// Hook para procesar cola
add_action('avance_process_queue', ['Avance_Job_Queue', 'process']);
