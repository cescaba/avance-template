<?php
/**
 * Header Helpers - Funciones auxiliares para el header
 * Production Safe - Sin errores en hosting
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Header_Helpers {

	/**
	 * Detecta si una página está activa por slug
	 * Seguro para usar cuando la página no existe
	 *
	 * @param string $slug El slug de la página
	 * @return bool True si la página existe y está activa
	 */
	public static function is_page_active($slug) {
		$page = get_page_by_path($slug, OBJECT, 'page');
		if (!$page) {
			return false;
		}
		return is_page($page->ID);
	}

	/**
	 * Obtiene la URL de WhatsApp de forma segura
	 * Intenta en orden: Constant → Option → empty string
	 *
	 * @return string URL de WhatsApp o string vacío
	 */
	public static function get_whatsapp_url() {
		// Opción 1: Constante en wp-config.php
		if (defined('AVANCE_WHATSAPP_URL')) {
			return AVANCE_WHATSAPP_URL;
		}
		// Opción 2: Opción de WordPress (configurable desde admin)
		$url = get_option('avance_whatsapp_url', '');
		return esc_url($url);
	}
}
