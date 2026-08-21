/**
 * Mentoria Reservation Booking System
 * Manages plan selection, calendar, and booking form submission
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	const PLANS = [
		{
			id: 'entrada',
			wc_id: null,
			name: 'Entrada',
			meta: '4 sesiones · 60 min c/u',
			price: 'S/ 490',
			unit: 'mes',
			features: ['Sesiones individuales', 'Acceso a recursos básicos', 'Soporte vía email', 'Plan de acción inicial']
		},
		{
			id: 'pro',
			wc_id: null,
			name: 'Pro',
			meta: '8 sesiones · 60 min c/u',
			price: 'S/ 890',
			unit: 'mes',
			features: ['Todo lo del plan Entrada', 'Materiales personalizados', 'Grabación de sesiones', 'Revisión de métricas y resultados', 'Prioridad en agenda']
		},
		{
			id: 'puntual',
			wc_id: null,
			name: 'Sesión puntual',
			meta: '1 sesión · 90 min',
			price: 'S/ 180',
			unit: 'sesión',
			features: ['Una sesión completa', 'Diagnóstico inicial', 'Plan de acción específico', 'Seguimiento por correo']
		},
	];

	const WEEK_DAYS = ['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'];
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const TIME_SLOTS = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];

	const config = {
		ajaxUrl: (typeof avanceFormConfig !== 'undefined' && avanceFormConfig.ajaxUrl)
			? avanceFormConfig.ajaxUrl
			: '/wp-admin/admin-ajax.php',
		nonce: (typeof mentoriaConfig !== 'undefined' && mentoriaConfig.nonce) ? mentoriaConfig.nonce : '',
		isSubmitting: false,
	};

	let state = {
		selectedPlan: null,
		month: new Date().getMonth(),
		year: new Date().getFullYear(),
		selectedDay: null,
		selectedTime: null,
	};

	function init() {
		if (!config.nonce) {
			const submitBtn = document.getElementById('mentoriaSubmitBtn');
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Error: Recarga la página';
			}
			return;
		}
		renderAll();
		attachEventListeners();
	}

	function renderAll() {
		renderPlans();
		renderAgenda();
		renderCalendar();
	}

	function renderPlans() {
		const container = document.getElementById('mentoriaPlans');
		if (!container) return;

		container.innerHTML = PLANS.map(p => {
			const sel = p.id === state.selectedPlan;
			const featuresHtml = sel && p.features
				? `<div class="mentoria-reserva__features">${p.features.map(f => `<div><span class="mentoria-reserva__feature-check">✓</span><span>${f}</span></div>`).join('')}</div>`
				: '';
			return `<div class="mentoria-reserva__plan ${sel ? 'selected' : ''}" data-plan="${p.id}" role="button" tabindex="0">
				<div class="mentoria-reserva__plan-head">
					<div class="mentoria-reserva__plan-left">
						<input type="radio" class="mentoria-reserva__plan-radio" name="plan" ${sel ? 'checked' : ''} readonly aria-label="Seleccionar plan ${p.name}">
						<div class="mentoria-reserva__plan-info">
							<span class="mentoria-reserva__plan-name">${p.name}</span>
							<div class="mentoria-reserva__plan-meta">${p.meta}</div>
						</div>
					</div>
					<div class="mentoria-reserva__plan-price"><span class="mentoria-reserva__plan-price-amount">${p.price}</span><span class="mentoria-reserva__plan-price-unit">/${p.unit}</span></div>
				</div>
				${featuresHtml}
			</div>`;
		}).join('');

		container.querySelectorAll('.mentoria-reserva__plan').forEach(el => {
			el.addEventListener('click', handlePlanSelect);
			el.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					handlePlanSelect.call(el);
				}
			});
		});
	}

	function handlePlanSelect() {
		const planId = this.dataset.plan;
		if (planId === state.selectedPlan) {
			state.selectedPlan = null;
		} else {
			state.selectedPlan = planId;
		}
		renderAll();
	}

	function isMobile480() {
		return window.innerWidth <= 479;
	}

	function renderAgenda() {
		const plan = PLANS.find(p => p.id === state.selectedPlan);

		const agendaSub = document.getElementById('agendaSub');
		if (agendaSub) {
			agendaSub.textContent = plan ? `${plan.name} · ${plan.price}/${plan.unit}` : 'Selecciona un plan';
		}

		const submitBtn = document.getElementById('mentoriaSubmitBtn');
		if (submitBtn) {
			submitBtn.textContent = plan ? `Confirmar reserva · ${plan.price}/${plan.unit} →` : 'Selecciona un plan primero';
			submitBtn.disabled = !plan;
		}

		// Controlar visibilidad por pasos SOLO en 479px (mobile)
		const isMobile = isMobile480();
		if (!isMobile) {
			// NO aplicar lógica a otras resoluciones
			return;
		}

		const plansContainer = document.getElementById('mentoriaPlans');
		const sidebar = document.querySelector('.mentoria-reserva__sidebar');

		if (!sidebar) return;

		const cards = sidebar.querySelectorAll('.mentoria-reserva__card');

		if (!plan) {
			// Paso 1: Mostrar planes, ocultar tarjetas (479px)
			if (plansContainer) plansContainer.style.display = 'flex';
			cards.forEach(card => card.style.display = 'none');
		} else if (!state.selectedDay || !state.selectedTime) {
			// Paso 2: Ocultar planes, mostrar calendario (479px)
			if (plansContainer) plansContainer.style.display = 'none';
			if (cards[0]) cards[0].style.display = 'flex';
			if (cards[1]) cards[1].style.display = 'none';
		} else {
			// Paso 3: Ocultar calendario, mostrar formulario (479px)
			if (plansContainer) plansContainer.style.display = 'none';
			if (cards[0]) cards[0].style.display = 'none';
			if (cards[1]) cards[1].style.display = 'flex';
		}
	}

	function renderCalendar() {
		const monthLabel = document.getElementById('mentoriaMonthLabel');
		const dowContainer = document.getElementById('mentoriaDow');
		const daysContainer = document.getElementById('mentoriaDays');

		if (!monthLabel || !dowContainer || !daysContainer) return;

		monthLabel.textContent = `${MONTHS[state.month]} ${state.year}`;

		dowContainer.innerHTML = WEEK_DAYS.map(d => `<span class="mentoria-reserva__weekday">${d}</span>`).join('');

		const firstDow = (new Date(state.year, state.month, 1).getDay() + 6) % 7;
		const daysInMonth = new Date(state.year, state.month + 1, 0).getDate();
		let html = '';

		for (let i = 0; i < firstDow; i++) {
			html += `<button class="mentoria-reserva__day-btn" disabled></button>`;
		}

		for (let d = 1; d <= daysInMonth; d++) {
			const sel = d === state.selectedDay;
			html += `<button class="mentoria-reserva__day-btn ${sel ? 'selected' : ''}" data-day="${d}">${d}</button>`;
		}

		daysContainer.innerHTML = html;
		daysContainer.querySelectorAll('.mentoria-reserva__day-btn[data-day]').forEach(btn => {
			btn.addEventListener('click', () => {
				state.selectedDay = parseInt(btn.dataset.day, 10);
				state.selectedTime = null;
				renderCalendar();
				renderTimeSlots();
			});
		});
	}

	function renderTimeSlots() {
		let timeContainer = document.getElementById('mentoriaTimeSlots');

		if (!timeContainer) {
			const calendarSection = document.querySelector('.mentoria-reserva__calendar-section');
			if (!calendarSection) return;

			timeContainer = document.createElement('div');
			timeContainer.id = 'mentoriaTimeSlots';
			timeContainer.className = 'mentoria-reserva__time-slots';
			calendarSection.insertBefore(timeContainer, calendarSection.querySelector('.mentoria-reserva__tz'));
		}

		if (!state.selectedDay) {
			timeContainer.innerHTML = '';
			// Mostrar calendario cuando no hay día seleccionado
			const calNav = document.querySelector('.mentoria-reserva__cal-nav');
			const calWeekdays = document.querySelector('.mentoria-reserva__cal-weekdays');
			const calDays = document.querySelector('.mentoria-reserva__cal-days');
			if (calNav) calNav.style.display = 'flex';
			if (calWeekdays) calWeekdays.style.display = 'grid';
			if (calDays) calDays.style.display = 'grid';
			return;
		}

		// Ocultar calendario cuando hay día seleccionado
		const calNav = document.querySelector('.mentoria-reserva__cal-nav');
		const calWeekdays = document.querySelector('.mentoria-reserva__cal-weekdays');
		const calDays = document.querySelector('.mentoria-reserva__cal-days');
		if (calNav) calNav.style.display = 'none';
		if (calWeekdays) calWeekdays.style.display = 'none';
		if (calDays) calDays.style.display = 'none';

		timeContainer.innerHTML = `<div class="mentoria-reserva__time-label">Elige una hora:</div>
			${TIME_SLOTS.map(time => `
				<button class="mentoria-reserva__time-btn ${state.selectedTime === time ? 'selected' : ''}" data-time="${time}">
					${time}
				</button>
			`).join('')}`;

		timeContainer.querySelectorAll('.mentoria-reserva__time-btn').forEach(btn => {
			btn.addEventListener('click', () => {
				state.selectedTime = state.selectedTime === btn.dataset.time ? null : btn.dataset.time;
				renderTimeSlots();
				renderAgenda();
			});
		});
	}

	function attachEventListeners() {
		const prevBtn = document.getElementById('mentoriaPrevMonth');
		const nextBtn = document.getElementById('mentoriaNextMonth');
		const submitBtn = document.getElementById('mentoriaSubmitBtn');
		const whatsappBtn = document.querySelector('.mentoria-reserva__whatsapp-btn');

		if (prevBtn) {
			prevBtn.addEventListener('click', () => {
				state.month--;
				if (state.month < 0) {
					state.month = 11;
					state.year--;
				}
				renderCalendar();
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', () => {
				state.month++;
				if (state.month > 11) {
					state.month = 0;
					state.year++;
				}
				renderCalendar();
			});
		}

		if (submitBtn) {
			submitBtn.addEventListener('click', handleSubmit);
		}

		if (whatsappBtn) {
			whatsappBtn.addEventListener('click', handleWhatsAppButton);
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
		const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
		return {
			nombre: getFieldValue('mentoriaName'),
			whatsapp: getFieldValue('mentoriaWhatsapp'),
			email: getFieldValue('mentoriaEmail'),
			desafio: getFieldValue('mentoriaDesafio'),
			plan: state.selectedPlan,
			fecha: state.selectedDay ? `${state.selectedDay}/${state.month + 1}/${state.year}` : '',
			hora: state.selectedTime || '',
			payment_method: paymentMethod ? paymentMethod.value : 'visa',
		};
	}

	function getFieldValue(fieldId) {
		const field = document.getElementById(fieldId);
		return field ? field.value.trim() : '';
	}

	function validateFormData(data) {
		if (!data.plan) {
			showError('Selecciona un plan primero');
			return false;
		}
		if (!data.fecha) {
			showError('Selecciona una fecha para tu sesión');
			return false;
		}
		if (!data.hora) {
			showError('Selecciona una hora para tu sesión');
			return false;
		}
		if (!data.nombre) {
			showError('El nombre es requerido');
			return false;
		}
		if (!data.whatsapp) {
			showError('El número de WhatsApp es requerido');
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
		return true;
	}

	function isValidEmail(email) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return emailRegex.test(email);
	}

	function submitFormViaAjax(formData) {
		const ajaxData = new FormData();

		ajaxData.append('action', 'avance_mentoria_booking');
		ajaxData.append('nonce', config.nonce);
		ajaxData.append('nombre', formData.nombre);
		ajaxData.append('whatsapp', formData.whatsapp);
		ajaxData.append('email', formData.email);
		ajaxData.append('desafio', formData.desafio);
		ajaxData.append('plan', formData.plan);
		ajaxData.append('fecha', formData.fecha);
		ajaxData.append('hora', formData.hora);
		ajaxData.append('payment_method', formData.payment_method);

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

				if (response.success && response.data) {
					if (!response.data.checkout_url || typeof response.data.checkout_url !== 'string' || response.data.checkout_url.trim() === '') {
						showError('No se pudo obtener la URL de pago. Intenta de nuevo.');
						return;
					}
					showSuccess(response.data.message || 'Redirigiendo a pago...');
					setTimeout(() => {
						window.location.href = response.data.checkout_url;
					}, 1500);
				} else {
					showError(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				config.isSubmitting = false;
				showError('Error de conexión. Intenta de nuevo.');
			});
	}

	function handleWhatsAppButton(e) {
		e.preventDefault();
		const plan = PLANS.find(p => p.id === state.selectedPlan);
		if (!plan) {
			showError('Selecciona un plan primero');
			return;
		}
		const whatsappNumber = '51993508652';
		const message = encodeURIComponent(`Hola, me interesa el plan ${plan.name} (${plan.price}/${plan.unit}). Me gustaría consultar más detalles.`);
		const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;
		window.open(whatsappUrl, '_blank');
	}

	function resetForm() {
		document.getElementById('mentoriaName').value = '';
		document.getElementById('mentoriaWhatsapp').value = '';
		document.getElementById('mentoriaEmail').value = '';
		document.getElementById('mentoriaDesafio').value = '';
		state.selectedDay = null;
		renderCalendar();
	}

	function showError(message) {
		const container = document.querySelector('.mentoria-reserva__sidebar');
		if (!container) return;

		const errorDiv = document.createElement('div');
		errorDiv.style.cssText = 'background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 6px; margin-bottom: 12px; font-size: 13px; position: absolute; top: 0; left: 0; right: 0; z-index: 1000;';
		errorDiv.textContent = message;
		container.insertBefore(errorDiv, container.firstChild);
		setTimeout(() => errorDiv.remove(), 4000);
	}

	function showSuccess(message) {
		const container = document.querySelector('.mentoria-reserva__sidebar');
		if (!container) return;

		const successDiv = document.createElement('div');
		successDiv.style.cssText = 'background-color: #efe; border: 1px solid #cfc; color: #3c3; padding: 12px; border-radius: 6px; margin-bottom: 12px; font-size: 13px; position: absolute; top: 0; left: 0; right: 0; z-index: 1000;';
		successDiv.textContent = message;
		container.insertBefore(successDiv, container.firstChild);
		setTimeout(() => successDiv.remove(), 4000);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
