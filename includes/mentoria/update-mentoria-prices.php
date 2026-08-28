<?php
/**
 * Script para actualizar precios de los productos de mentoría en WooCommerce
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

class Avance_Update_Mentoria_Prices {

    public static function update_prices() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        $price_updates = [
            'Entrada' => 800,      // 4 sesiones
            'Pro' => 1200,          // 8 sesiones
            'Sesión puntual' => 250 // 1 sesión
        ];

        foreach ($price_updates as $product_name => $new_price) {
            self::update_product_price($product_name, $new_price);
        }
    }

    private static function update_product_price($product_name, $new_price) {
        $args = [
            'post_type' => 'product',
            'title' => $product_name,
            'posts_per_page' => 1,
        ];

        $products = get_posts($args);

        if (!empty($products)) {
            $product_id = $products[0]->ID;
            $product = wc_get_product($product_id);

            if ($product) {
                $product->set_regular_price($new_price);
                $product->save();
            }
        }
    }
}

// Ejecutar actualización
add_action('wp_loaded', [Avance_Update_Mentoria_Prices::class, 'update_prices']);
