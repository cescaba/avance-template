<?php
/**
 * WooCommerce Integration
 * Organiza toda la lógica de integración con WooCommerce
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Menciones de Mentoría (Reservas y Checkout)
require_once dirname(__DIR__) . '/mentoria/class-mentoria-handler.php';
require_once dirname(__DIR__) . '/mentoria/class-mentoria-updater.php';
require_once dirname(__DIR__) . '/mentoria/class-mentoria-woocommerce-fix.php';
