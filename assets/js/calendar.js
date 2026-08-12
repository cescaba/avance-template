/**
 * Calendar - Interactive Date Picker
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	class AppointmentCalendar {
		constructor() {
			this.currentDate = new Date();
			this.selectedDate = null;
			this.selectedTime = '09:00';
		}

		init() {
			this.createCalendarUI();
			this.attachEventListeners();
		}

		createCalendarUI() {
			const calendar = document.getElementById('appointment-calendar');
			if (!calendar) return;

			const html = `
				<div class="appointment-calendar-container">
					<div class="appointment-calendar-header">
						<button class="calendar-prev" type="button">←</button>
						<h3 class="calendar-month"></h3>
						<button class="calendar-next" type="button">→</button>
					</div>
					<div class="calendar-weekdays">
						<div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sab</div>
					</div>
					<div class="calendar-days"></div>
					<div class="appointment-time-picker">
						<label>Hora:</label>
						<input type="time" id="appointment-time" value="09:00" min="08:00" max="18:00">
					</div>
				</div>
			`;

			calendar.innerHTML = html;
			this.renderCalendar();
		}

		renderCalendar() {
			const year = this.currentDate.getFullYear();
			const month = this.currentDate.getMonth();

			// Actualizar título
			const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
				'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
			document.querySelector('.calendar-month').textContent = `${monthNames[month]} ${year}`;

			// Obtener días
			const firstDay = new Date(year, month, 1).getDay();
			const daysInMonth = new Date(year, month + 1, 0).getDate();
			const daysInPrevMonth = new Date(year, month, 0).getDate();

			const daysContainer = document.querySelector('.calendar-days');
			daysContainer.innerHTML = '';

			// Días del mes anterior
			for (let i = firstDay - 1; i >= 0; i--) {
				const div = document.createElement('div');
				div.className = 'calendar-day other-month';
				div.textContent = daysInPrevMonth - i;
				daysContainer.appendChild(div);
			}

			// Días del mes actual
			const today = new Date();
			for (let day = 1; day <= daysInMonth; day++) {
				const div = document.createElement('div');
				const dateObj = new Date(year, month, day);

				div.className = 'calendar-day';
				div.textContent = day;

				// Deshabilitar fechas pasadas
				if (dateObj < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
					div.classList.add('disabled');
				} else {
					div.addEventListener('click', () => this.selectDate(dateObj));
				}

				if (this.selectedDate &&
					dateObj.toDateString() === this.selectedDate.toDateString()) {
					div.classList.add('selected');
				}

				daysContainer.appendChild(div);
			}

			// Días del próximo mes
			const nextMonthDays = 42 - (firstDay + daysInMonth);
			for (let day = 1; day <= nextMonthDays; day++) {
				const div = document.createElement('div');
				div.className = 'calendar-day other-month';
				div.textContent = day;
				daysContainer.appendChild(div);
			}
		}

		selectDate(date) {
			this.selectedDate = date;
			this.updateFormDate();
			this.renderCalendar();
		}

		updateFormDate() {
			if (!this.selectedDate) return;

			const year = this.selectedDate.getFullYear();
			const month = String(this.selectedDate.getMonth() + 1).padStart(2, '0');
			const day = String(this.selectedDate.getDate()).padStart(2, '0');
			const dateString = `${year}-${month}-${day}`;

			const input = document.getElementById('appointment-fecha');
			if (input) {
				input.value = dateString;
			}
		}

		attachEventListeners() {
			const prevBtn = document.querySelector('.calendar-prev');
			const nextBtn = document.querySelector('.calendar-next');
			const timeInput = document.getElementById('appointment-time');

			if (prevBtn) {
				prevBtn.addEventListener('click', () => {
					this.currentDate.setMonth(this.currentDate.getMonth() - 1);
					this.renderCalendar();
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', () => {
					this.currentDate.setMonth(this.currentDate.getMonth() + 1);
					this.renderCalendar();
				});
			}

			if (timeInput) {
				timeInput.addEventListener('change', (e) => {
					this.selectedTime = e.target.value;
					const horaInput = document.getElementById('appointment-hora');
					if (horaInput) {
						horaInput.value = this.selectedTime;
					}
				});
			}
		}
	}

	// Inicializar cuando el DOM esté listo
	document.addEventListener('DOMContentLoaded', () => {
		const calendar = new AppointmentCalendar();
		calendar.init();
	});
})();
