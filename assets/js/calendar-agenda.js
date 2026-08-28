document.addEventListener('DOMContentLoaded', () => {
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const WEEK_DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
	const TIME_SLOTS = ['09:00', '10:00', '11:00', '14:00', '15:00'];

	const calendarSection = document.querySelector('[id$="scheduling-section"], [id$="mentoriaAgendaTimeSlots"]') || document.querySelector('section.contacto-agenda');
	const calGrid = calendarSection?.querySelector('[id$="CalGrid"]');
	const monthLabel = calendarSection?.querySelector('[id$="MonthLabel"]');
	const prevMonthBtn = calendarSection?.querySelector('[id$="PrevMonth"]');
	const nextMonthBtn = calendarSection?.querySelector('[id$="NextMonth"]');
	const timeContainer = calendarSection?.querySelector('[id$="TimeSlots"]');

	if (!calGrid || !monthLabel) {
		return;
	}

	const calendarType = prevMonthBtn?.id?.includes('mentoria') ? 'mentoria' : 'contacto';

	const state = {
		viewYear: new Date().getFullYear(),
		viewMonth: new Date().getMonth(),
		selectedKey: null,
		selectedTime: null
	};

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
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn-pad';
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
					const dateObj = { day, month: month - 1, year };

					if (calendarType === 'mentoria') {
						window.mentoriaSelectedDate = dateObj;
						window.mentoriaSelectedTime = null;
					} else {
						window.contactoSelectedDate = dateObj;
						window.contactoSelectedTime = null;
					}

					renderCalendar();
					renderTimeSlots();
					if (window.updateSubmitButtonState) window.updateSubmitButtonState();
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
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn-pad';
			frag.appendChild(pad);
		}

		calGrid.appendChild(frag);
	}

	function formatDate(dateStr) {
		const [year, month, day] = dateStr.split('-').map(Number);
		const date = new Date(year, month - 1, day);
		const dayName = WEEK_DAYS[date.getDay()];
		return `${dayName}, ${day} de ${MONTHS[month - 1]} ${year}`;
	}

	function renderTimeSlots() {
		const tc = timeContainer || document.querySelector('.contacto-agenda__time-slots');
		if (!tc) return;

		const calHeader = document.querySelector('.contacto-agenda__cal-header');
		const calDays = document.querySelector('.contacto-agenda__cal-grid');

		if (!state.selectedKey) {
			tc.innerHTML = '';
			if (calHeader) calHeader.style.display = 'flex';
			if (calDays) calDays.style.display = 'grid';
			return;
		}

		if (calHeader) calHeader.style.display = 'none';
		if (calDays) calDays.style.display = 'none';

		if (!state.selectedTime) {
			tc.innerHTML = `<div class="contacto-agenda__time-wrapper">
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

			const timeLabel = tc.querySelector('.contacto-agenda__time-label');
			if (timeLabel) {
				timeLabel.addEventListener('click', () => {
					state.selectedKey = null;
					state.selectedTime = null;
					renderCalendar();
					renderTimeSlots();
				});
			}

			tc.querySelectorAll('.contacto-agenda__time-btn').forEach(btn => {
				btn.addEventListener('click', () => {
					state.selectedTime = btn.dataset.time;
					if (calendarType === 'mentoria') {
						window.mentoriaSelectedTime = btn.dataset.time;
					} else {
						window.contactoSelectedTime = btn.dataset.time;
					}
					renderTimeSlots();
				});
			});
		} else {
			const formattedDate = formatDate(state.selectedKey);
			const hour = state.selectedTime;
			const endHour = parseInt(hour.split(':')[0]).toString().padStart(2, '0') + ':30';

			tc.innerHTML = `
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
					<button class="contacto-agenda__form-btn" type="button">Continuar</button>
				</div>
			`;

			const changeBtn = tc.querySelector('.contacto-agenda__card-action .contacto-agenda__change-btn');
			if (changeBtn) {
				changeBtn.addEventListener('click', () => {
					state.selectedTime = null;
					renderTimeSlots();
				});
			}

			const formBtn = tc.querySelector('.contacto-agenda__form-btn');
			if (formBtn) {
				formBtn.addEventListener('click', (e) => {
					e.preventDefault();
					if (calendarType === 'mentoria') {
						const calendarCard = document.getElementById('mentoriaCalendarCard');
						const formCard = document.getElementById('mentoriaFormCard');
						if (calendarCard) {
							calendarCard.classList.add('is-hidden');
							calendarCard.classList.remove('is-visible');
						}
						if (formCard) {
							formCard.classList.remove('is-hidden');
							formCard.classList.add('is-visible');
						}
					} else {
						const rightSection = document.querySelector('.contacto-agenda__right');
						const leftSection = document.querySelector('.contacto-agenda__left');
						if (rightSection && leftSection) {
							rightSection.classList.add('is-visible');
							leftSection.classList.add('is-hidden');
							setTimeout(() => updateGridHeightMobile(), 10);
						}
					}
				});
			}
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

	const backBtn = document.getElementById('mentoriaBackBtn') || document.getElementById('contacto-agenda-back');
	if (backBtn) {
		backBtn.addEventListener('click', () => {
			if (calendarType === 'mentoria') {
				const calendarCard = document.getElementById('mentoriaCalendarCard');
				const formCard = document.getElementById('mentoriaFormCard');
				if (formCard) {
					formCard.classList.add('is-hidden');
					formCard.classList.remove('is-visible');
				}
				if (calendarCard) {
					calendarCard.classList.remove('is-hidden');
					calendarCard.classList.add('is-visible');
				}
			} else {
				const rightSection = document.querySelector('.contacto-agenda__right');
				const leftSection = document.querySelector('.contacto-agenda__left');
				if (rightSection && leftSection) {
					rightSection.classList.remove('is-visible');
					leftSection.classList.remove('is-hidden');
					setTimeout(() => updateGridHeightMobile(), 10);
				}
			}
		});
	}

	function updateGridHeightMobile() {
		if (window.innerWidth <= 479 && calendarType === 'contacto') {
			const grid = document.querySelector('.contacto-agenda__grid');
			const rightSection = document.querySelector('.contacto-agenda__right');
			const leftSection = document.querySelector('.contacto-agenda__left');

			if (rightSection?.classList.contains('is-visible') && grid) {
				grid.style.minHeight = rightSection.offsetHeight + 'px';
			} else if (leftSection && grid) {
				grid.style.minHeight = leftSection.offsetHeight + 'px';
			}
		}
	}

	function initializeMobileState() {
		if (window.innerWidth <= 479 && calendarType === 'contacto') {
			const rightSection = document.querySelector('.contacto-agenda__right');
			const leftSection = document.querySelector('.contacto-agenda__left');
			if (rightSection && leftSection) {
				rightSection.classList.add('is-hidden');
				leftSection.classList.remove('is-hidden');
				updateGridHeightMobile();
			}
		}
	}

	initializeMobileState();
	renderCalendar();
});
