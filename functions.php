<?php
/**
 * Avance Template - Functions
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

// Incluir configuración centralizada
require_once get_template_directory() . '/config/settings.php';
require_once get_template_directory() . '/config/theme-config.php';

// Incluir servicios core
require_once get_template_directory() . '/includes/core/class-core-service.php';
require_once get_template_directory() . '/includes/core/class-ajax-handler.php';

// Instanciar servicios
new Avance_Core_Service();
new Avance_Ajax_Handler();

// Incluir clases de CONTACTOS
require_once get_template_directory() . '/includes/validators/class-contact-validator.php';
require_once get_template_directory() . '/includes/database/contacts/class-contact-db.php';
require_once get_template_directory() . '/includes/database/contacts/class-contact-form-handler.php';
require_once get_template_directory() . '/includes/admin/contacts/class-contact-admin.php';

// Incluir orquestador de AGENDAMIENTO
require_once get_template_directory() . '/includes/appointments/class-appointments-manager.php';

// Incluir orquestador de DIAGNÓSTICO
require_once get_template_directory() . '/includes/managers/diagnostico/class-diagnostico-manager.php';

// Incluir clases de AGENDAMIENTO DE SESIONES
require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-sesiones-db.php';
require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-sesiones-handler.php';

// Incluir clases de PROPUESTAS
require_once get_template_directory() . '/includes/database/proposals/class-proposal-db.php';
require_once get_template_directory() . '/includes/database/proposals/class-proposal-handler.php';

// Incluir integración con WOOCOMMERCE (Mentoría, Reservas)
require_once get_template_directory() . '/includes/woocommerce/index.php';

// Incluir COMPONENTES REUTILIZABLES
require_once get_template_directory() . '/includes/managers/scheduling/class-scheduling-component.php';
require_once get_template_directory() . '/includes/database/form-sesiones/class-form-component.php';
