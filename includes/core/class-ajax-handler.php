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
        // Agendamiento
        add_action('wp_ajax_avance_agendamiento_submit', [$this, 'handle_agendamiento']);
        add_action('wp_ajax_nopriv_avance_agendamiento_submit', [$this, 'handle_agendamiento']);

        // Propuestas
        add_action('wp_ajax_avance_proposal_submit', [$this, 'handle_proposal']);
        add_action('wp_ajax_nopriv_avance_proposal_submit', [$this, 'handle_proposal']);
    }

    /**
     * Handle Agendamiento - Sanitizado, validado, correcto
     */
    public function handle_agendamiento() {
        check_ajax_referer('avance_agendamiento_form', 'nonce');

        // SANITIZAR primero (defense-in-depth)
        $data = [
            'nombre'   => sanitize_text_field($_POST['nombre'] ?? ''),
            'numero'   => sanitize_text_field($_POST['numero'] ?? ''),
            'tema'     => sanitize_text_field($_POST['tema'] ?? ''),
            'fecha'    => sanitize_text_field($_POST['fecha'] ?? ''),
        ];

        // Validar
        $validation = Avance_Agendamiento_Sesiones_Handler::validate($data);
        if (!$validation['success']) {
            wp_send_json_error(['message' => implode(', ', $validation['errors'])]);
        }

        // Procesar
        $result = Avance_Agendamiento_Sesiones_Handler::process($validation['data']);
        if (!$result['success']) {
            wp_send_json_error(['message' => $result['message']]);
        }

        // WhatsApp con variable centralizada
        $fecha_fmt = date('d/m/Y', strtotime($validation['data']['fecha_agendada']));
        $mensaje = sprintf(
            "Hola, quiero agendar una sesión:\n\nNombre: %s\nTeléfono: %s\nTema: %s\nFecha: %s",
            $validation['data']['nombre'],
            $validation['data']['numero'],
            $validation['data']['tema'],
            $fecha_fmt
        );

        $wa_url = 'https://wa.me/' . AVANCE_WHATSAPP . '?text=' . urlencode($mensaje);

        wp_send_json_success([
            'id' => $result['id'],
            'whatsapp_url' => $wa_url,
            'fecha_formateada' => $fecha_fmt,
        ]);
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

        $wa_url = 'https://wa.me/' . AVANCE_WHATSAPP . '?text=' . urlencode($mensaje);

        wp_send_json_success([
            'id' => $result['id'],
            'whatsapp_url' => $wa_url,
        ]);
    }
}
