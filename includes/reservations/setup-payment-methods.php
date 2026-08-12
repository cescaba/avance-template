<?php
/**
 * SETUP PAYMENT METHODS - Configurar métodos de pago automáticamente
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Activar métodos de pago básicos en WooCommerce
 */
add_action('wp_loaded', function() {
	// Solo si WooCommerce está activo
	if (!class_exists('WooCommerce')) {
		return;
	}

	// Verificar si ya se configuró
	if (get_option('avance_payment_methods_configured')) {
		return;
	}

	// Habilitar "Direct Bank Transfer" (Transferencia Directa)
	update_option('woocommerce_bacs_settings', array(
		'enabled' => 'yes',
		'title' => 'Transferencia Bancaria Directa',
		'description' => 'Realiza una transferencia bancaria directa a nuestra cuenta. Por favor utiliza el ID de tu pedido como referencia de pago.',
		'instructions' => 'Por favor realiza tu pago a:

Banco: [Tu Banco]
Titular: [Tu Nombre]
Número de Cuenta: [Tu Número]
Código SWIFT: [SWIFT Code]

Referencia de pago: Tu ID de pedido',
	));

	// Habilitar "Cash on Delivery" (Pago Efectivo/Manual)
	update_option('woocommerce_cod_settings', array(
		'enabled' => 'yes',
		'title' => 'Pago Manual / Efectivo',
		'description' => 'Completa el pago a través de tu forma preferida',
		'instructions' => 'Por favor contacta con nosotros para completar el pago de tu mentoría.',
	));

	// Marcar como configurado
	update_option('avance_payment_methods_configured', true);

	error_log('✅ Métodos de pago configurados automáticamente');
}, 999);
