<?php
/**
 * Footer Helpers - Funciones auxiliares para el footer
 * Production Safe - Sin errores en hosting
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Footer_Helpers {

	/**
	 * Obtiene el email de contacto de forma segura
	 *
	 * @return string Email o string vacío
	 */
	public static function get_contact_email() {
		// Opción 1: Constante en wp-config.php
		if (defined('AVANCE_CONTACT_EMAIL')) {
			return AVANCE_CONTACT_EMAIL;
		}
		// Opción 2: Opción de WordPress
		return get_option('avance_contact_email', 'informes@avance-empresarial.com');
	}

	/**
	 * Obtiene el teléfono principal
	 *
	 * @return string Teléfono o string vacío
	 */
	public static function get_contact_phone_primary() {
		// Opción 1: Constante en wp-config.php
		if (defined('AVANCE_CONTACT_PHONE_PRIMARY')) {
			return AVANCE_CONTACT_PHONE_PRIMARY;
		}
		// Opción 2: Opción de WordPress
		return get_option('avance_contact_phone_primary', '+51 993 508 652');
	}

	/**
	 * Obtiene el link de una página por slug de forma segura
	 *
	 * @param string $slug El slug de la página
	 * @return string URL de la página o home_url si no existe
	 */
	public static function get_page_link($slug) {
		$page = get_page_by_path($slug, OBJECT, 'page');
		if ($page) {
			return get_permalink($page->ID);
		}
		// Fallback: vuelve a home si la página no existe
		return home_url('/');
	}

	/**
	 * Obtiene el link de WhatsApp formateado (wa.me)
	 * Extrae solo números del teléfono
	 *
	 * @return string Link wa.me con número formateado o string vacío
	 */
	public static function get_whatsapp_link() {
		$phone = self::get_contact_phone_primary();
		if (!$phone) {
			return '';
		}
		// Extraer solo números y signo + del teléfono
		$phone_clean = preg_replace('/[^0-9+]/', '', $phone);
		// Eliminar + si está al inicio
		$phone_clean = ltrim($phone_clean, '+');
		if (!$phone_clean) {
			return '';
		}
		return 'https://wa.me/' . $phone_clean;
	}
}
