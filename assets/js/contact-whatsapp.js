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
		// Usar event delegation en document para detectar submits del formulario
		// Esto funciona incluso si el formulario se carga dinámicamente
		document.addEventListener('submit', (e) => {
			if (e.target && e.target.id === config.formId) {
				handleFormSubmit(e);
			}
		}, true); // Usar capture phase para asegurar que se ejecuta primero

		// Inicializar dropdown si el formulario ya existe
		initCustomDropdown();
	}

	function initCustomDropdown() {
		// Solo inicializar si existen los elementos del dropdown personalizado
		const trigger = document.getElementById('contacto_wsp_asunto_trigger');
		const menu = document.getElementById('contacto_wsp_asunto_menu');

		// Si no existen, probablemente se esté usando un select nativo
		if (!trigger || !menu) {
			return;
		}

		const hiddenInput = document.getElementById('contacto_wsp_asunto');
		const options = menu.querySelectorAll('.home-form__select-option');

		if (!hiddenInput) {
			return;
		}

		// Toggle dropdown
		trigger.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			menu.classList.toggle('open');
		});

		// Close dropdown when clicking outside
		document.addEventListener('click', (e) => {
			if (!trigger.contains(e.target) && !menu.contains(e.target)) {
				menu.classList.remove('open');
			}
		});

		// Handle option selection
		options.forEach(option => {
			option.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();

				const value = option.getAttribute('data-value');
				if (value) {
					hiddenInput.value = value;
					trigger.textContent = option.textContent;
					trigger.setAttribute('data-selected', value);

					// Update selected state
					options.forEach(opt => opt.classList.remove('selected'));
					option.classList.add('selected');

					// Close menu
					menu.classList.remove('open');
				}
			});
		});
	}

	function handleFormSubmit(e) {
		e.preventDefault();

		if (config.isSubmitting) {
			return;
		}

		const formData = getFormData();

		if (!validateFormData(formData)) {
			return;
		}

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
			.then(response => {
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.then(response => {
				config.isSubmitting = false;

				if (response.success && response.data && response.data.url) {
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
		const form = document.getElementById(config.formId);
		if (form) {
			const errorDiv = document.createElement('div');
			errorDiv.style.cssText = 'background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;';
			errorDiv.textContent = message;
			form.insertAdjacentElement('afterbegin', errorDiv);
			setTimeout(() => errorDiv.remove(), 4000);
		}
	}

	function showSuccess(message) {
		const form = document.getElementById(config.formId);
		if (form) {
			const successDiv = document.createElement('div');
			successDiv.style.cssText = 'background-color: #efe; border: 1px solid #cfc; color: #3c3; padding: 12px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;';
			successDiv.textContent = message;
			form.insertAdjacentElement('afterbegin', successDiv);
			setTimeout(() => successDiv.remove(), 4000);
		}
	}

	// Inicializar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initForm);
	} else {
		initForm();
	}
})();
