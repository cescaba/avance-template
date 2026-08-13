<?php
/**
 * Avance Template - Functions
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

// Incluir configuración del tema
require_once get_template_directory() . '/config/theme-config.php';

// Incluir clases de CONTACTOS
require_once get_template_directory() . '/includes/class-contact-validator.php';
require_once get_template_directory() . '/includes/class-contact-db.php';
require_once get_template_directory() . '/includes/class-contact-form-handler.php';
require_once get_template_directory() . '/includes/class-contact-admin.php';

// Incluir orquestador de AGENDAMIENTO
require_once get_template_directory() . '/includes/appointments/class-appointments-manager.php';

// Incluir orquestador de DIAGNÓSTICO
require_once get_template_directory() . '/includes/class-diagnostico-manager.php';

// Incluir clases de AGENDAMIENTO
require_once get_template_directory() . '/includes/class-agendamiento-db.php';
require_once get_template_directory() . '/includes/class-agendamiento-handler.php';

// Incluir COMPONENTES REUTILIZABLES
require_once get_template_directory() . '/includes/class-scheduling-component.php';
require_once get_template_directory() . '/includes/class-form-component.php';


/**
 * CREAR PRODUCTOS DE MENTORÍA AUTOMÁTICAMENTE
 */
add_action('init', function() {
	if (!class_exists('WooCommerce')) {
		return;
	}

	if (get_option('avance_mentoria_products_created')) {
		return;
	}

	$mentoria_products = array(
		array(
			'name' => 'Mentoría Básica',
			'price' => '99',
			'description' => 'Sesiones focalizadas en tu desafío principal.',
			'short_description' => 'Sesiones 1:1 enfocadas en tu desafío',
		),
		array(
			'name' => 'Mentoría Premium',
			'price' => '199',
			'description' => 'Acompañamiento completo con acceso WhatsApp.',
			'short_description' => 'Acompañamiento + acceso WhatsApp',
		),
		array(
			'name' => 'Mentoría VIP',
			'price' => '399',
			'description' => 'Plan anual con sesiones semanales.',
			'short_description' => 'Plan anual + sesiones semanales',
		),
	);

	foreach ($mentoria_products as $product_data) {
		$product = new WC_Product_Simple();
		$product->set_name($product_data['name']);
		$product->set_price($product_data['price']);
		$product->set_regular_price($product_data['price']);
		$product->set_description($product_data['description']);
		$product->set_short_description($product_data['short_description']);
		$product->set_status('publish');
		$product->save();

		wp_set_post_terms($product->get_id(), 'mentoria', 'product_tag', true);
	}

	update_option('avance_mentoria_products_created', true);
}, 999);

/**
 * DESPUÉS de verificar que están creados en WordPress Admin → Productos,
 * ELIMINA TODO ESTE BLOQUE de functions.php (desde CREAR PRODUCTOS... hasta aquí)
 */

/**
 * Crear tabla de contactos y agendamientos al cargar WordPress
 */
add_action('wp_loaded', function() {
	if (class_exists('Avance_Contact_DB')) {
		Avance_Contact_DB::create_table();
	}
	if (class_exists('Avance_Agendamiento_DB')) {
		Avance_Agendamiento_DB::create_table();
	}
});

/**
 * Enqueue styles and scripts
 */
