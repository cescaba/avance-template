<?php
/**
 * SMTP Validator - Validación DNS asincrónica
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Smtp_Validator {

	public function __construct() {
		add_action('avance_smtp_async_check', [$this, 'async_smtp_check'], 10, 2);
	}

	public function async_smtp_check($email, $domain) {
		if (class_exists('Global_Form_Handler')) {
			Global_Form_Handler::async_smtp_check($email, $domain);
		}
	}
}
