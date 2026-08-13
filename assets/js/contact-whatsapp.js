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
		ajaxUrl: (typeof avanceFormConfig !== 'undefined' && avanceFormConfig.ajaxUrl)
			? avanceFormConfig.ajaxUrl
			: '/wp-admin/admin-ajax.php',
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

		if (!form) {
			console.warn('[Avance Contact Form] Form ID not found:', config.formId);
			return;
		}

		console.log('[Avance Contact Form] Initialized successfully');
		console.log('[Avance Contact Form] AJAX URL:', config.ajaxUrl);
		form.addEventListener('submit', handleFormSubmit);
		initCustomDropdown();
	}

	function initCustomDropdown() {
		const trigger = document.getElementById('contacto_wsp_asunto_trigger');
		const menu = document.getElementById('contacto_wsp_asunto_menu');
		const hiddenInput = document.getElementById('contacto_wsp_asunto');
		const options = menu ? menu.querySelectorAll('.home-form__select-option') : [];

		if (!trigger || !menu) return;

		trigger.addEventListener('click', () => {
			menu.classList.toggle('open');
		});

		document.addEventListener('click', (e) => {
			if (!trigger.contains(e.target) && !menu.contains(e.target)) {
				menu.classList.remove('open');
			}
		});

		options.forEach(option => {
			option.addEventListener('click', () => {
				const value = option.getAttribute('data-value');
				hiddenInput.value = value;
				trigger.textContent = option.textContent;
				trigger.setAttribute('data-selected', value);
				options.forEach(opt => opt.classList.remove('selected'));
				option.classList.add('selected');
				menu.classList.remove('open');
				console.log('[Custom Dropdown] Selected:', value);
			});
		});
	}

	function handleFormSubmit(e) {
		e.preventDefault();

		console.log('[Avance Contact Form] Form submit triggered');

		if (config.isSubmitting) {
			console.warn('[Avance Contact Form] Already submitting, ignoring duplicate');
			return;
		}

		const formData = getFormData();
		console.log('[Avance Contact Form] Form data:', formData);

		if (!validateFormData(formData)) {
			console.warn('[Avance Contact Form] Validation failed');
			return;
		}

		config.isSubmitting = true;
		console.log('[Avance Contact Form] Submitting via AJAX...');
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
			.then(response => {
				console.log('[Avance Contact Form] AJAX Response Status:', response.status);
				return response.json();
			})
			.then(response => {
				config.isSubmitting = false;

				console.log('[Avance Contact Form] AJAX Response:', response);

				if (response.success && response.data && response.data.url) {
					console.log('[Avance Contact Form] Success! Opening WhatsApp:', response.data.url);
					openWhatsApp(response.data.url);
					showSuccess(response.data.message || 'Mensaje enviado correctamente.');
					resetForm();
				} else {
					console.error('[Avance Contact Form] API Error:', response.data);
					showError(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				config.isSubmitting = false;
				console.error('[Avance Contact Form] Fetch Error:', error);
				showError('Error de conexión. Intenta de nuevo.');
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