function avance_template_enqueue_assets() {
	$theme_uri = get_template_directory_uri();

	// CSS
	wp_enqueue_style(
		'avance-base',
		$theme_uri . '/assets/css/base.css',
		array(),
		wp_get_theme()->get('Version'),
		'all'
	);

	wp_enqueue_style(
		'avance-pages',
		$theme_uri . '/assets/css/pages.css',
		array('avance-base'),
		wp_get_theme()->get('Version'),
		'all'
	);

	// Page-specific styles and scripts - Mentorías
	if (is_page_template('templates/page-mentoria.php')) {
		wp_enqueue_style(
			'avance-page-mentoria',
			$theme_uri . '/assets/css/page-mentoria.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);

		wp_enqueue_script(
			'avance-faq-accordion',
			$theme_uri . '/assets/js/faq-accordion.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);
	}

	// Page-specific styles - Servicios Empresa
	if (is_page_template('templates/page-servicio-empresa.php')) {
		wp_enqueue_style(
			'avance-page-servicio-empresa',
			$theme_uri . '/assets/css/page-servicio-empresa.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);
	}

	// Page-specific styles - Diagnóstico
	if (is_page_template('templates/page-diagnostico.php')) {
		wp_enqueue_style(
			'avance-page-diagnostico',
			$theme_uri . '/assets/css/page-diagnostico.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);

		wp_enqueue_script(
			'avance-diagnostico-quiz',
			$theme_uri . '/assets/js/diagnostico-quiz.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);
	}

	// Page-specific styles - Sobre Mí
	if (is_page_template('templates/page-sobremi.php')) {
		wp_enqueue_style(
			'avance-page-sobremi',
			$theme_uri . '/assets/css/page-sobremi.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);
	}

	// Page-specific styles - Libro
	if (is_page_template('templates/page-libro.php')) {
		wp_enqueue_style(
			'avance-page-libro',
			$theme_uri . '/assets/css/page-libro.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);
	}

	// Page-specific styles - Contacto
	if (is_page_template('templates/page-contacto.php')) {
		wp_enqueue_style(
			'avance-page-contacto',
			$theme_uri . '/assets/css/page-contacto.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);
	}

	// Agendamiento (Global - se verifica en JS si existe el formulario)
	wp_enqueue_style(
		'avance-appointment-calendar',
		$theme_uri . '/assets/css/calendar.css',
		array('avance-base'),
		wp_get_theme()->get('Version'),
		'all'
	);

	wp_enqueue_script(
		'avance-calendar-picker',
		$theme_uri . '/assets/js/calendar.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);

	wp_enqueue_script(
		'avance-appointment-handler',
		$theme_uri . '/assets/js/appointment-handler.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);

	// Localizar datos para el agendamiento
	wp_localize_script(
		'avance-appointment-handler',
		'avanceAppointmentConfig',
		array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('avance_appointment_form'),
		)
	);

	// Contact Form Script (Enqueue en todas las páginas - el script verifica si el formulario existe)
	wp_enqueue_script(
		'avance-contact-whatsapp',
		$theme_uri . '/assets/js/contact-whatsapp.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);

	// Localizar datos para el formulario de contacto
	wp_localize_script(
		'avance-contact-whatsapp',
		'avanceFormConfig',
		array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('avance_contact_form'),
		)
	);

	// Contacto Agenda Script (CSS is in pages.css)
	wp_enqueue_script(
		'avance-contacto-agenda',
		$theme_uri . '/assets/js/contacto-agenda.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);

	// Localizar datos para el agendamiento
	wp_localize_script(
		'avance-contacto-agenda',
		'avanceAgendamientoConfig',
		array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('avance_agendamiento_form'),
		)
	);

	// WordPress styles
	wp_enqueue_style('wp-block-library');

	// JavaScript - Animations (Scroll + Counter)
	wp_enqueue_script(
		'avance-animations',
		$theme_uri . '/assets/js/animations.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);

	// JavaScript - Mobile Menu
	wp_enqueue_script(
		'avance-mobile-menu',
		$theme_uri . '/assets/js/mobile-menu.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);
}
add_action('wp_enqueue_scripts', 'avance_template_enqueue_assets');

/**
 * Setup theme features
 */
function avance_template_setup() {
	// Add theme support
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-logo');
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));

	// Register navigation menus
	register_nav_menus(array(
		'primary' => esc_html__('Primary Menu', 'avance-template'),
	));
}
add_action('after_setup_theme', 'avance_template_setup');

/**
 * Register widgets
 */
function avance_template_widgets_init() {
	register_sidebar(array(
		'name'          => esc_html__('Footer Widget Area', 'avance-template'),
		'id'            => 'footer-1',
		'description'   => esc_html__('Footer widget area', 'avance-template'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'avance_template_widgets_init');

/**
 * Crear páginas requeridas automáticamente
 */
function avance_template_create_pages() {
	$pages = array(
		array(
			'post_title'   => 'Mentorías',
			'post_name'    => 'mentorias',
			'post_content' => '',
			'page_template' => 'templates/page-mentoria.php',
		),
		array(
			'post_title'   => 'Servicios Empresa',
			'post_name'    => 'servicio-empresa',
			'post_content' => '',
			'page_template' => 'templates/page-servicio-empresa.php',
		),
		array(
			'post_title'   => 'Diagnóstico',
			'post_name'    => 'diagnostico',
			'post_content' => '',
			'page_template' => 'templates/page-diagnostico.php',
		),
		array(
			'post_title'   => 'Sobre Mí',
			'post_name'    => 'sobre-mi',
			'post_content' => '',
			'page_template' => 'templates/page-sobremi.php',
		),
		array(
			'post_title'   => 'Libro',
			'post_name'    => 'mi-libro',
			'post_content' => '',
			'page_template' => 'templates/page-libro.php',
		),
		array(
			'post_title'   => 'Contacto',
			'post_name'    => 'contacto',
			'post_content' => '',
			'page_template' => 'templates/page-contacto.php',
		),
	);

	foreach ($pages as $page) {
		$existing = get_page_by_path($page['post_name']);

		if (!$existing) {
			$page_id = wp_insert_post(array(
				'post_type'   => 'page',
				'post_title'  => $page['post_title'],
				'post_name'   => $page['post_name'],
				'post_content' => $page['post_content'],
				'post_status' => 'publish',
			));

			if ($page_id && isset($page['page_template'])) {
				update_post_meta($page_id, '_wp_page_template', $page['page_template']);
			}
		}
	}
}
add_action('after_setup_theme', 'avance_template_create_pages');

/**
 * Procesar agendamiento via AJAX
 */
add_action('wp_ajax_avance_agendamiento_submit', 'avance_handle_agendamiento_submit');
add_action('wp_ajax_nopriv_avance_agendamiento_submit', 'avance_handle_agendamiento_submit');

function avance_handle_agendamiento_submit() {
	check_ajax_referer('avance_agendamiento_form', 'nonce');

	$data = array(
		'nombre' => $_POST['nombre'] ?? '',
		'numero' => $_POST['numero'] ?? '',
		'tema' => $_POST['tema'] ?? '',
		'fecha' => $_POST['fecha'] ?? '',
	);

	// Validar datos
	$validation = Avance_Agendamiento_Handler::validate($data);

	if (!$validation['success']) {
		wp_send_json_error(array(
			'message' => implode(', ', $validation['errors']),
		));
	}

	// Procesar agendamiento
	$result = Avance_Agendamiento_Handler::process($validation['data']);

	if (!$result['success']) {
		wp_send_json_error(array(
			'message' => $result['message'],
		));
	}

	// Formatear datos para WhatsApp
	$fecha_formato = date('d/m/Y', strtotime($validation['data']['fecha_agendada']));
	$mensaje = sprintf(
		"Hola, quiero agendar una sesión:\n\nNombre: %s\nTeléfono: %s\nTema: %s\nFecha: %s",
		$validation['data']['nombre'],
		$validation['data']['numero'],
		$validation['data']['tema'],
		$fecha_formato
	);

	$whatsapp_number = '51936975214';
	$whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($mensaje);

	wp_send_json_success(array(
		'message' => $result['message'],
		'id' => $result['id'],
		'whatsapp_url' => $whatsapp_url,
		'fecha_formateada' => $fecha_formato,
	));
}
