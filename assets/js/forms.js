/**
 * Forms System
 * Gestiona: Proposal Form + Diagnostico Submit + Contact WhatsApp + Appointment Handler
 *
 * @package Avance_Template
 */

/**
 * FormHandler - Clase base para todos los formularios
 */
class FormHandler {
	constructor(config) {
		this.config = {
			formId: null,
			ajaxUrl: '/wp-admin/admin-ajax.php',
			nonce: '',
			isSubmitting: false,
			...config
		};
	}

	init() {
		const form = document.getElementById(this.config.formId);
		if (!form) return;

		form.addEventListener('submit', (e) => this.handleFormSubmit(e));
		this.onInit();
	}

	onInit() {
		// Override en subclases si se necesita lógica adicional de inicialización
	}

	handleFormSubmit(e) {
		e.preventDefault();

		if (this.config.isSubmitting) return;

		const formData = this.getFormData();
		if (!this.validateFormData(formData)) {
			return;
		}

		this.config.isSubmitting = true;
		this.submitFormViaAjax(formData);
	}

	getFormData() {
		throw new Error('getFormData debe ser implementado en subclase');
	}

	validateFormData(data) {
		throw new Error('validateFormData debe ser implementado en subclase');
	}

	submitFormViaAjax(formData) {
		throw new Error('submitFormViaAjax debe ser implementado en subclase');
	}

	showError(message) {
		alert(message);
	}

	showSuccess(message) {
		alert(message);
	}

	resetForm() {
		const form = document.getElementById(this.config.formId);
		if (form) form.reset();
	}

	isValidEmail(email) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return emailRegex.test(email);
	}

	openWhatsApp(url) {
		window.open(url, '_blank');
	}

	getFieldValue(fieldId) {
		const field = document.getElementById(fieldId);
		return field ? field.value.trim() : '';
	}
}

/**
 * ProposalFormHandler
 */
class ProposalFormHandler extends FormHandler {
	constructor() {
		super({
			formId: 'proposal-form',
			ajaxUrl: typeof avanceProposalConfig !== 'undefined' ? avanceProposalConfig.ajaxUrl : '/wp-admin/admin-ajax.php',
			nonce: typeof avanceProposalConfig !== 'undefined' ? avanceProposalConfig.nonce : '',
		});
	}

	getFormData() {
		const form = document.getElementById(this.config.formId);
		return {
			nombre: form.querySelector('input[name="nombre"]')?.value.trim() || '',
			cargo: form.querySelector('input[name="cargo"]')?.value.trim() || '',
			empresa: form.querySelector('input[name="empresa"]')?.value.trim() || '',
			tamaño_equipo: form.querySelector('input[name="tamaño_equipo"]')?.value.trim() || '',
			email: form.querySelector('input[name="email"]')?.value.trim() || '',
			whatsapp: form.querySelector('input[name="whatsapp"]')?.value.trim() || '',
			servicio_interes: form.querySelector('input[name="servicio_interes"]')?.value.trim() || '',
			desafio_comercial: form.querySelector('textarea[name="desafio_comercial"]')?.value.trim() || '',
		};
	}

	validateFormData(data) {
		const errors = [];

		if (!data.nombre) errors.push('El nombre es requerido');
		if (!data.empresa) errors.push('La empresa es requerida');
		if (!data.email) errors.push('El email es requerido');
		else if (!this.isValidEmail(data.email)) errors.push('El email no es válido');
		if (!data.servicio_interes) errors.push('El servicio de interés es requerido');
		if (!data.desafio_comercial) errors.push('El desafío comercial es requerido');
		else if (data.desafio_comercial.length < 10) errors.push('Por favor describe tu desafío con más detalle');

		if (errors.length > 0) {
			this.showError('Errores en el formulario:\n\n' + errors.join('\n'));
			return false;
		}

		return true;
	}

