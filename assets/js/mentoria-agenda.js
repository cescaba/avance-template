document.addEventListener('DOMContentLoaded', () => {
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const WEEK_DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
	const TIME_SLOTS = ['09:00', '10:00', '11:00', '14:00', '15:00'];

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

	function daysInMonth(y, m) {
		return new Date(y, m + 1, 0).getDate();
	}

	function renderCalendar() {
		const { viewYear: y, viewMonth: m, selectedKey } = state;
		monthLabel.textContent = MONTHS[m] + ' ' + y;

		calGrid.querySelectorAll('.contacto-agenda__daybtn').forEach(el => el.remove());

		const today = new Date();
		const todayKey = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
		const firstWeekday = new Date(y, m, 1).getDay();
		const total = daysInMonth(y, m);
		const frag = document.createDocumentFragment();

		for (let i = 0; i < firstWeekday; i++) {
			const pad = document.createElement('div');
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn.pad';
			frag.appendChild(pad);
		}

		for (let d = 1; d <= total; d++) {
			const key = y + '-' + (m + 1) + '-' + d;
			const currentDate = new Date(y, m, d);
			const weekday = currentDate.getDay();
			const isSunday = weekday === 0;
			const isSelected = key === selectedKey;
			const isToday = key === todayKey;
			const isPast = currentDate < today;

			const canSelect = !isPast && !isSunday;

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
			} else if (isToday && !isSelected) {
				const dot = document.createElement('span');
				dot.className = 'dot';
				btn.appendChild(dot);
			}
			frag.appendChild(btn);
		}

		const totalCells = firstWeekday + total;
		const trailing = (7 - (totalCells % 7)) % 7;
		for (let i = 0; i < trailing; i++) {
			const pad = document.createElement('div');
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn.pad';
			frag.appendChild(pad);
		}

		calGrid.appendChild(frag);
	}

	function formatDate(dateStr) {
		const [year, month, day] = dateStr.split('-').map(Number);
		const date = new Date(year, month - 1, day);
		const dayName = WEEK_DAYS[date.getDay()];
		const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		return `${dayName}, ${day} de ${monthNames[month - 1]} ${year}`;
	}

	function renderTimeSlots() {
		if (!timeContainer) return;

		const calHeader = document.querySelector('.contacto-agenda__cal-header');
		const calWeekdays = document.querySelector('.contacto-agenda__cal-weekdays');
		const calDays = document.querySelector('.contacto-agenda__cal-grid');

		if (!state.selectedKey) {
			timeContainer.innerHTML = '';
			if (calHeader) calHeader.style.display = 'flex';
			if (calWeekdays) calWeekdays.style.display = 'grid';
			if (calDays) calDays.style.display = 'grid';
			return;
		}

		if (calHeader) calHeader.style.display = 'none';
		if (calWeekdays) calWeekdays.style.display = 'none';
		if (calDays) calDays.style.display = 'none';

		if (!state.selectedTime) {
			timeContainer.innerHTML = `<div class="contacto-agenda__time-wrapper">
				<div class="contacto-agenda__time-label">
					<svg class="contacto-agenda__time-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M15 19l-7-7 7-7"></path>
					</svg>
					<span>Elige una hora</span>
				</div>
				<div class="contacto-agenda__time-grid">
					${TIME_SLOTS.map(time => {
						const hour = parseInt(time.split(':')[0]);
						const endHour = hour.toString().padStart(2, '0') + ':30';
						return `
							<button class="contacto-agenda__time-btn" data-time="${time}">
								${time} - ${endHour}
							</button>
						`;
					}).join('')}
				</div>
			</div>`;

			const timeLabel = timeContainer.querySelector('.contacto-agenda__time-label');
			if (timeLabel) {
				timeLabel.addEventListener('click', () => {
					state.selectedKey = null;
					state.selectedTime = null;
					renderCalendar();
					renderTimeSlots();
				});
			}

			timeContainer.querySelectorAll('.contacto-agenda__time-btn').forEach(btn => {
				btn.addEventListener('click', () => {
					state.selectedTime = btn.dataset.time;
					window.mentoriaSelectedTime = btn.dataset.time;
					renderTimeSlots();
				});
			});
		} else {
			const formattedDate = formatDate(state.selectedKey);
			const hour = state.selectedTime;
			const endHour = parseInt(hour.split(':')[0]).toString().padStart(2, '0') + ':30';

			timeContainer.innerHTML = `
				<div class="contacto-agenda__summary">
					<div class="contacto-agenda__summary-row">
						<div class="contacto-agenda__summary-label">FECHA SELECCIONADA</div>
						<div class="contacto-agenda__summary-value">${formattedDate}</div>
					</div>
					<div class="contacto-agenda__summary-row">
						<div class="contacto-agenda__summary-label">HORA SELECCIONADA</div>
						<div class="contacto-agenda__summary-value">${hour} - ${endHour}</div>
					</div>
				</div>
				<div class="contacto-agenda__card-action">
					<button class="contacto-agenda__change-btn">Seleccionar otra hora</button>
					<button class="contacto-agenda__form-btn" type="button" id="mentoriaFormButton">Continuar</button>
				</div>
			`;

			const changeBtn = timeContainer.querySelector('.contacto-agenda__card-action .contacto-agenda__change-btn');
			if (changeBtn) {
				changeBtn.addEventListener('click', () => {
					state.selectedTime = null;
					renderTimeSlots();
				});
			}

			const formBtn = timeContainer.querySelector('.contacto-agenda__form-btn');
			if (formBtn) {
				formBtn.addEventListener('click', handleFormButtonClick);
			}
		}
	}

	function handleFormButtonClick(e) {
		e.preventDefault();

		const sidebar = document.querySelector('.mentoria-reserva__sidebar');
		if (!sidebar) return;

		const cards = sidebar.querySelectorAll('.mentoria-reserva__card');
		const calendarCard = cards[0];
		const formCard = cards[1];

		if (calendarCard) {
			calendarCard.style.display = 'none';
		}

		if (formCard) {
			// Mostrar el card si está oculto
			formCard.style.display = 'flex';

			// Forzar reflow
			formCard.offsetHeight;

			// Scroll suave hacia el card
			setTimeout(() => {
				formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}, 100);
		}
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

	renderCalendar();

	// Back button handler
	const backBtn = document.getElementById('mentoriaBackBtn');
	if (backBtn) {
		backBtn.addEventListener('click', () => {
			const sidebar = document.querySelector('.mentoria-reserva__sidebar');
			if (sidebar) {
				const cards = sidebar.querySelectorAll('.mentoria-reserva__card');
				const calendarCard = cards[0];
				const formCard = cards[1];

				if (formCard) {
					formCard.style.display = 'none';
				}

				if (calendarCard) {
					calendarCard.style.display = 'flex';
					calendarCard.offsetHeight;
					setTimeout(() => {
						calendarCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
					}, 100);
				}
			}
		});
	}
});
