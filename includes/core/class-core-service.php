<?php
/**
 * Avance Core Service
 * Gestiona: Setup, Assets, Activation, Productos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

class Avance_Core_Service {

    public function __construct() {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('widgets_init', [$this, 'register_widgets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_loaded', [$this, 'create_tables']);
        register_activation_hook(WP_CONTENT_DIR . '/themes/avance-template/functions.php', [$this, 'on_activate']);
    }

    /**
     * Setup inicial (theme features)
     */
    public function setup() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('custom-logo');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
        register_nav_menus(['primary' => esc_html__('Primary Menu', 'avance-template')]);
    }

    /**
     * Register widgets
     */
    public function register_widgets() {
        register_sidebar([
            'name'          => esc_html__('Footer Widget Area', 'avance-template'),
            'id'            => 'footer-1',
            'description'   => esc_html__('Footer widget area', 'avance-template'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ]);
    }

    /**
     * Enqueue assets - SOLO necesario
     */
    public function enqueue_assets() {
        $theme_uri = get_template_directory_uri();
        $version = AVANCE_THEME_VERSION;

        // Base styles
        wp_enqueue_style('avance-base', $theme_uri . '/assets/css/base.css', [], $version);
        wp_enqueue_style('avance-pages', $theme_uri . '/assets/css/pages.css', ['avance-base'], $version);

        // Page-specific SOLO si es necesario
        if (is_page_template('templates/page-mentoria.php')) {
            wp_enqueue_style('avance-page-mentoria', $theme_uri . '/assets/css/page-mentoria.css', ['avance-base'], $version);
            wp_enqueue_script('avance-faq-accordion', $theme_uri . '/assets/js/faq-accordion.js', [], $version, true);
        }

        if (is_page_template('templates/page-diagnostico.php')) {
            wp_enqueue_style('avance-page-diagnostico', $theme_uri . '/assets/css/page-diagnostico.css', ['avance-base'], $version);
            wp_enqueue_script('avance-diagnostico-quiz', $theme_uri . '/assets/js/diagnostico-quiz.js', [], $version, true);
        }

        if (is_page_template('templates/page-contacto.php')) {
            wp_enqueue_style('avance-page-contacto', $theme_uri . '/assets/css/page-contacto.css', ['avance-base'], $version);
            wp_enqueue_style('avance-sessiones', $theme_uri . '/assets/css/sessiones.css', ['avance-base'], $version);
        }

        if (is_page_template('templates/page-servicio-empresa.php')) {
            wp_enqueue_style('avance-page-servicio-empresa', $theme_uri . '/assets/css/page-servicio-empresa.css', ['avance-base'], $version);
        }

        if (is_page_template('templates/page-sobremi.php')) {
            wp_enqueue_style('avance-page-sobremi', $theme_uri . '/assets/css/page-sobremi.css', ['avance-base'], $version);
        }

        if (is_page_template('templates/page-libro.php')) {
            wp_enqueue_style('avance-page-libro', $theme_uri . '/assets/css/page-libro.css', ['avance-base'], $version);
        }

        // Scripts globales (realmente necesarios)
        wp_enqueue_script('avance-animations', $theme_uri . '/assets/js/animations.js', [], $version, true);
        wp_enqueue_script('avance-mobile-menu', $theme_uri . '/assets/js/mobile-menu.js', [], $version, true);

        // Scheduling scripts
        wp_enqueue_script('avance-calendar-picker', $theme_uri . '/assets/js/calendar.js', [], $version, true);
        wp_enqueue_script('avance-appointment-handler', $theme_uri . '/assets/js/appointment-handler.js', [], $version, true);
        wp_localize_script('avance-appointment-handler', 'avanceAppointmentConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_appointment_form'),
        ]);

        // Contact form scripts
        wp_enqueue_script('avance-contact-whatsapp', $theme_uri . '/assets/js/contact-whatsapp.js', [], $version, true);
        wp_localize_script('avance-contact-whatsapp', 'avanceFormConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_contact_form'),
        ]);

        // Agenda scripts
        wp_enqueue_script('avance-contacto-agenda', $theme_uri . '/assets/js/contacto-agenda.js', [], $version, true);
        wp_localize_script('avance-contacto-agenda', 'avanceAgendamientoConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_agendamiento_form'),
        ]);

        // Proposal form
        wp_enqueue_script('avance-proposal-form', $theme_uri . '/assets/js/proposal-form.js', [], $version, true);
        wp_localize_script('avance-proposal-form', 'avanceProposalConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_proposal_form'),
        ]);

        wp_enqueue_style('wp-block-library');
    }

    /**
     * Crear tablas (una sola vez)
     */
    public function create_tables() {
        if (class_exists('Avance_Contact_DB')) {
            Avance_Contact_DB::create_table();
        }
        if (class_exists('Avance_Agendamiento_Sesiones_DB')) {
            Avance_Agendamiento_Sesiones_DB::create_table();
        }
        if (class_exists('Avance_Proposal_DB')) {
            Avance_Proposal_DB::create_table();
        }
    }

    /**
     * Activation hook - tasks one-time
     */
    public function on_activate() {
        $this->create_pages_once();
        $this->create_products_once();
    }

    /**
     * Crear páginas (SOLO primera vez)
     */
    private function create_pages_once() {
        $pages = [
            'mentorias' => 'Mentorías',
            'servicio-empresa' => 'Servicios Empresa',
            'diagnostico' => 'Diagnóstico',
            'sobre-mi' => 'Sobre Mí',
            'mi-libro' => 'Libro',
            'contacto' => 'Contacto',
        ];

        foreach ($pages as $slug => $title) {
            if (!get_page_by_path($slug)) {
                wp_insert_post([
                    'post_type' => 'page',
                    'post_title' => $title,
                    'post_name' => $slug,
                    'post_status' => 'publish',
                ]);
            }
        }

        set_transient('avance_pages_created', true, HOUR_IN_SECONDS);
    }

    /**
     * Crear productos WooCommerce (SOLO primera vez)
     */
    private function create_products_once() {
        if (!class_exists('WooCommerce') || get_option('avance_mentoria_products_created')) {
            return;
        }

        $products = [
            ['name' => 'Mentoría Básica', 'price' => '99', 'desc' => 'Sesiones focalizadas en tu desafío principal.'],
            ['name' => 'Mentoría Premium', 'price' => '199', 'desc' => 'Acompañamiento completo con acceso WhatsApp.'],
            ['name' => 'Mentoría VIP', 'price' => '399', 'desc' => 'Plan anual con sesiones semanales.'],
        ];

        foreach ($products as $p) {
            $product = new WC_Product_Simple();
            $product->set_name($p['name']);
            $product->set_price($p['price']);
            $product->set_regular_price($p['price']);
            $product->set_short_description($p['desc']);
            $product->set_status('publish');
            $product->save();
            wp_set_post_terms($product->get_id(), 'mentoria', 'product_tag', true);
        }

        update_option('avance_mentoria_products_created', true);
    }
}
