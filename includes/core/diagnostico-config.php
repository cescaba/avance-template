<?php
/**
 * Configuración centralizada del Diagnóstico
 * Única fuente de verdad para preguntas - usada en frontend y backend
 */

if (!defined('ABSPATH')) {
	exit;
}

function avance_get_diagnostico_questions() {
	return [
		'¿Cuál es tu mayor desafío comercial ahora mismo?',
		'¿Cuántos vendedores tiene tu equipo actualmente?',
		'¿Tienes definida tu propuesta de valor?',
		'¿Cuánto inviertes en formación comercial?'
	];
}
