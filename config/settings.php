<?php
/**
 * Configuración centralizada
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ISSUE 17: Obtener configuración con validación y fallback robusto
 *
 * Previene silent failures si get_option retorna vacío o inválido
 * - Valida que no esté vacío
 * - Validación opcional de formato telefónico
 * - Logging si se usa fallback
 *
 * @param string $key Nombre de la opción
 * @param string $default Valor por defecto si está vacío
 * @param bool $validate_phone Validar formato de teléfono (9-15 dígitos)
 * @return string Valor de la opción o fallback
 */
function avance_get_config($key, $default = '', $validate_phone = false) {
	$value = get_option($key, $default);

	// Si está vacío, usar fallback (pero no si es "0" que es válido)
	if (empty($value) && '0' !== $value) {
		if ($value !== $default && !empty($key)) {
			Avance_Logger::warning('CONFIG: Opción "' . $key . '" está vacía, usando fallback: ' . $default);
		}
		return $default;
	}

	// Validar teléfono si es requerido
	if ($validate_phone) {
		$phone = preg_replace('/[^0-9]/', '', (string)$value);
		if (strlen($phone) < 9 || strlen($phone) > 15) {
			Avance_Logger::warning('CONFIG: Opción "' . $key . '" tiene formato inválido (' . $value . '), usando fallback: ' . $default);
			return $default;
		}
	}

	return $value;
}

// WhatsApp - ÚNICO lugar donde se define
// ISSUE 17: Con validación y fallback robusto
define('AVANCE_WHATSAPP', avance_get_config('avance_whatsapp_number', '51993508652', true));
define('AVANCE_WHATSAPP_NAME', avance_get_config('avance_whatsapp_name', 'Avance Empresarial'));
define('AVANCE_WHATSAPP_URL', 'https://wa.me/' . AVANCE_WHATSAPP);
define('AVANCE_WHATSAPP_ATTRS', 'target="_blank" rel="noopener noreferrer"');
define('AVANCE_THEME_VERSION', wp_get_theme()->get('Version'));

/**
 * Obtener enlace de WhatsApp con mensaje (Helper Function)
 * Centraliza la construcción de URLs de WhatsApp
 *
 * @param string $message Mensaje opcional para agregar en ?text=
 * @return string URL de WhatsApp con mensaje si se proporciona
 */
function avance_get_whatsapp_link($message = '') {
	$base_url = AVANCE_WHATSAPP_URL;
	if (!empty($message)) {
		return $base_url . '?text=' . urlencode($message);
	}
	return $base_url;
}
