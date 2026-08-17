/**
 * Appointment Handler - Form AJAX
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	const config = {
		formId: 'appointment-form',
		ajaxUrl: typeof avanceAppointmentConfig !== 'undefined' ? avanceAppointmentConfig.ajaxUrl : '/wp-admin/admin-ajax.php',
		nonce: typeof avanceAppointmentConfig !== 'undefined' ? avanceAppointmentConfig.nonce : '',
		isSubmitting: false,
	};

	function initForm() {
		const form = document.getElementById(config.formId);
		if (!form) {
			return;
		}

		form.addEventListener('submit', handleFormSubmit);
	}

	function handleFormSubmit(e) {
		e.preventDefault();

		if (config.isSubmitting) return;


		const formData = getFormData();
		if (!validateFormData(formData)) {
			return;
		}

		config.isSubmitting = true;
		submitFormViaAjax(formData);
	}

	function getFormData() {
		return {
			nombre: getFieldValue('appointment-nombre'),
			whatsapp: getFieldValue('appointment-whatsapp'),
			servicio: getFieldValue('appointment-servicio'),
			fecha: getFieldValue('appointment-fecha'),
			hora: getFieldValue('appointment-hora'),
			notas: getFieldValue('appointment-notas'),
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
		if (!data.whatsapp) {
			showError('El WhatsApp es requerido');
			return false;
		}
		if (!data.servicio) {
			showError('Debe seleccionar un servicio');
			return false;
		}
		if (!data.fecha) {
			showError('Debe seleccionar una fecha');
			return false;
		}
		if (!data.hora) {
			showError('Debe seleccionar una hora');
			return false;
		}
		return true;
	}

	function submitFormViaAjax(formData) {
		const ajaxData = new FormData();

		ajaxData.append('action', 'avance_submit_appointment');
		ajaxData.append('nonce', config.nonce);
		ajaxData.append('nombre', formData.nombre);
		ajaxData.append('whatsapp', formData.whatsapp);
		ajaxData.append('servicio', formData.servicio);
		ajaxData.append('fecha', formData.fecha);
		ajaxData.append('hora', formData.hora);
		ajaxData.append('notas', formData.notas);


		fetch(config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => response.json())
			.then(response => {
				config.isSubmitting = false;

	
				if (response.success && response.data?.url) {
					showSuccess('Cita agendada. Abriendo WhatsApp...');
					window.open(response.data.url, '_blank');
					resetForm();
				} else {
					showError(response.data?.message || 'Error al agendar la cita');
				}
			})
			.catch(error => {
				config.isSubmitting = false;
					showError('Error de conexión. Intenta de nuevo.');
			});
	}

	function resetForm() {
		const form = document.getElementById(config.formId);
		if (form) form.reset();
	}

	function showError(message) {
		alert(message);
	}

	function showSuccess(message) {
	}

	// Inicializar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initForm);
	} else {
		initForm();
	}
})();
