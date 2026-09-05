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
 * Configuración de reCAPTCHA v3
 *
 * Obtén las claves en: https://www.google.com/recaptcha/admin
 * Selecciona: reCAPTCHA v3
 */
define('AVANCE_RECAPTCHA_SITE_KEY', ''); // Reemplazar con clave pública
define('AVANCE_RECAPTCHA_SECRET', ''); // Reemplazar con clave secreta

/**
 * Obtener configuración de WhatsApp del propietario
 */
function avance_get_whatsapp_config() {
	return array(
		'owner_phone' => AVANCE_WHATSAPP_OWNER,
	);
}

/**
 * Obtener configuración de reCAPTCHA
 */
function avance_get_recaptcha_config() {
	return array(
		'site_key' => AVANCE_RECAPTCHA_SITE_KEY,
		'enabled' => !empty(AVANCE_RECAPTCHA_SITE_KEY),
	);
}
