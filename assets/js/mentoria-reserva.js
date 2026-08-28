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
			price: 'S/ 800',
			unit: 'mes',
			features: ['Sesiones individuales', 'Acceso a recursos básicos', 'Soporte vía email', 'Plan de acción inicial']
		},
		{
			id: 'pro',
			wc_id: null,
			name: 'Pro',
			meta: '8 sesiones · 60 min c/u',
			price: 'S/ 1200',
			unit: 'mes',
			features: ['Todo lo del plan Entrada', 'Materiales personalizados', 'Grabación de sesiones', 'Revisión de métricas y resultados', 'Prioridad en agenda']
		},
		{
			id: 'puntual',
			wc_id: null,
			name: 'Sesión puntual',
			meta: '1 sesión · 90 min',
			price: 'S/ 250',
			unit: 'sesión',
			features: ['Una sesión completa', 'Diagnóstico inicial', 'Plan de acción específico', 'Seguimiento por correo']
		},
	];

	const WEEK_DAYS = ['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'];
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const TIME_SLOTS = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];

	const DOM_SELECTORS = {
		plansContainer: '#mentoriaPlans',
		calendarCard: '#mentoriaCalendarCard',
		formCard: '#mentoriaFormCard',
		continueBtn: '#mentoriaContinueBtn',
		agendaSub: '#agendaSub',
		submitBtn: '#mentoriaSubmitBtn',
		monthLabel: '#mentoriaMonthLabel',
		dowContainer: '#mentoriaDow',
		daysContainer: '#mentoriaDays',
		timeSlots: '#mentoriaTimeSlots',
		prevMonth: '#mentoriaPrevMonth',
		nextMonth: '#mentoriaNextMonth',
		whatsappBtn: '.mentoria-reserva__whatsapp-btn',
		calNav: '.mentoria-reserva__cal-nav',
		calWeekdays: '.mentoria-reserva__cal-weekdays',
		calDays: '.mentoria-reserva__cal-days',
		calendarSection: '.mentoria-reserva__calendar-section',
		sidebar: '.mentoria-reserva__sidebar',
	};

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

		// Renderizar botón SOLO en mobile
		if (isMobile480()) {
			renderContinueButton();
		}
	}

	function renderContinueButton() {
		let continueBtn = document.getElementById('mentoriaContinueBtn');
		const isMobile = isMobile480();

		if (isMobile) {
			// Crear botón SOLO en mobile
			if (!continueBtn) {
				const container = document.getElementById('mentoriaPlans');
				if (!container) return;

				continueBtn = document.createElement('button');
				continueBtn.type = 'button';
				continueBtn.id = 'mentoriaContinueBtn';
				continueBtn.className = 'mentoria-reserva__continue-btn';
				continueBtn.textContent = 'Continuar reserva';
				container.parentNode.insertBefore(continueBtn, container.nextSibling);

				// Agregar event listener SOLO UNA VEZ
				continueBtn.addEventListener('click', () => {
					if (state.selectedPlan) {
						const calendarCard = document.getElementById('mentoriaCalendarCard');
						const plansContainer = document.getElementById('mentoriaPlans');
						if (plansContainer) plansContainer.classList.add('is-hidden');
						if (calendarCard) {
							calendarCard.classList.remove('is-hidden');
							calendarCard.classList.add('is-visible');
							setTimeout(() => {
								calendarCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
							}, 350);
						}
						continueBtn.style.display = 'none';
					}
				});
			}

			// Actualizar estado del botón en mobile
			if (continueBtn) {
				continueBtn.disabled = !state.selectedPlan;
				if (state.selectedPlan && !state.selectedDay && !state.selectedTime) {
					continueBtn.style.display = 'block';
				}
			}
		} else {
			// En desktop: remover botón si existe
			if (continueBtn) {
				continueBtn.remove();
			}
		}
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

	function toggleElement(selector, show) {
		const el = document.querySelector(selector);
		if (!el) return;
		if (show) {
			el.classList.remove('is-hidden');
			el.classList.add('is-visible');
		} else {
			el.classList.add('is-hidden');
			el.classList.remove('is-visible');
		}
	}

	function getElement(selector) {
		return document.querySelector(selector);
	}

	function setElementText(selector, text) {
		const el = getElement(selector);
		if (el) el.textContent = text;
	}

	function renderAgenda() {
		const plan = PLANS.find(p => p.id === state.selectedPlan);

		setElementText(DOM_SELECTORS.agendaSub, plan ? `${plan.name} · ${plan.price}/${plan.unit}` : 'Selecciona un plan');

		const submitBtn = getElement(DOM_SELECTORS.submitBtn);
		if (submitBtn) {
			submitBtn.textContent = plan ? `Confirmar reserva · ${plan.price}/${plan.unit} →` : 'Selecciona un plan primero';
			submitBtn.disabled = !plan;
		}

		if (!plan) {
			toggleElement(DOM_SELECTORS.plansContainer, true);
			toggleElement(DOM_SELECTORS.calendarCard, false);
			toggleElement(DOM_SELECTORS.formCard, false);
		} else if (!state.selectedDay || !state.selectedTime) {
			if (isMobile480()) {
				toggleElement(DOM_SELECTORS.plansContainer, true);
				toggleElement(DOM_SELECTORS.calendarCard, false);
				toggleElement(DOM_SELECTORS.formCard, false);
			} else {
				toggleElement(DOM_SELECTORS.plansContainer, true);
				toggleElement(DOM_SELECTORS.calendarCard, true);
				toggleElement(DOM_SELECTORS.formCard, true);
			}
		} else {
			toggleElement(DOM_SELECTORS.plansContainer, false);
			toggleElement(DOM_SELECTORS.calendarCard, false);
			toggleElement(DOM_SELECTORS.formCard, true);
			const continueBtn = getElement(DOM_SELECTORS.continueBtn);
			if (continueBtn && isMobile480()) {
				continueBtn.style.display = 'none';
			}
		}
	}

	function renderCalendar() {
		const monthLabel = getElement(DOM_SELECTORS.monthLabel);
		const dowContainer = getElement(DOM_SELECTORS.dowContainer);
		const daysContainer = getElement(DOM_SELECTORS.daysContainer);

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

	function toggleCalendarDisplay(show) {
		const elements = [DOM_SELECTORS.calNav, DOM_SELECTORS.calWeekdays, DOM_SELECTORS.calDays];
		elements.forEach(selector => {
			const el = getElement(selector);
			if (el) el.style.display = show ? (selector === DOM_SELECTORS.calNav ? 'flex' : 'grid') : 'none';
		});
	}

	function renderTimeSlots() {
		let timeContainer = getElement(DOM_SELECTORS.timeSlots);

		if (!timeContainer) {
			const calendarSection = getElement(DOM_SELECTORS.calendarSection);
			if (!calendarSection) return;

			timeContainer = document.createElement('div');
			timeContainer.id = 'mentoriaTimeSlots';
			timeContainer.className = 'mentoria-reserva__time-slots';
			const tz = calendarSection.querySelector('.mentoria-reserva__tz');
			calendarSection.insertBefore(timeContainer, tz);
		}

		if (!state.selectedDay) {
			timeContainer.innerHTML = '';
			toggleCalendarDisplay(true);
			return;
		}

		toggleCalendarDisplay(false);

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

	function changeMonth(offset) {
		state.month += offset;
		if (state.month < 0) {
			state.month = 11;
			state.year--;
		} else if (state.month > 11) {
			state.month = 0;
			state.year++;
		}
		renderCalendar();
	}

	function attachEventListeners() {
		const prevBtn = getElement(DOM_SELECTORS.prevMonth);
		const nextBtn = getElement(DOM_SELECTORS.nextMonth);
		const submitBtn = getElement(DOM_SELECTORS.submitBtn);
		const whatsappBtn = getElement(DOM_SELECTORS.whatsappBtn);

		if (prevBtn) prevBtn.addEventListener('click', () => changeMonth(-1));
		if (nextBtn) nextBtn.addEventListener('click', () => changeMonth(1));
		if (submitBtn) submitBtn.addEventListener('click', handleSubmit);
		if (whatsappBtn) whatsappBtn.addEventListener('click', handleWhatsAppButton);
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
		const selectedDate = window.mentoriaSelectedDate || state;
		const selectedTime = window.mentoriaSelectedTime || state.selectedTime;
		return {
			nombre: getFieldValue('mentoriaName'),
			whatsapp: getFieldValue('mentoriaWhatsapp'),
			email: getFieldValue('mentoriaEmail'),
			desafio: getFieldValue('mentoriaDesafio'),
			plan: state.selectedPlan,
			fecha: selectedDate.day ? `${selectedDate.day}/${selectedDate.month + 1}/${selectedDate.year}` : '',
			hora: selectedTime || '',
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

	function showMessage(message, type = 'error') {
		const container = getElement(DOM_SELECTORS.sidebar);
		if (!container) return;

		const messageDiv = document.createElement('div');
		messageDiv.className = `mentoria-reserva__message mentoria-reserva__message--${type}`;
		messageDiv.textContent = message;
		messageDiv.setAttribute('role', 'alert');
		container.insertBefore(messageDiv, container.firstChild);
		setTimeout(() => messageDiv.remove(), 4000);
	}

	function showError(message) {
		showMessage(message, 'error');
	}

	function showSuccess(message) {
		showMessage(message, 'success');
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
