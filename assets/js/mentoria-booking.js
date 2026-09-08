/**
 * Mentoría Booking - Sincroniza calendar-agenda.js con formulario
 * Envía datos a handler-mentoria-booking.php
 */

(function() {
	'use strict';

	const config = {
		ajaxUrl: (typeof avanceFormConfig !== 'undefined' && avanceFormConfig.ajaxUrl)
			? avanceFormConfig.ajaxUrl
			: '/wp-admin/admin-ajax.php',
		nonce: (typeof mentoriaConfig !== 'undefined' && mentoriaConfig.nonce) ? mentoriaConfig.nonce : '',
		isSubmitting: false,
	};

	function init() {
		attachFormListener();
	}

	function attachFormListener() {
		const form = document.querySelector('.mentoria-reserva__form');
		if (!form) return;

		const submitBtn = form.querySelector('.mentoria-reserva__submit');
		if (submitBtn) {
			submitBtn.addEventListener('click', handleSubmit);
		}
	}

	function handleSubmit(e) {
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
		const firstName = document.getElementById('mentoriaFirstName')?.value.trim() || '';
		const lastName = document.getElementById('mentoriaLastName')?.value.trim() || '';
		const email = document.getElementById('mentoriaEmail')?.value.trim() || '';
		const whatsapp = document.getElementById('mentoriaWhatsapp')?.value.trim() || '';
		const desafio = document.getElementById('mentoriaDesafio')?.value.trim() || '';

		// Leer datos del calendario (calendar-agenda.js)
		const calendarData = window.mentoriaSelectedDate;
		const calendarTime = window.mentoriaSelectedTime;

		let fecha = '';
		if (calendarData && calendarData.day) {
			fecha = `${calendarData.day}/${calendarData.month + 1}/${calendarData.year}`;
		}

		const agendaSub = document.getElementById('agendaSub');
		const plan = agendaSub ? agendaSub.textContent.split(' ')[0].toLowerCase() : '';

		return {
			first_name: firstName,
			last_name: lastName,
			email: email,
			whatsapp: whatsapp,
			desafio: desafio,
			plan: plan,
			fecha: fecha,
			hora: calendarTime || '',
		};
	}

	function validateFormData(data) {
		if (!data.first_name || !data.last_name) {
			showError('El nombre completo es requerido');
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
		if (!data.whatsapp) {
			showError('El WhatsApp es requerido');
			return false;
		}
		if (!data.desafio) {
			showError('El desafío es requerido');
			return false;
		}
		if (!data.plan) {
			showError('Selecciona un plan primero');
			return false;
		}
		if (!data.fecha) {
			showError('Selecciona una fecha');
			return false;
		}
		if (!data.hora) {
			showError('Selecciona una hora');
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

		ajaxData.append('action', 'avance_save_mentoria_booking');
		ajaxData.append('nonce', config.nonce);
		ajaxData.append('first_name', formData.first_name);
		ajaxData.append('last_name', formData.last_name);
		ajaxData.append('email', formData.email);
		ajaxData.append('whatsapp', formData.whatsapp);
		ajaxData.append('desafio', formData.desafio);
		ajaxData.append('plan', formData.plan);
		ajaxData.append('fecha', formData.fecha);
		ajaxData.append('hora', formData.hora);

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => response.json())
			.then(response => {
				config.isSubmitting = false;

				if (response.success && response.data) {
					showSuccess('Redirigiendo a checkout...');
					setTimeout(() => {
						window.location.href = response.data.redirect || '/checkout/';
					}, 800);
				} else {
					showError(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				config.isSubmitting = false;
				showError('Error de conexión. Intenta de nuevo.');
			});
	}

	function showError(message) {
		if (typeof MentoriaAlerts !== 'undefined' && MentoriaAlerts.showError) {
			MentoriaAlerts.showError(message);
		} else {
			alert(message);
		}
	}

	function showSuccess(message) {
		if (typeof MentoriaAlerts !== 'undefined' && MentoriaAlerts.showSuccess) {
			MentoriaAlerts.showSuccess(message);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
