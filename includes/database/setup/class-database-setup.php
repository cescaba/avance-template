<?php
/**
 * Database Setup - Crear tablas de base de datos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Database_Setup {

	public function __construct() {
		add_action('init', [$this, 'create_tables'], 1);
	}

	public function create_tables() {
		$current_version = get_option('avance_db_version');

		// Solo ejecutar si versión cambió
		if ($current_version === AVANCE_DB_VERSION) {
			return;
		}

		Avance_Logger::info('Avance DB: Actualizando esquema a ' . AVANCE_DB_VERSION);

		// Tabla de contactos (form-section)
		if (class_exists('Avance_Contact_DB')) {
			Avance_Contact_DB::create_table();
		}

		// Tabla de agendamientos (citas)
		if (class_exists('Avance_Agendamiento_Contacto_DB')) {
			Avance_Agendamiento_Contacto_DB::create_table();
		}

		// Tabla de calendario - agendamiento
		if (class_exists('Avance_Calendario_Reservas_DB')) {
			Avance_Calendario_Reservas_DB::create_table();
		}

		// Tabla de calendario - mentoría
		if (class_exists('Avance_Calendario_Reservas_Mentoria_DB')) {
			Avance_Calendario_Reservas_Mentoria_DB::create_table();
		}

		// Guardar versión
		update_option('avance_db_version', AVANCE_DB_VERSION);
		Avance_Logger::info('Avance DB: Esquema actualizado a ' . AVANCE_DB_VERSION);
	}
}
