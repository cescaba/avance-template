<?php
/**
 * Avance AJAX Handler
 * Centraliza TODOS los AJAX: Sanitización, validación y respuesta
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
    exit;
}

class Avance_Ajax_Handler {

    public function __construct() {
        // Propuestas
        add_action('wp_ajax_avance_proposal_submit', [$this, 'handle_proposal']);
        add_action('wp_ajax_nopriv_avance_proposal_submit', [$this, 'handle_proposal']);

        // Mentoría Checkout
        add_action('wp_ajax_avance_save_mentoria_checkout_data', [$this, 'handle_mentoria_checkout']);
        add_action('wp_ajax_nopriv_avance_save_mentoria_checkout_data', [$this, 'handle_mentoria_checkout']);
    }

    /**
     * Handle Propuestas
     */
    public function handle_proposal() {
        check_ajax_referer('avance_proposal_form', 'nonce');

        // SANITIZAR
        $data = [
            'nombre'            => sanitize_text_field($_POST['nombre'] ?? ''),
            'cargo'             => sanitize_text_field($_POST['cargo'] ?? ''),
            'empresa'           => sanitize_text_field($_POST['empresa'] ?? ''),
            'tamaño_equipo'     => sanitize_text_field($_POST['tamaño_equipo'] ?? ''),
            'email'             => sanitize_email($_POST['email'] ?? ''),
            'whatsapp'          => sanitize_text_field($_POST['whatsapp'] ?? ''),
            'servicio_interes'  => sanitize_text_field($_POST['servicio_interes'] ?? ''),
            'desafio_comercial' => sanitize_textarea_field($_POST['desafio_comercial'] ?? ''),
        ];

        // Validar
        $validation = Avance_Proposal_Handler::validate($data);
        if (!$validation['success']) {
            wp_send_json_error(['message' => implode(', ', $validation['errors'])]);
        }

        // Procesar
        $result = Avance_Proposal_Handler::process($validation['data']);
        if (!$result['success']) {
            wp_send_json_error(['message' => $result['message']]);
        }

        // WhatsApp
        $mensaje = sprintf(
            "Hola, tengo interés en una propuesta:\n\nNombre: %s\nCargo: %s\nEmpresa: %s\nEquipo: %s\nEmail: %s\nWA: %s\nServicio: %s\nDesafío: %s",
            $validation['data']['nombre'],
            $validation['data']['cargo'] ?: 'No esp.',
            $validation['data']['empresa'],
            $validation['data']['tamaño_equipo'] ?: 'No esp.',
            $validation['data']['email'],
            $validation['data']['whatsapp'] ?: 'No esp.',
            $validation['data']['servicio_interes'],
            $validation['data']['desafio_comercial']
        );

        // Normalizar número para WhatsApp (agregar +51 si falta)
        $phone = preg_replace('/[^0-9]/', '', AVANCE_WHATSAPP);
        if (strlen($phone) === 9) {
            $phone = '51' . $phone;
        }
        $wa_url = 'https://wa.me/' . $phone . '?text=' . urlencode($mensaje);

        wp_send_json_success([
            'id' => $result['id'],
            'whatsapp_url' => $wa_url,
        ]);
    }

    /**
     * Handle Mentoria Checkout
     * 1. Guarda fecha/hora en tabla de mentoría
     * 2. Crea orden en WooCommerce
     * Seguridad: Rate limiting + Spam detection
     */
    public function handle_mentoria_checkout() {
        check_ajax_referer('avance_mentoria_booking', 'nonce');

        // RATE LIMITING (5/hora por IP)
        $ip = $this->get_client_ip();
        if (!$this->check_rate_limit($ip)) {
            wp_send_json_error(['message' => 'Demasiados intentos. Espera un momento.'], 429);
        }

        // SANITIZAR
        $data = [
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'whatsapp' => sanitize_text_field($_POST['whatsapp'] ?? ''),
            'plan' => sanitize_text_field($_POST['plan'] ?? ''),
            'fecha' => sanitize_text_field($_POST['fecha'] ?? ''),
            'hora' => sanitize_text_field($_POST['hora'] ?? ''),
            'desafio' => sanitize_textarea_field($_POST['desafio'] ?? ''),
        ];

        // VALIDAR CAMPOS REQUERIDOS
        if (empty($data['first_name'])) {
            wp_send_json_error(['message' => 'El nombre es requerido']);
        }
        if (strlen($data['first_name']) < 3) {
            wp_send_json_error(['message' => 'El nombre debe tener mínimo 3 caracteres']);
        }

        if (empty($data['last_name'])) {
            wp_send_json_error(['message' => 'El apellido es requerido']);
        }
        if (strlen($data['last_name']) < 3) {
            wp_send_json_error(['message' => 'El apellido debe tener mínimo 3 caracteres']);
        }

        if (empty($data['email']) || !is_email($data['email'])) {
            wp_send_json_error(['message' => 'El email no es válido']);
        }

        // VALIDAR WHATSAPP (9-15 dígitos)
        if (empty($data['whatsapp'])) {
            wp_send_json_error(['message' => 'El WhatsApp es requerido']);
        }
        $whatsapp_clean = preg_replace('/[^0-9+]/', '', $data['whatsapp']);
        $digits_only = preg_replace('/[^0-9]/', '', $whatsapp_clean);
        if (strlen($digits_only) < 9 || strlen($digits_only) > 15) {
            wp_send_json_error(['message' => 'WhatsApp debe tener entre 9 y 15 dígitos']);
        }

        if (empty($data['plan'])) {
            wp_send_json_error(['message' => 'El plan es requerido']);
        }
        if (empty($data['fecha'])) {
            wp_send_json_error(['message' => 'La fecha es requerida']);
        }
        if (empty($data['hora'])) {
            wp_send_json_error(['message' => 'La hora es requerida']);
        }
        if (empty($data['desafio'])) {
            wp_send_json_error(['message' => 'El desafío es requerido']);
        }

        // SPAM DETECTION en desafio
        if (class_exists('Avance_Spam_Detector')) {
            $spam_result = Avance_Spam_Detector::analyze_message(
                $data['desafio'],
                $data['first_name'],
                $data['email']
            );
            if ($spam_result['is_spam']) {
                wp_send_json_error(['message' => 'Tu mensaje fue identificado como spam'], 400);
            }
        }

        // GUARDAR en tabla de mentoría PRIMERO
        $fecha_db = $this->parse_fecha_db($data['fecha']);
        if (!$fecha_db) {
            wp_send_json_error(['message' => 'Formato de fecha inválido']);
        }

        if (class_exists('Avance_Calendario_Reservas_Mentoria_DB')) {
            $hora_disponible = Avance_Calendario_Reservas_Mentoria_DB::is_hora_disponible($fecha_db, $data['hora']);
            if (!$hora_disponible) {
                wp_send_json_error(['message' => 'Esta hora ya no está disponible'], 409);
            }

            $insert_result = Avance_Calendario_Reservas_Mentoria_DB::insert($fecha_db, $data['hora']);
            if (!$insert_result) {
                wp_send_json_error(['message' => 'Error al guardar la reserva en calendario']);
            }
        }

        // CREAR ORDEN (Flujo WooCommerce)
        $result = Avance_Mentoria_Checkout::create_order($data);

        if (!$result['success']) {
            wp_send_json_error(['message' => $result['message']]);
        }

        wp_send_json_success([
            'order_id' => $result['order_id'],
            'redirect' => $result['checkout_url'],
        ]);
    }

    private function parse_fecha_db($fecha_str) {
        $parts = explode('/', $fecha_str);
        if (count($parts) !== 3) {
            return false;
        }
        $day = intval($parts[0]);
        $month = intval($parts[1]);
        $year = intval($parts[2]);

        if (!checkdate($month, $day, $year)) {
            return false;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    private function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        return sanitize_text_field($ip);
    }

    private function check_rate_limit($ip) {
        global $wpdb;
        $table = $wpdb->prefix . 'avance_form_attempts';

        // Verificar intentos en la última hora
        $attempts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE ip_address = %s AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            $ip
        ));

        // Máximo 5 intentos por hora
        if ($attempts >= 5) {
            return false;
        }

        // Registrar intento
        $wpdb->insert($table, [
            'ip_address' => $ip,
            'created_at' => current_time('mysql'),
            'last_attempt' => current_time('mysql'),
        ], ['%s', '%s', '%s']);

        return true;
    }
}