	submitFormViaAjax(formData) {
		const form = document.getElementById(this.config.formId);
		const submitBtn = form.querySelector('button[type="submit"]');

		const ajaxData = new FormData();
		ajaxData.append('action', 'avance_proposal_submit');
		ajaxData.append('nonce', this.config.nonce);
		Object.keys(formData).forEach(key => {
			ajaxData.append(key, formData[key]);
		});

		submitBtn.disabled = true;
		submitBtn.textContent = 'Procesando...';

		fetch(this.config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then((response) => {
				if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
				return response.json();
			})
			.then((result) => {
				this.config.isSubmitting = false;

				if (result.success) {
					submitBtn.textContent = '¡Propuesta enviada!';
					submitBtn.style.backgroundColor = '#28a745';

					if (result.data?.whatsapp_url) {
						this.openWhatsApp(result.data.whatsapp_url);
					}

					setTimeout(() => {
						this.resetForm();
						submitBtn.textContent = 'Solicitar propuesta personalizada';
						submitBtn.style.backgroundColor = '';
						submitBtn.disabled = false;
					}, 3000);
				} else {
					submitBtn.textContent = 'Error al enviar';
					submitBtn.disabled = false;
					this.showError('Error: ' + (result.data?.message || result.message || 'Error desconocido'));
				}
			})
			.catch((error) => {
				this.config.isSubmitting = false;
				submitBtn.textContent = 'Error al procesar';
				submitBtn.disabled = false;
				this.showError('Error al procesar la solicitud: ' + error.message);
			});
	}
}

/**
 * DiagnosticoSubmitHandler
 */
class DiagnosticoSubmitHandler {
	constructor() {
		this.config = {
			ajaxUrl: typeof avanceDiagnosticoConfig !== 'undefined' ? avanceDiagnosticoConfig.ajaxUrl : '/wp-admin/admin-ajax.php',
			nonce: typeof avanceDiagnosticoConfig !== 'undefined' ? avanceDiagnosticoConfig.nonce : '',
		};

		window.avanceDiagnosticoSubmit = (formData, button) => this.submitDiagnostico(formData, button);
	}

