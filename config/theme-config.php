<?php
/**
 * Avance Template - Theme Configuration
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Configuración de Contacto WhatsApp
 *
 * Número del dueño del sitio donde llegarán los mensajes del formulario
 * Formato: código país + número (sin +, sin espacios, sin guiones)
 *
 * Ejemplos:
 * - Perú: 51999000000
 * - Colombia: 573012345678
 * - Argentina: 541123456789
 */
define('AVANCE_WHATSAPP_OWNER', '51993508652');

/**
 * Obtener configuración de WhatsApp del propietario
 */
function avance_get_whatsapp_config() {
	return array(
		'owner_phone' => AVANCE_WHATSAPP_OWNER,
	);
}
