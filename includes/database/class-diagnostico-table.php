<?php
/**
 * Diagnostico Table
 * Gestiona la tabla de diagnósticos en BD
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnostico_Table {
	private $table_name;
	private $charset_collate;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'avance_diagnosticos';
		$this->charset_collate = $wpdb->get_charset_collate();
	}

	public function create_table_if_not_exists() {
		global $wpdb;

		if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") !== $this->table_name) {
			$sql = "CREATE TABLE {$this->table_name} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				nombre_completo VARCHAR(255) NOT NULL,
				email VARCHAR(255) NOT NULL,
				whatsapp VARCHAR(20) NOT NULL,
				respuestas LONGTEXT NOT NULL,
				fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY email (email),
				KEY whatsapp (whatsapp)
			) {$this->charset_collate};";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta($sql);
		}
	}

	public function insert_diagnostico($data) {
		global $wpdb;

		$inserted = $wpdb->insert(
			$this->table_name,
			array(
				'nombre_completo' => sanitize_text_field($data['nombre_completo']),
				'email'           => sanitize_email($data['email']),
				'whatsapp'        => sanitize_text_field($data['whatsapp']),
				'respuestas'      => wp_json_encode($data['respuestas']),
			),
			array('%s', '%s', '%s', '%s')
		);

		if ($inserted) {
			return $wpdb->insert_id;
		}

		return false;
	}

	public function get_table_name() {
		return $this->table_name;
	}
}
