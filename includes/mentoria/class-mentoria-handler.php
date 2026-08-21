<?php
/**
 * Mentoria Booking Handler
 * Crea carrito en WooCommerce y redirige a checkout
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

class Avance_Mentoria_Handler {

    public function __construct() {
        add_action('wp_ajax_avance_mentoria_booking', [$this, 'handle_booking']);
        add_action('wp_ajax_nopriv_avance_mentoria_booking', [$this, 'handle_booking']);
    }

    /**
     * Handle Mentoria Booking
     */
    public function handle_booking() {
        check_ajax_referer('avance_mentoria_booking', 'nonce');

        // Verificar que WooCommerce esté activado
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(['message' => 'WooCommerce no está disponible']);
            return;
        }

        // Sanitizar
        $data = [
            'nombre'          => sanitize_text_field($_POST['nombre'] ?? ''),
            'whatsapp'        => sanitize_text_field($_POST['whatsapp'] ?? ''),
            'email'           => sanitize_email($_POST['email'] ?? ''),
            'desafio'         => sanitize_textarea_field($_POST['desafio'] ?? ''),
            'plan'            => sanitize_text_field($_POST['plan'] ?? ''),
            'fecha'           => sanitize_text_field($_POST['fecha'] ?? ''),
            'hora'            => sanitize_text_field($_POST['hora'] ?? ''),
            'payment_method'  => sanitize_text_field($_POST['payment_method'] ?? 'visa'),
        ];

        // Validar
        if (!$this->validate($data)) {
            wp_send_json_error(['message' => 'Datos inválidos']);
            return;
        }

        // Mapear plan a producto de WooCommerce
        $product_id = $this->get_product_by_plan($data['plan']);
        if (!$product_id) {
            wp_send_json_error(['message' => 'Plan no disponible']);
            return;
        }

        // Limpiar carrito anterior
        if (WC()->cart) {
            WC()->cart->empty_cart();
        }

        // Agregar producto al carrito
        WC()->cart->add_to_cart($product_id, 1, 0, [], [
            'mentoria_nombre'          => $data['nombre'],
            'mentoria_whatsapp'        => $data['whatsapp'],
            'mentoria_email'           => $data['email'],
            'mentoria_desafio'         => $data['desafio'],
            'mentoria_fecha'           => $data['fecha'],
            'mentoria_hora'            => $data['hora'],
            'mentoria_plan'            => $data['plan'],
            'mentoria_payment_method'  => $data['payment_method'],
        ]);

        // URL de checkout
        $checkout_url = wc_get_checkout_url();

        if (!$checkout_url) {
            wp_send_json_error(['message' => 'No se pudo obtener URL de checkout']);
            return;
        }

        wp_send_json_success([
            'message' => 'Carrito creado. Redirigiendo a pago...',
            'checkout_url' => $checkout_url,
        ]);
    }

    /**
     * Validar datos de reserva
     */
    private function validate($data) {
        if (empty($data['nombre']) || empty($data['whatsapp']) || empty($data['email']) || empty($data['fecha']) || empty($data['hora']) || empty($data['plan'])) {
            return false;
        }

        if (!is_email($data['email'])) {
            return false;
        }

        return true;
    }

    /**
     * Obtener producto de WooCommerce por plan
     */
    private function get_product_by_plan($plan) {
        // Mapeo de planes a productos WooCommerce
        $plan_map = [
            'entrada'  => 'Entrada',
            'pro'      => 'Pro',
            'puntual'  => 'Sesión puntual',
        ];

        $product_name = $plan_map[$plan] ?? null;
        if (!$product_name) {
            return null;
        }

        // Buscar producto por nombre
        $args = [
            'post_type'  => 'product',
            'posts_per_page' => 1,
            's'          => $product_name,
        ];

        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            wp_reset_postdata();
            return $product_id;
        }

        return null;
    }
}

new Avance_Mentoria_Handler();
