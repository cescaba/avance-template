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
        add_action('wp_loaded', [$this, 'load_handlers']);
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
        wp_enqueue_style('avance-calendar-agenda', $theme_uri . '/assets/css/calendar-agenda.css', ['avance-pages'], $version);
        wp_enqueue_style('avance-sessiones', $theme_uri . '/assets/css/sessiones.css', ['avance-pages'], $version);

        // Page-specific SOLO si es necesario
        if (is_page_template('templates/page-mentoria.php')) {
            wp_enqueue_style('avance-page-mentoria', $theme_uri . '/assets/css/page-mentoria.css', ['avance-pages'], $version);
            wp_enqueue_script('avance-mentoria-reserva', $theme_uri . '/assets/js/mentoria-reserva.js', [], $version, true);
            wp_enqueue_script('avance-calendar-agenda', $theme_uri . '/assets/js/calendar-agenda.js', [], $version, true);
            wp_localize_script('avance-mentoria-reserva', 'mentoriaConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('avance_mentoria_booking'),
            ]);
        }

        if (is_page_template('templates/page-diagnostico.php')) {
            wp_enqueue_style('avance-page-diagnostico', $theme_uri . '/assets/css/page-diagnostico.css', ['avance-base'], $version);
            wp_enqueue_script('avance-diagnostico-quiz', $theme_uri . '/assets/js/diagnostico-quiz.js', [], $version, true);
        }

        if (is_front_page() || is_page_template('templates/page-inicio.php')) {
            wp_enqueue_script('avance-calendar-agenda', $theme_uri . '/assets/js/calendar-agenda.js', [], $version, true);
            wp_enqueue_script('avance-scheduling-section', $theme_uri . '/assets/js/scheduling-section.js', [], $version, true);
            wp_localize_script('avance-scheduling-section', 'avanceAgendamientoContactoConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('avance_agendamiento_contacto'),
            ]);
        }

        if (is_page_template('templates/page-contacto.php')) {
            wp_enqueue_style('avance-page-contacto', $theme_uri . '/assets/css/page-contacto.css', ['avance-base'], $version);
            wp_enqueue_script('avance-calendar-agenda', $theme_uri . '/assets/js/calendar-agenda.js', [], $version, true);
            wp_enqueue_script('avance-scheduling-section', $theme_uri . '/assets/js/scheduling-section.js', [], $version, true);
            wp_localize_script('avance-scheduling-section', 'avanceAgendamientoContactoConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('avance_agendamiento_contacto'),
            ]);
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

        // WooCommerce Checkout personalizado
        if (is_checkout()) {
            wp_enqueue_style('avance-woocommerce-checkout', $theme_uri . '/assets/css/woocommerce-checkout.css', ['woocommerce-general'], $version);
        }

        // Scripts globales (realmente necesarios)
        wp_enqueue_script('avance-animations', $theme_uri . '/assets/js/animations.js', [], $version, true);
        wp_enqueue_script('avance-ui-components', $theme_uri . '/assets/js/ui-components.js', [], $version, true);

        // Modal PDF Download
        wp_enqueue_style('avance-modal-pdf', $theme_uri . '/assets/css/modal-pdf.css', ['avance-base'], $version);
        wp_enqueue_script('avance-modal-pdf', $theme_uri . '/assets/js/modal-pdf.js', [], $version, true);

        // Forms system (all forms consolidated)
        wp_enqueue_script('avance-forms', $theme_uri . '/assets/js/forms.js', [], $version, true);
        wp_localize_script('avance-forms', 'avanceProposalConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_proposal_form'),
        ]);
        wp_localize_script('avance-forms', 'avanceFormConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_contact_form'),
        ]);
        wp_localize_script('avance-forms', 'avanceAppointmentConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_appointment_form'),
        ]);
        wp_localize_script('avance-forms', 'avanceDiagnosticoConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('avance_diagnostico_form'),
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
        if (class_exists('Avance_Proposal_DB')) {
            Avance_Proposal_DB::create_table();
        }
        if (class_exists('Avance_Agendamiento_Contacto_DB')) {
            Avance_Agendamiento_Contacto_DB::create_table();
        }
    }

    /**
     * Cargar handlers
     */
    public function load_handlers() {
        require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';
        require_once get_template_directory() . '/includes/database/agendamientos-sesiones/handler-agendamiento-contacto.php';

        if (is_admin()) {
            require_once get_template_directory() . '/includes/admin/class-admin-agendamiento-contacto.php';
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
