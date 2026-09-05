<?php
/**
 * Checkout Customizer - Personalización de WooCommerce checkout
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Checkout_Customizer {

	public function __construct() {
		// Deshabilitar bloques de checkout
		add_filter('woocommerce_blocks_checkout_enabled', '__return_false');
		add_filter('woocommerce_blocks_cart_enabled', '__return_false');

		// Encolar estilos y scripts personalizados
		add_action('wp_enqueue_scripts', [$this, 'enqueue_checkout_assets'], 100);

		// Google Fonts para checkout
		add_action('wp_head', [$this, 'enqueue_checkout_fonts']);

		// Customizar template order-received
		add_action('template_redirect', [$this, 'customize_order_received']);
	}

	public function enqueue_checkout_assets() {
		if (!is_checkout() && !is_wc_endpoint_url('order-pay') && !is_wc_endpoint_url('order-received')) {
			return;
		}

		// Remover CSS de WooCommerce
		wp_dequeue_style('woocommerce-general');
		wp_dequeue_style('woocommerce-layout');
		wp_dequeue_style('woocommerce-smallscreen');
		wp_dequeue_style('woocommerce-blocktheme');
		wp_dequeue_style('wc-block-style');
		wp_dequeue_style('wc-blocks-checkout');

		// Encolar CSS personalizado
		wp_enqueue_style(
			'checkout-custom',
			get_template_directory_uri() . '/assets/css/checkout.css',
			array(),
			filemtime(get_template_directory() . '/assets/css/checkout.css')
		);

		// Remover scripts de WooCommerce
		wp_dequeue_script('wc-checkout');
		wp_dequeue_script('wc-blocks-checkout');
	}

	public function enqueue_checkout_fonts() {
		if (!is_checkout() && !is_wc_endpoint_url('order-pay') && !is_wc_endpoint_url('order-received')) {
			return;
		}

		echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
		echo '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">';
	}

	public function customize_order_received() {
		if (!is_wc_endpoint_url('order-received')) {
			return;
		}

		// Remover hooks de WooCommerce
		remove_all_actions('woocommerce_thankyou');
		remove_all_actions('woocommerce_after_thankyou');
		remove_all_actions('the_content');

		// Remover shortcodes
		remove_shortcode('woocommerce_thankyou');

		// Filtrar contenido
		add_filter('the_content', [$this, 'render_order_received_template'], 999);
	}

	public function render_order_received_template($content) {
		$order_id = $this->get_order_id();

		if (!$order_id) {
			return '<p>Error: No se encontró la orden. Por favor verifica el enlace o contacta a soporte.</p>';
		}

		$order = wc_get_order($order_id);

		if (!$order) {
			return '<p>Error: No se encontró la orden con ID: ' . esc_html($order_id) . '</p>';
		}

		ob_start();
		include get_template_directory() . '/woocommerce/checkout/order-received.php';
		return ob_get_clean();
	}

	private function get_order_id() {
		$order_id = 0;

		// Intenta 1: $_GET['order']
		if (isset($_GET['order'])) {
			$order_id = absint($_GET['order']);
		}

		// Intenta 2: $_GET['order_id']
		if (!$order_id && isset($_GET['order_id'])) {
			$order_id = absint($_GET['order_id']);
		}

		// Intenta 3: URL reescrita (query_vars)
		if (!$order_id) {
			global $wp;
			if (isset($wp->query_vars['order-received'])) {
				$order_id = absint($wp->query_vars['order-received']);
			}
		}

		return $order_id;
	}
}
