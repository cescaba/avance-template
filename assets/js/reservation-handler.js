/**
 * Reservation Handler - AJAX + Calendar
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	const config = {
		formId: 'reservation-form',
		ajaxUrl: typeof avanceReservationConfig !== 'undefined' ? avanceReservationConfig.ajaxUrl : '/wp-admin/admin-ajax.php',
		nonce: typeof avanceReservationConfig !== 'undefined' ? avanceReservationConfig.nonce : '',
		checkoutUrl: typeof avanceReservationConfig !== 'undefined' ? avanceReservationConfig.checkoutUrl : '/checkout/',
		isSubmitting: false,
	};

	const calendarState = {
		currentDate: new Date(),
		selectedDate: null,
	};

	// Inicializar calendario global
	window.avanceInitReservationCalendar = function() {
		renderCalendar();
	};

	// Inicializar formulario cuando carga el DOM
	function initForm() {
		const form = document.getElementById(config.formId);
		if (!form) {
			console.warn('[Avance Reservations] Form not found');
			return;
		}

		console.log('[Avance Reservations] Form initialized');
		form.addEventListener('submit', handleFormSubmit);
	}

	/**
	 * Render calendar
	 */
	function renderCalendar() {
		const calendarDiv = document.getElementById('reservation-calendar');
		if (!calendarDiv) return;

		const year = calendarState.currentDate.getFullYear();
		const month = calendarState.currentDate.getMonth();

		// Crear HTML del calendario
		let html = '<div class="reservation-cal-nav">';
		html += '<button type="button" class="reservation-cal-nav-btn" onclick="avanceCalendarPrev()">‹</button>';

		const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
			'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		html += '<div class="reservation-cal-month">' + monthNames[month] + ' ' + year + '</div>';

		html += '<button type="button" class="reservation-cal-nav-btn" onclick="avanceCalendarNext()">›</button>';
		html += '</div>';

		html += '<div class="reservation-cal-weekdays">';
		html += '<div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div>';
		html += '</div>';

		html += '<div class="reservation-cal-days">';

		// Días del mes anterior
		const firstDay = new Date(year, month, 1).getDay();
		const daysInMonth = new Date(year, month + 1, 0).getDate();
		const daysInPrevMonth = new Date(year, month, 0).getDate();

		for (let i = firstDay - 1; i >= 0; i--) {
			html += '<div class="reservation-cal-day is-empty">' + (daysInPrevMonth - i) + '</div>';
		}

		// Días del mes actual
		const today = new Date();
		const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

		for (let day = 1; day <= daysInMonth; day++) {
			const dateObj = new Date(year, month, day);
			const isDisabled = dateObj < todayDate;
			const isSelected = calendarState.selectedDate &&
				dateObj.toDateString() === calendarState.selectedDate.toDateString();

			let className = 'reservation-cal-day';
			if (isDisabled) className += ' is-disabled';
			if (isSelected) className += ' is-selected';

			if (isDisabled) {
				html += '<div class="' + className + '">' + day + '</div>';
			} else {
				html += '<div class="' + className + '" onclick="avanceSelectDate(new Date(' + year + ',' + month + ',' + day + '))">' + day + '</div>';
			}
		}

		// Días del próximo mes
		const nextMonthDays = 42 - (firstDay + daysInMonth);
		for (let day = 1; day <= nextMonthDays; day++) {
			html += '<div class="reservation-cal-day is-empty">' + day + '</div>';
		}

		html += '</div>';

		calendarDiv.innerHTML = html;
	}

	// Seleccionar fecha
	window.avanceSelectDate = function(date) {
		calendarState.selectedDate = date;
		updateFormDate();
		renderCalendar();
	};

	// Navegar calendario
	window.avanceCalendarPrev = function() {
		calendarState.currentDate.setMonth(calendarState.currentDate.getMonth() - 1);
		renderCalendar();
	};

	window.avanceCalendarNext = function() {
		calendarState.currentDate.setMonth(calendarState.currentDate.getMonth() + 1);
		renderCalendar();
	};

	/**
	 * Actualizar input de fecha
	 */
	function updateFormDate() {
		if (!calendarState.selectedDate) return;

		const year = calendarState.selectedDate.getFullYear();
		const month = String(calendarState.selectedDate.getMonth() + 1).padStart(2, '0');
		const day = String(calendarState.selectedDate.getDate()).padStart(2, '0');
		const dateString = year + '-' + month + '-' + day;

		const input = document.getElementById('reservation-fecha');
		if (input) {
			input.value = dateString;
		}
	}

	/**
	 * Manejar submit del formulario
	 */
	function handleFormSubmit(e) {
		e.preventDefault();

		if (config.isSubmitting) return;

		console.log('[Avance Reservations] Form submitted');

		const formData = getFormData();
		if (!validateFormData(formData)) {
			console.warn('[Avance Reservations] Validation failed');
			return;
		}

		config.isSubmitting = true;
		submitFormViaAjax(formData);
	}

	/**
	 * Obtener datos del formulario
	 */
	function getFormData() {
		return {
			product_id: getFieldValue('reservation-product-id'),
			fecha_reserva: getFieldValue('reservation-fecha'),
			nombre: getFieldValue('reservation-nombre'),
			email: getFieldValue('reservation-email'),
			whatsapp: getFieldValue('reservation-whatsapp'),
			notas: getFieldValue('reservation-notas'),
		};
	}

	/**
	 * Obtener valor del campo
	 */
	function getFieldValue(fieldId) {
		const field = document.getElementById(fieldId);
		return field ? field.value.trim() : '';
	}

	/**
	 * Validar datos del formulario
	 */
	function validateFormData(data) {
		if (!data.product_id) {
			showError('Debe seleccionar una mentoría');
			return false;
		}
		if (!data.nombre) {
			showError('El nombre es requerido');
			return false;
		}
		if (!data.email) {
			showError('El email es requerido');
			return false;
		}
		if (!data.whatsapp) {
			showError('El WhatsApp es requerido');
			return false;
		}
		if (!data.fecha_reserva) {
			showError('Debe seleccionar una fecha');
			return false;
		}
		return true;
	}

	/**
	 * Enviar datos vía AJAX
	 */
	function submitFormViaAjax(formData) {
		const ajaxData = new FormData();

		ajaxData.append('action', 'avance_add_reservation');
		ajaxData.append('nonce', config.nonce);
		ajaxData.append('product_id', formData.product_id);
		ajaxData.append('fecha_reserva', formData.fecha_reserva);
		ajaxData.append('nombre', formData.nombre);
		ajaxData.append('email', formData.email);
		ajaxData.append('whatsapp', formData.whatsapp);
		ajaxData.append('notas', formData.notas);

		console.log('[Avance Reservations] Sending AJAX...');

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => response.json())
			.then(response => {
				config.isSubmitting = false;

				console.log('[Avance Reservations] Response:', response);

				if (response.success) {
					showSuccess('¡Reserva confirmada! Redirigiendo al pago...');
					setTimeout(() => {
						window.location.href = response.data.checkout_url;
					}, 1500);
				} else {
					const errorMsg = response.data?.errors
						? response.data.errors.join('\n')
						: response.data?.message || 'Error al procesar la reserva';
					showError(errorMsg);
				}
			})
			.catch(error => {
				config.isSubmitting = false;
				console.error('[Avance Reservations] Error:', error);
				showError('Error de conexión. Intenta de nuevo.');
			});
	}

	/**
	 * Mostrar errores
	 */
	function showError(message) {
		alert(message);
	}

	/**
	 * Mostrar éxito
	 */
	function showSuccess(message) {
		console.log('[Avance Reservations] Success:', message);
	}

	// Inicializar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initForm);
	} else {
		initForm();
	}
})();
