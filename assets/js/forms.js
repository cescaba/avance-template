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
 * ServicioEmpresaHandler - Para formulario de servicios empresas
 */
class ServicioEmpresaHandler extends FormHandler {
	constructor() {
		super({
			formId: 'proposal-form',
			ajaxUrl: '/wp-admin/admin-ajax.php',
			nonce: '',
		});
		this.getNonce();
	}

	getNonce() {
		const form = document.getElementById(this.config.formId);
		if (form) {
			const nonceField = form.querySelector('input[name="nonce"]');
			if (nonceField) {
				this.config.nonce = nonceField.value;
			}
		}
	}

	showError(message) {
		const header = document.querySelector('.servicio-contact__header');
		if (header) {
			// Crear notificación con diseño correcto
			const notif = document.createElement('div');
			notif.className = 'notification notification--error';
			notif.textContent = message;
			header.insertAdjacentElement('afterend', notif);
			setTimeout(() => notif.remove(), 4000);
		}
	}

	showSuccessMessage(message) {
		const header = document.querySelector('.servicio-contact__header');
		if (header) {
			// Crear notificación con diseño correcto
			const notif = document.createElement('div');
			notif.className = 'notification notification--success';
			notif.textContent = message;
			header.insertAdjacentElement('afterend', notif);
			setTimeout(() => notif.remove(), 4000);
		}
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
			servicio_interes: form.querySelector('select[name="servicio_interes"]')?.value.trim() || '',
			desafio_comercial: form.querySelector('textarea[name="desafio_comercial"]')?.value.trim() || '',
			nonce: this.config.nonce,
		};
	}

	validateFormData(data) {
		const errors = [];

		if (!data.nombre || data.nombre.length < 3) errors.push('Nombre debe tener al menos 3 caracteres');
		if (!data.empresa || data.empresa.length < 2) errors.push('Empresa es requerida');
		if (!data.email || !this.isValidEmail(data.email)) errors.push('Email válido es requerido');
		if (!data.servicio_interes || data.servicio_interes.length < 3) errors.push('Servicio de interés es requerido');
		if (!data.desafio_comercial || data.desafio_comercial.length < 10) errors.push('Describe tu desafío con más detalle');

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
		ajaxData.append('action', 'avance_submit_servicio_empresa');
		ajaxData.append('nonce', formData.nonce);

		Object.keys(formData).forEach(key => {
			if (key !== 'nonce') {
				ajaxData.append(key, formData[key]);
			}
		});

		submitBtn.disabled = true;
		submitBtn.textContent = 'Procesando...';

		fetch(this.config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then((response) => {
				return response.json().then(data => {
					if (!response.ok) {
						throw { status: response.status, data: data };
					}
					return data;
				});
			})
			.then((result) => {
				this.config.isSubmitting = false;

				if (result.success) {
					submitBtn.textContent = '¡Propuesta enviada!';
					submitBtn.style.backgroundColor = '#28a745';

					if (result.data?.url) {
						this.openWhatsApp(result.data.url);
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
				submitBtn.textContent = 'Solicitar propuesta personalizada';
				submitBtn.disabled = false;

				let errorMessage = 'Error al procesar la solicitud. Intenta de nuevo.';

				if (error.status === 409) {
					errorMessage = error.data?.data?.message || 'Este email ya fue registrado en las últimas 24 horas.';
				} else if (error.status === 429) {
					errorMessage = error.data?.data?.message || 'Demasiados intentos. Por favor, espera antes de intentar de nuevo.';
				} else if (error.status === 403) {
					errorMessage = error.data?.data?.message || 'Tu IP ha sido bloqueada.';
				} else if (error.status === 400) {
					errorMessage = error.data?.data?.message || 'Datos inválidos. Por favor, verifica tu información.';
				} else if (error.status === 500) {
					errorMessage = error.data?.data?.message || 'Error del servidor. Por favor, intenta más tarde.';
				}

				this.showError(errorMessage);
			});
	}
}

/**
 * ProposalFormHandler (DEPRECATED - usar ServicioEmpresaHandler)
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
			servicio_interes: form.querySelector('select[name="servicio_interes"]')?.value.trim() || '',
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
		let nonce = this.config.nonce;
		const quizView = document.getElementById('diagnosticoQuizView');
		const quizTitle = document.getElementById('diagnosticoQuestionText');

		if (quizView) {
			const nonceField = quizView.querySelector('input[name="nonce"]');
			if (nonceField) {
				nonce = nonceField.value;
			}
		}

		const ajaxData = new FormData();
		ajaxData.append('action', 'avance_submit_diagnostico');
		ajaxData.append('nonce', nonce);
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
				return response.json().then(data => {
					if (!response.ok) {
						throw { status: response.status, data: data };
					}
					return data;
				});
			})
			.then(response => {
				if (response.success && response.data?.url) {
					button.textContent = 'Abriendo WhatsApp...';

					if (quizTitle && typeof NotificationManager !== 'undefined') {
						NotificationManager.success('¡Diagnóstico enviado correctamente!', quizTitle);
					}

					setTimeout(() => {
						window.open(response.data.url, '_blank');

						setTimeout(() => {
							button.textContent = 'Se envió su diagnóstico';
						}, 800);
					}, 1500);
				} else {
					button.textContent = 'Se envió su diagnóstico';
					const errorMsg = response.data?.message || 'Ocurrió un error. Intenta de nuevo.';
					if (quizTitle && typeof NotificationManager !== 'undefined') {
						NotificationManager.error(errorMsg, quizTitle);
					} else {
						alert(errorMsg);
					}
				}
			})
			.catch(error => {
				button.textContent = 'Se envió su diagnóstico';
				button.disabled = false;

				let errorMessage = 'Error de conexión. Intenta de nuevo.';

				if (error.status === 409) {
					errorMessage = error.data?.data?.message || 'Este email ya fue registrado en las últimas 24 horas.';
				} else if (error.status === 429) {
					errorMessage = error.data?.data?.message || 'Demasiados intentos. Por favor, espera antes de intentar de nuevo.';
				} else if (error.status === 403) {
					errorMessage = error.data?.data?.message || 'Tu IP ha sido bloqueada.';
				} else if (error.status === 400) {
					errorMessage = error.data?.data?.message || 'Datos inválidos. Por favor, verifica tu información.';
				} else if (error.status === 500) {
					errorMessage = error.data?.data?.message || 'Error del servidor. Por favor, intenta más tarde.';
				}

				if (quizTitle && typeof NotificationManager !== 'undefined') {
					NotificationManager.error(errorMessage, quizTitle);
				} else {
					alert(errorMessage);
				}
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
		document.addEventListener('submit', (e) => {
			if (e.target && e.target.id === this.config.formId) {
				this.handleFormSubmit(e);
			}
		}, true);
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
				return response.json().then(data => {
					if (!response.ok) {
						throw { status: response.status, data: data };
					}
					return data;
				});
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

				let errorMessage = 'Error de conexión. Intenta de nuevo.';

				if (error.status === 409) {
					errorMessage = error.data?.data?.message || 'Este email o número ya fue registrado en las últimas 24 horas.';
				} else if (error.status === 429) {
					errorMessage = error.data?.data?.message || 'Demasiados intentos. Por favor, espera antes de intentar de nuevo.';
				} else if (error.status === 403) {
					errorMessage = error.data?.data?.message || 'Tu IP ha sido bloqueada.';
				} else if (error.status === 400) {
					errorMessage = error.data?.data?.message || 'Datos inválidos. Por favor, verifica tu información.';
				} else if (error.status === 500) {
					errorMessage = error.data?.data?.message || 'Error del servidor. Por favor, intenta más tarde.';
				}

				this.showError(errorMessage);
			});
	}

	getNonce() {
		// Primero intentar obtener del config localizado (avanceFormConfig)
		if (typeof avanceFormConfig !== 'undefined' && avanceFormConfig.nonce) {
			return avanceFormConfig.nonce;
		}
		// Si no está disponible, buscar en el formulario HTML
		const form = document.getElementById(this.config.formId);
		const nonceField = form ? form.querySelector('input[name="nonce"]') : null;
		return nonceField ? nonceField.value : '';
	}

	showSuccessMessage(message) {
		const form = document.getElementById(this.config.formId);
		if (form) {
			NotificationManager.success(message, form);
		}
	}

	showError(message) {
		const form = document.getElementById(this.config.formId);
		if (form) {
			NotificationManager.error(message, form);
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
	// Servicio Empresa Form - Opcional (solo si existe)
	if (document.getElementById('proposal-form')) {
		new ServicioEmpresaHandler().init();
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
