<?php
/**
 * Database Performance - Crear índices compuestos
 *
 * ISSUE 14: Índices de Base de Datos Incompletos
 * - Query check_duplicate() hace full table scan sin índice compuesto
 * - Solución: Agregar INDEX compuesto (email, created_at)
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Database_Performance {

	public function __construct() {
		add_action('wp_loaded', [$this, 'create_indices'], 999);
	}

	public function create_indices() {
		// Evitar ejecutar en cada page load
		if (get_transient('avance_db_indices_v2_checked')) {
			return;
		}

		global $wpdb;

		// 1. CONTACTOS - INDEX (email, created_at)
		// Usado por: check_duplicate() en form-contacto
		$wpdb->query("ALTER TABLE {$wpdb->prefix}avance_contacts
		              ADD INDEX IF NOT EXISTS composite_email_date (email, created_at)");

		// 2. PROPOSALS - INDEX (email, created_at)
		// Usado por: check_duplicate() en form-propuestas
		$wpdb->query("ALTER TABLE {$wpdb->prefix}avance_proposals
		              ADD INDEX IF NOT EXISTS composite_email_date (email, created_at)");

		// 3. AGENDAMIENTO CONTACTO - INDEX (whatsapp, created_at)
		// Usado por: check_duplicate() en form-agendamiento
		$wpdb->query("ALTER TABLE {$wpdb->prefix}avance_agendamiento_contacto
		              ADD INDEX IF NOT EXISTS composite_whatsapp_date (whatsapp, created_at)");

		// Marcar completado
		set_transient('avance_db_indices_v2_checked', true, MONTH_IN_SECONDS);
	}
}
