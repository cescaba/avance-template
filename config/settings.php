<?php
/**
 * Configuración centralizada
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// WhatsApp - ÚNICO lugar donde se define
define('AVANCE_WHATSAPP', get_option('avance_whatsapp_number', '51993508652'));
define('AVANCE_WHATSAPP_NAME', get_option('avance_whatsapp_name', 'Avance Empresarial'));
define('AVANCE_THEME_VERSION', wp_get_theme()->get('Version'));
