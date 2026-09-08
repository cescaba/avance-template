document.addEventListener('DOMContentLoaded', async () => {
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const WEEK_DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
	const TIME_SLOTS = ['15:00', '15:30', '16:00', '16:30', '17:00'];
	const AJAX_URL = '/wp-admin/admin-ajax.php';

	const calGrid = document.querySelector('[id$="CalGrid"]');
	const monthLabel = document.querySelector('[id$="MonthLabel"]');
	const prevMonthBtn = document.querySelector('[id$="PrevMonth"]');
	const nextMonthBtn = document.querySelector('[id$="NextMonth"]');
	const timeContainer = document.querySelector('[id$="TimeSlots"]');

	if (!calGrid || !monthLabel) {
		return;
	}

	const calendarType = prevMonthBtn?.id?.includes('mentoria') ? 'mentoria' : 'contacto';

	const state = {
		viewYear: new Date().getFullYear(),
		viewMonth: new Date().getMonth(),
		selectedKey: null,
		selectedTime: null,
		hoursCache: {} // Caché local para horas booked
	};

	function formatDateKey(year, month, day) {
		return String(year).padStart(4, '0') + '-' +
		       String(month + 1).padStart(2, '0') + '-' +
		       String(day).padStart(2, '0');
	}

	function daysInMonth(y, m) {
		return new Date(y, m + 1, 0).getDate();
	}

	async function renderCalendar() {
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
				btn.addEventListener('click', async () => {
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

					await renderCalendar();
					renderTimeSlots();
					if (window.updateSubmitButtonState) window.updateSubmitButtonState();
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

	function formatDate(dateStr) {
		const [year, month, day] = dateStr.split('-').map(Number);
		const date = new Date(year, month - 1, day);
		const dayName = WEEK_DAYS[date.getDay()];
		return `${dayName}, ${day} de ${MONTHS[month - 1]} ${year}`;
	}

	async function renderTimeSlots() {
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
			// Renderizar horas INMEDIATAMENTE (sin esperar AJAX)
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
						const minute = time.split(':')[1];
						const endHour = parseInt(minute) === 30 ? (hour + 1).toString().padStart(2, '0') + ':00' : hour.toString().padStart(2, '0') + ':30';
						return `
							<button class="contacto-agenda__time-btn"
									data-time="${time}">
								${time} - ${endHour}
							</button>
						`;
					}).join('')}
				</div>
			</div>`;

			// Obtener horas booked en BACKGROUND (sin bloquear UI)
			getBookedHours(state.selectedKey).then(bookedHours => {
				if (bookedHours.length > 0) {
					// Remover del DOM las horas ocupadas
					bookedHours.forEach(time => {
						const btn = tc.querySelector(`[data-time="${time}"]`);
						if (btn) btn.remove();
					});

					// Si no hay horas disponibles
					if (tc.querySelectorAll('.contacto-agenda__time-btn').length === 0) {
						const label = tc.querySelector('.contacto-agenda__time-label span');
						if (label) label.textContent = 'No hay disponibilidad este día';
					}
				}
			});

			// Función para agregar event listeners a botones
			const attachButtonListeners = () => {
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
			};

			// Agregar listeners inmediatamente
			attachButtonListeners();

			const timeLabel = tc.querySelector('.contacto-agenda__time-label');
			if (timeLabel) {
				timeLabel.addEventListener('click', () => {
					state.selectedKey = null;
					state.selectedTime = null;
					renderCalendar();
					renderTimeSlots();
				});
			}
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
		prevMonthBtn.addEventListener('click', async () => {
			const today = new Date();
			if (state.viewYear > today.getFullYear() ||
				(state.viewYear === today.getFullYear() && state.viewMonth > today.getMonth())) {
				state.viewMonth -= 1;
				if (state.viewMonth < 0) {
					state.viewMonth = 11;
					state.viewYear -= 1;
				}
				await renderCalendar();
			}
		});
	}

	if (nextMonthBtn) {
		nextMonthBtn.addEventListener('click', async () => {
			state.viewMonth += 1;
			if (state.viewMonth > 11) {
				state.viewMonth = 0;
				state.viewYear += 1;
			}
			await renderCalendar();
		});
	}

	const backBtn = document.getElementById('mentoriaBackBtn') || document.getElementById('contacto-agenda-back');
	if (backBtn) {
		backBtn.addEventListener('click', async () => {
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

	async function getBookedHours(fecha) {
		// Retornar del caché si ya existe
		if (state.hoursCache[fecha]) {
			return state.hoursCache[fecha];
		}

		try {
			const action = calendarType === 'mentoria' ? 'avance_get_mentoria_hours' : 'avance_get_available_hours';
			const timestamp = new Date().getTime();
			const response = await fetch(`${AJAX_URL}?action=${action}&fecha=${fecha}&t=${timestamp}`, {
				cache: 'no-store'
			});
			const data = await response.json();
			const booked = data.success ? (data.data?.booked_hours || []) : [];

			// Guardar en caché
			state.hoursCache[fecha] = booked;

			return booked;
		} catch (error) {
			console.error('Error fetching booked hours:', error);
			return [];
		}
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

	// Función global para resetear calendario desde otros scripts
	window.resetContactoCalendar = async function() {
		if (calendarType === 'contacto') {
			state.selectedKey = null;
			state.selectedTime = null;
			state.hoursCache = {};
			window.contactoSelectedDate = null;
			window.contactoSelectedTime = null;
			await renderCalendar();
			renderTimeSlots();
		}
	};

	initializeMobileState();
	await renderCalendar();
});
