<?php
/**
 * Mentoria Products Updater
 * Actualiza los productos de WooCommerce con nombres y precios correctos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

class Avance_Mentoria_Updater {

    public function __construct() {
        add_action('wp_loaded', [$this, 'update_mentoria_products']);
    }

    /**
     * Actualizar productos de mentoría
     */
    public function update_mentoria_products() {
        if (get_option('avance_mentoria_products_updated')) {
            return;
        }

        if (!class_exists('WooCommerce')) {
            return;
        }

        $updates = [
            'Mentoría Básica' => [
                'name' => 'Entrada',
                'price' => '490',
                'desc' => 'Sesiones focalizadas en tu desafío principal.',
            ],
            'Mentoría Premium' => [
                'name' => 'Pro',
                'price' => '890',
                'desc' => 'Acompañamiento completo con acceso WhatsApp.',
            ],
            'Mentoría VIP' => [
                'name' => 'Sesión puntual',
                'price' => '180',
                'desc' => 'Plan anual con sesiones semanales.',
            ],
        ];

        foreach ($updates as $old_name => $new_data) {
            $this->update_product($old_name, $new_data);
        }

        update_option('avance_mentoria_products_updated', true);
    }

    /**
     * Actualizar un producto
     */
    private function update_product($old_name, $new_data) {
        $args = [
            'post_type'  => 'product',
            'posts_per_page' => 1,
            's'          => $old_name,
        ];

        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            return;
        }

        $query->the_post();
        $product_id = get_the_ID();
        wp_reset_postdata();

        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $product->set_name($new_data['name']);
        $product->set_price($new_data['price']);
        $product->set_regular_price($new_data['price']);
        $product->set_short_description($new_data['desc']);
        $product->save();
    }
}

new Avance_Mentoria_Updater();
