/**
 * Contact Form to WhatsApp Handler
 * Sends form data via AJAX with backend validation
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	const config = {
		formId: 'contacto-wsp-form',
		ajaxUrl: typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php',
		fields: {
			nombre: 'contacto_wsp_nombre',
			email: 'contacto_wsp_email',
			numero: 'contacto_wsp_numero',
			asunto: 'contacto_wsp_asunto',
			mensaje: 'contacto_wsp_mensaje',
		},
		isSubmitting: false,
	};

	function initForm() {
		const form = document.getElementById(config.formId);
		if (!form) return;

		form.addEventListener('submit', handleFormSubmit);
	}

	function handleFormSubmit(e) {
		e.preventDefault();

		if (config.isSubmitting) return;

		const formData = getFormData();
		if (!validateFormData(formData)) return;

		config.isSubmitting = true;
		submitFormViaAjax(formData);
	}

	function getFormData() {
		return {
			nombre: getFieldValue(config.fields.nombre),
			email: getFieldValue(config.fields.email),
			numero: getFieldValue(config.fields.numero),
			asunto: getFieldValue(config.fields.asunto),
			mensaje: getFieldValue(config.fields.mensaje),
		};
	}

	function getFieldValue(fieldId) {
		const field = document.getElementById(fieldId);
		return field ? field.value.trim() : '';
	}

	function validateFormData(data) {
		if (!data.nombre) {
			showError('El nombre es requerido');
			return false;
		}
		if (!data.email) {
			showError('El email es requerido');
			return false;
		}
		if (!isValidEmail(data.email)) {
			showError('El email no es válido');
			return false;
		}
		if (!data.numero) {
			showError('El número de WhatsApp es requerido');
			return false;
		}
		if (!data.asunto) {
			showError('Selecciona un servicio de interés');
			return false;
		}
		return true;
	}

	function isValidEmail(email) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return emailRegex.test(email);
	}

	function submitFormViaAjax(formData) {
		const ajaxData = new FormData();

		ajaxData.append('action', 'avance_submit_contact');
		ajaxData.append('nonce', getFormNonce());
		ajaxData.append('nombre', formData.nombre);
		ajaxData.append('email', formData.email);
		ajaxData.append('numero', formData.numero);
		ajaxData.append('asunto', formData.asunto);
		ajaxData.append('mensaje', formData.mensaje);

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => response.json())
			.then(response => {
				config.isSubmitting = false;

				if (response.success && response.data && response.data.url) {
					// Validación pasada - abrir WhatsApp
					openWhatsApp(response.data.url);
					showSuccess(response.data.message || 'Mensaje enviado correctamente.');
					resetForm();
				} else {
					showError(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				config.isSubmitting = false;
				showError('Error de conexión. Intenta de nuevo.');
				console.error('AJAX Error:', error);
			});
	}

	function getFormNonce() {
		const form = document.getElementById(config.formId);
		const nonceField = form ? form.querySelector('input[name="nonce"]') : null;
		return nonceField ? nonceField.value : '';
	}

	function openWhatsApp(url) {
		window.open(url, '_blank');
	}

	function resetForm() {
		const form = document.getElementById(config.formId);
		if (form) form.reset();
	}

	function showError(message) {
		alert(message);
	}

	function showSuccess(message) {
		// Opcional: mostrar mensaje de éxito elegante
		console.log('Success:', message);
	}

	// Inicializar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initForm);
	} else {
		initForm();
	}
})();