	submitDiagnostico(formData, button) {
		const ajaxData = new FormData();
		ajaxData.append('action', 'avance_submit_diagnostico');
		ajaxData.append('nonce', this.config.nonce);
		ajaxData.append('nombreCompleto', formData.nombreCompleto);
		ajaxData.append('email', formData.email);
		ajaxData.append('whatsapp', formData.whatsapp);

		if (Array.isArray(formData.respuestas)) {
			formData.respuestas.forEach((respuesta) => {
				ajaxData.append('respuestas[]', respuesta);
			});
		}

		button.textContent = 'Procesando...';

		fetch(this.config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => {
				if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
				return response.json();
			})
			.then(response => {
				if (response.success && response.data?.url) {
					button.textContent = 'Abriendo WhatsApp...';

					setTimeout(() => {
						window.open(response.data.url, '_blank');

						setTimeout(() => {
							button.textContent = 'Se envió su diagnóstico';
						}, 800);
					}, 1500);
				} else {
					button.textContent = 'Se envió su diagnóstico';
					alert(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				button.textContent = 'Se envió su diagnóstico';
				alert('Error de conexión. Intenta de nuevo.');
			});
	}
}

/**
 * ContactWhatsAppHandler
 */
class ContactWhatsAppHandler extends FormHandler {
	constructor() {
		super({
			formId: 'contacto-wsp-form',
			ajaxUrl: typeof avanceFormConfig !== 'undefined' ? avanceFormConfig.ajaxUrl : '/wp-admin/admin-ajax.php',
		});
	}

	onInit() {
		this.initCustomDropdown();

		document.addEventListener('submit', (e) => {
			if (e.target && e.target.id === this.config.formId) {
				this.handleFormSubmit(e);
			}
		}, true);
	}

	initCustomDropdown() {
		const trigger = document.getElementById('contacto_wsp_asunto_trigger');
		const menu = document.getElementById('contacto_wsp_asunto_menu');

		if (!trigger || !menu) return;

		const hiddenInput = document.getElementById('contacto_wsp_asunto');
		const options = menu.querySelectorAll('.home-form__select-option');

		if (!hiddenInput) return;

		trigger.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			menu.classList.toggle('open');
		});

		document.addEventListener('click', (e) => {
			if (!trigger.contains(e.target) && !menu.contains(e.target)) {
				menu.classList.remove('open');
			}
		});

		options.forEach(option => {
			option.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();

				const value = option.getAttribute('data-value');
				if (value) {
					hiddenInput.value = value;
					trigger.textContent = option.textContent;
					trigger.setAttribute('data-selected', value);

					options.forEach(opt => opt.classList.remove('selected'));
					option.classList.add('selected');

					menu.classList.remove('open');
				}
			});
		});
	}

	getFormData() {
		return {
			nombre: this.getFieldValue('contacto_wsp_nombre'),
			email: this.getFieldValue('contacto_wsp_email'),
			numero: this.getFieldValue('contacto_wsp_numero'),
			asunto: this.getFieldValue('contacto_wsp_asunto'),
			mensaje: this.getFieldValue('contacto_wsp_mensaje'),
		};
	}

	validateFormData(data) {
		if (!data.nombre) {
			this.showError('El nombre es requerido');
			return false;
		}
		if (!data.email) {
			this.showError('El email es requerido');
			return false;
		}
		if (!this.isValidEmail(data.email)) {
			this.showError('El email no es válido');
			return false;
		}
		if (!data.numero) {
			this.showError('El número de WhatsApp es requerido');
			return false;
		}
		if (!data.asunto) {
			this.showError('Selecciona un servicio de interés');
			return false;
		}
		return true;
	}

	submitFormViaAjax(formData) {
		const ajaxData = new FormData();

		ajaxData.append('action', 'avance_submit_contact');
		ajaxData.append('nonce', this.getNonce());
		Object.keys(formData).forEach(key => {
			ajaxData.append(key, formData[key]);
		});

		fetch(this.config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => {
				if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
				return response.json();
			})
			.then(response => {
				this.config.isSubmitting = false;

				if (response.success && response.data?.url) {
					this.openWhatsApp(response.data.url);
					this.showSuccessMessage(response.data.message || 'Mensaje enviado correctamente.');
					this.resetForm();
				} else {
					this.showError(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				this.config.isSubmitting = false;
				this.showError('Error de conexión. Intenta de nuevo.');
			});
	}

	getNonce() {
		const form = document.getElementById(this.config.formId);
		const nonceField = form ? form.querySelector('input[name="nonce"]') : null;
		return nonceField ? nonceField.value : '';
	}

	showSuccessMessage(message) {
		const form = document.getElementById(this.config.formId);
		if (form) {
			const successDiv = document.createElement('div');
			successDiv.style.cssText = 'background-color: #efe; border: 1px solid #cfc; color: #3c3; padding: 12px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;';
			successDiv.textContent = message;
			form.insertAdjacentElement('afterbegin', successDiv);
			setTimeout(() => successDiv.remove(), 4000);
		}
	}

	showError(message) {
		const form = document.getElementById(this.config.formId);
		if (form) {
			const errorDiv = document.createElement('div');
			errorDiv.style.cssText = 'background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;';
			errorDiv.textContent = message;
			form.insertAdjacentElement('afterbegin', errorDiv);
			setTimeout(() => errorDiv.remove(), 4000);
		}
	}
}

/**
 * AppointmentHandler
 */
class AppointmentHandler extends FormHandler {
	constructor() {
		super({
			formId: 'appointment-form',
			ajaxUrl: typeof avanceAppointmentConfig !== 'undefined' ? avanceAppointmentConfig.ajaxUrl : '/wp-admin/admin-ajax.php',
			nonce: typeof avanceAppointmentConfig !== 'undefined' ? avanceAppointmentConfig.nonce : '',
		});
	}

	getFormData() {
		return {
			nombre: this.getFieldValue('appointment-nombre'),
			whatsapp: this.getFieldValue('appointment-whatsapp'),
			servicio: this.getFieldValue('appointment-servicio'),
			fecha: this.getFieldValue('appointment-fecha'),
			hora: this.getFieldValue('appointment-hora'),
			notas: this.getFieldValue('appointment-notas'),
		};
	}

	validateFormData(data) {
		if (!data.nombre) {
			this.showError('El nombre es requerido');
			return false;
		}
		if (!data.whatsapp) {
			this.showError('El WhatsApp es requerido');
			return false;
		}
		if (!data.servicio) {
			this.showError('Debe seleccionar un servicio');
			return false;
		}
		if (!data.fecha) {
			this.showError('Debe seleccionar una fecha');
			return false;
		}
		if (!data.hora) {
			this.showError('Debe seleccionar una hora');
			return false;
		}
		return true;
	}

	submitFormViaAjax(formData) {
		const ajaxData = new FormData();

		ajaxData.append('action', 'avance_submit_appointment');
		ajaxData.append('nonce', this.config.nonce);
		Object.keys(formData).forEach(key => {
			ajaxData.append(key, formData[key]);
		});

		fetch(this.config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => response.json())
			.then(response => {
				this.config.isSubmitting = false;

				if (response.success && response.data?.url) {
					this.showError('Cita agendada. Abriendo WhatsApp...');
					this.openWhatsApp(response.data.url);
					this.resetForm();
				} else {
					this.showError(response.data?.message || 'Error al agendar la cita');
				}
			})
			.catch(error => {
				this.config.isSubmitting = false;
				this.showError('Error de conexión. Intenta de nuevo.');
			});
	}
}

/**
 * Inicializar todos los formularios cuando el DOM esté listo
 */
function initForms() {
	// Proposal Form - Opcional (solo si existe)
	if (document.getElementById('proposal-form')) {
		new ProposalFormHandler().init();
	}

	// Diagnostico Handler - Siempre inicializar (define función global)
	new DiagnosticoSubmitHandler();

	// Contact WhatsApp - Opcional (solo si existe)
	if (document.getElementById('contacto-wsp-form')) {
		new ContactWhatsAppHandler().init();
	}

	// Appointment Handler - Opcional (solo si existe)
	if (document.getElementById('appointment-form')) {
		new AppointmentHandler().init();
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initForms);
} else {
	initForms();
}
