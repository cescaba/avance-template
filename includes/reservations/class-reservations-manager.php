<?php
/**
 * Reservations Manager - Orquestador Principal
 *
 * Gestiona la inicialización del sistema de reservas con WooCommerce
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Reservations_Manager {

	/**
	 * Inicializar sistema de reservas
	 */
	public static function init() {
		// Verificar que WooCommerce esté activo
		if (!self::is_woocommerce_active()) {
			return;
		}

		// Cargar clases
		self::load_classes();

		// Registrar AJAX handlers
		self::register_ajax_handlers();

		// Enqueue scripts y estilos
		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
	}

	/**
	 * Verificar si WooCommerce está activo
	 */
	private static function is_woocommerce_active() {
		return class_exists('WooCommerce');
	}

	/**
	 * Cargar clases del sistema
	 */
	private static function load_classes() {
		$reservations_dir = get_template_directory() . '/includes/reservations/';

		require_once $reservations_dir . 'class-reservation-validator.php';
		require_once $reservations_dir . 'class-reservation-cart-handler.php';
	}

	/**
	 * Registrar AJAX handlers
	 */
	private static function register_ajax_handlers() {
		if (class_exists('Avance_Reservation_Cart_Handler')) {
			new Avance_Reservation_Cart_Handler();
		}
	}

	/**
	 * Enqueue de scripts y estilos
	 */
	public static function enqueue_assets() {
		$theme_uri = get_template_directory_uri();

		// CSS - Widget
		wp_enqueue_style(
			'avance-reservations-widget',
			$theme_uri . '/assets/css/reservations-widget.css',
			array(),
			wp_get_theme()->get('Version'),
			'all'
		);

		// CSS - Calendar (si existe)
		wp_enqueue_style(
			'avance-reservations-calendar',
			$theme_uri . '/assets/css/reservations.css',
			array(),
			wp_get_theme()->get('Version'),
			'all'
		);

		// JS
		wp_enqueue_script(
			'avance-reservation-handler',
			$theme_uri . '/assets/js/reservation-handler.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);

		// Localizar datos
		wp_localize_script(
			'avance-reservation-handler',
			'avanceReservationConfig',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('avance_reservation_form'),
				'checkoutUrl' => wc_get_checkout_url(),
			)
		);
	}

	/**
	 * Obtener productos de reserva configurados
	 *
	 * Busca productos con etiqueta "mentoria" o los primeros 3 productos
	 */
	public static function get_reservation_products() {
		// Opción 1: Buscar productos con etiqueta "mentoria"
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => 3,
			'orderby' => 'meta_value_num',
			'meta_key' => '_price',
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_tag',
					'field' => 'slug',
					'terms' => 'mentoria',
					'operator' => 'IN',
				),
			),
		);

		$query = new WP_Query($args);

		// Si no encuentra con etiqueta, busca los primeros 3 productos
		if ($query->post_count === 0) {
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => 3,
				'orderby' => 'meta_value_num',
				'meta_key' => '_price',
				'order' => 'ASC',
				'post_status' => 'publish',
			);
			$query = new WP_Query($args);
		}

		$products = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$product = wc_get_product(get_the_ID());

				if ($product && $product->is_purchasable()) {
					$products[] = array(
						'id' => $product->get_id(),
						'title' => $product->get_name(),
						'price' => $product->get_price(),
						'description' => $product->get_short_description(),
						'image' => wp_get_attachment_url($product->get_image_id()),
					);
				}
			}
		}

		wp_reset_postdata();

		return $products;
	}
}

// Inicializar al cargar WordPress
add_action('wp_loaded', array('Avance_Reservations_Manager', 'init'));
