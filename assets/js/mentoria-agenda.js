document.addEventListener('DOMContentLoaded', () => {
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const WEEK_DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
	const TIME_SLOTS = ['15:00', '15:30', '16:00', '16:30', '17:00'];

	const state = {
		viewYear: new Date().getFullYear(),
		viewMonth: new Date().getMonth(),
		selectedKey: null,
		selectedTime: null
	};

	const calGrid = document.getElementById('mentoriaAgendaCalGrid');
	const monthLabel = document.getElementById('mentoriaAgendaMonthLabel');
	const prevMonthBtn = document.getElementById('mentoriaAgendaPrevMonth');
	const nextMonthBtn = document.getElementById('mentoriaAgendaNextMonth');
	const timeContainer = document.getElementById('mentoriaAgendaTimeSlots');

	if (!calGrid || !monthLabel) {
		return;
	}

	function formatDateKey(year, month, day) {
		return String(year).padStart(4, '0') + '-' +
		       String(month + 1).padStart(2, '0') + '-' +
		       String(day).padStart(2, '0');
	}

	function daysInMonth(y, m) {
		return new Date(y, m + 1, 0).getDate();
	}

	function renderCalendar() {
		const { viewYear: y, viewMonth: m, selectedKey } = state;
		monthLabel.textContent = MONTHS[m] + ' ' + y;

		calGrid.querySelectorAll('.contacto-agenda__daybtn').forEach(el => el.remove());

		const today = new Date();
		const todayKey = formatDateKey(today.getFullYear(), today.getMonth(), today.getDate());
		const firstWeekday = new Date(y, m, 1).getDay();
		const total = daysInMonth(y, m);
		const frag = document.createDocumentFragment();

		for (let i = 0; i < firstWeekday; i++) {
			const pad = document.createElement('div');
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn-pad';
			frag.appendChild(pad);
		}

		for (let d = 1; d <= total; d++) {
			const key = formatDateKey(y, m, d);
			const currentDate = new Date(y, m, d);
			const weekday = currentDate.getDay();
			const isSunday = weekday === 0;
			const isSelected = key === selectedKey;
			const isToday = key === todayKey;
			const isPast = currentDate < today;

			const canSelect = !isPast && !isToday;

			const btn = document.createElement('button');
			btn.className = 'contacto-agenda__daybtn' + (isSunday ? ' sunday' : '') + (isSelected ? ' selected' : '') + (canSelect ? ' enabled-day' : ' disabled-day');
			btn.textContent = d;
			btn.dataset.key = key;

			if (!canSelect) {
				btn.disabled = true;
				btn.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
				});
			}

			if (canSelect) {
				btn.addEventListener('click', () => {
					state.selectedKey = key;
					state.selectedTime = null;
					const [year, month, day] = key.split('-').map(Number);
					window.mentoriaSelectedDate = { day, month: month - 1, year };
					window.mentoriaSelectedTime = null;
					renderCalendar();
					renderTimeSlots();
				});
			}
			frag.appendChild(btn);
		}

		const totalCells = firstWeekday + total;
		const trailing = (7 - (totalCells % 7)) % 7;
		for (let i = 0; i < trailing; i++) {
			const pad = document.createElement('div');
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn-pad';
			frag.appendChild(pad);
		}

		calGrid.appendChild(frag);
	}

	function renderTimeSlots() {
		if (!timeContainer) return;

		if (!state.selectedKey) {
			timeContainer.innerHTML = '';
			return;
		}

		const handleTimeSelect = function() {
			state.selectedTime = this.dataset.time;
			window.mentoriaSelectedTime = this.dataset.time;
			renderTimeSlots();
		};

		const slotsHTML = TIME_SLOTS
			.map(time => `<button class="contacto-agenda__time-btn" data-time="${time}">${time}</button>`)
			.join('');

		timeContainer.innerHTML = `<div class="contacto-agenda__time-wrapper">${slotsHTML}</div>`;

		timeContainer.querySelectorAll('.contacto-agenda__time-btn').forEach(btn => {
			btn.addEventListener('click', handleTimeSelect);
		});
	}

	if (prevMonthBtn) {
		prevMonthBtn.addEventListener('click', () => {
			const today = new Date();
			if (state.viewYear > today.getFullYear() ||
				(state.viewYear === today.getFullYear() && state.viewMonth > today.getMonth())) {
				state.viewMonth -= 1;
				if (state.viewMonth < 0) {
					state.viewMonth = 11;
					state.viewYear -= 1;
				}
				renderCalendar();
			}
		});
	}

	if (nextMonthBtn) {
		nextMonthBtn.addEventListener('click', () => {
			state.viewMonth += 1;
			if (state.viewMonth > 11) {
				state.viewMonth = 0;
				state.viewYear += 1;
			}
			renderCalendar();
		});
	}

	window.resetMentoriaCalendar = () => {
		state.selectedKey = null;
		state.selectedTime = null;
		window.mentoriaSelectedDate = null;
		window.mentoriaSelectedTime = null;
		renderCalendar();
		renderTimeSlots();
	};

	renderCalendar();
	renderTimeSlots();
});
