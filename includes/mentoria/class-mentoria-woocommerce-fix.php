<?php
/**
 * Mentoria WooCommerce Fix
 * Arregla problemas de checkout y coming soon
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

class Avance_Mentoria_WooCommerce_Fix {

    public function __construct() {
        add_action('wp_loaded', [$this, 'fix_woocommerce_setup']);
    }

    /**
     * Arreglar setup de WooCommerce
     */
    public function fix_woocommerce_setup() {
        if (!class_exists('WooCommerce')) {
            return;
        }

        // 1. Desactivar modo coming soon
        $this->disable_coming_soon();

        // 2. Crear/verificar páginas de WooCommerce (siempre verificar)
        $this->ensure_woocommerce_pages();

        // 3. Verificar permalinks (solo una vez)
        if (!get_option('avance_woocommerce_permalinks_fixed')) {
            $this->check_permalinks();
            update_option('avance_woocommerce_permalinks_fixed', true);
        }
    }

    /**
     * Desactivar modo coming soon
     */
    private function disable_coming_soon() {
        // Desactivar opciones de coming soon comunes
        update_option('woocommerce_coming_soon', 'no');
        update_option('woocommerce_store_notice', '');
        update_option('blog_public', 1);
    }

    /**
     * Asegurar que existan las páginas de WooCommerce
     */
    private function ensure_woocommerce_pages() {
        $pages = [
            'shop'       => 'Tienda',
            'cart'       => 'Carrito',
            'checkout'   => 'Finalizar compra',
            'myaccount'  => 'Mi Cuenta',
        ];

        foreach ($pages as $page_type => $page_title) {
            $page_id = wc_get_page_id($page_type);

            // Si la página no existe, crearla
            if ($page_id < 1) {
                $this->create_page($page_type, $page_title);
            } else {
                // Si existe, verificar que tenga el shortcode correcto
                $this->verify_page_content($page_id, $page_type);
            }
        }
    }

    /**
     * Verificar que la página tenga el contenido correcto
     */
    private function verify_page_content($page_id, $page_type) {
        $page = get_post($page_id);
        if (!$page) {
            return;
        }

        $shortcode = '[woocommerce_' . $page_type . ']';
        $has_shortcode = strpos($page->post_content, $shortcode) !== false;

        // Si no tiene el shortcode, agregarlo
        if (!$has_shortcode) {
            wp_update_post([
                'ID'           => $page_id,
                'post_content' => $shortcode,
            ]);
        }
    }

    /**
     * Crear página de WooCommerce
     */
    private function create_page($page_type, $page_title) {
        $page = [
            'post_title'   => $page_title,
            'post_content' => '[woocommerce_' . $page_type . ']',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ];

        $page_id = wp_insert_post($page);

        if ($page_id && !is_wp_error($page_id)) {
            update_option('woocommerce_' . $page_type . '_page_id', $page_id);
        }
    }

    /**
     * Verificar permalinks
     */
    private function check_permalinks() {
        $permalink_structure = get_option('permalink_structure');

        // Si está vacío o es /?p= (plain), cambiar a postname
        if (empty($permalink_structure) || $permalink_structure === '/?p=%post_id%/') {
            update_option('permalink_structure', '/%postname%/');
            flush_rewrite_rules();
        }
    }
}

new Avance_Mentoria_WooCommerce_Fix();
