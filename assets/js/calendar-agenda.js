document.addEventListener('DOMContentLoaded', async () => {
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const WEEK_DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
	const TIME_SLOTS = ['15:00', '15:30', '16:00', '16:30', '17:00'];

	// Buscar elementos con selectores más específicos
	const calGrid = document.querySelector('[id*="CalGrid"]') || document.querySelector('.contacto-agenda__cal-grid');
	const monthLabel = document.querySelector('[id*="MonthLabel"]') || document.querySelector('.contacto-agenda__cal-month-label');
	const prevMonthBtn = document.querySelector('[id*="PrevMonth"]');
	const nextMonthBtn = document.querySelector('[id*="NextMonth"]');
	const timeContainer = document.querySelector('[id*="TimeSlots"]') || document.querySelector('.contacto-agenda__time-slots');

	if (!calGrid || !monthLabel) {
		console.warn('Calendar elements not found:', { calGrid: !!calGrid, monthLabel: !!monthLabel });
		return;
	}

	const calendarType = prevMonthBtn?.id?.includes('mentoria') ? 'mentoria' : 'contacto';

	const state = {
		viewYear: new Date().getFullYear(),
		viewMonth: new Date().getMonth(),
		selectedKey: null,
		selectedTime: null,
		hoursCache: {}, // {fecha: [horas]}
		cacheTTL: {}, // {fecha: timestamp} - cuándo se cacheó
		CACHE_DURATION: 5 * 60 * 1000 // 5 minutos
	};

	// Limpiar selecciones previas en variables globales
	if (calendarType === 'contacto') {
		window.contactoSelectedDate = null;
		window.contactoSelectedTime = null;
	} else {
		window.mentoriaSelectedDate = null;
		window.mentoriaSelectedTime = null;
	}

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
			// Función manejadora para selección de hora (definida primero)
			const handleTimeSelect = function() {
				state.selectedTime = this.dataset.time;
				if (calendarType === 'mentoria') {
					window.mentoriaSelectedTime = this.dataset.time;
				} else {
					window.contactoSelectedTime = this.dataset.time;
				}
				renderTimeSlots();
			};

			// Renderizar SOLO estado de carga (sin botones aún)
			tc.innerHTML = `<div class="contacto-agenda__time-wrapper">
				<div class="contacto-agenda__time-label">
					<svg class="contacto-agenda__time-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M15 19l-7-7 7-7"></path>
					</svg>
					<span>Cargando disponibilidad...</span>
				</div>
				<div class="contacto-agenda__time-grid"></div>
			</div>`;

			// Obtener horas booked ANTES de renderizar botones
			const result = await getBookedHours(state.selectedKey);
			const label = tc.querySelector('.contacto-agenda__time-label span');

			if (!result.success) {
				// Error: no renderizar botones
				if (label) {
					label.textContent = `Error al cargar disponibilidad: ${result.error}`;
				}
				console.error('Failed to load booked hours:', result.error);
				return;
			}

			// Éxito: AHORA renderizar solo botones disponibles
			const bookedHours = result.data || [];
			const bookedSet = new Set(bookedHours);
			const timeGrid = tc.querySelector('.contacto-agenda__time-grid');

			if (bookedHours.length === TIME_SLOTS.length) {
				label.textContent = 'No hay disponibilidad este día';
				return;
			}

			label.textContent = 'Elige una hora';

			// Renderizar solo horas DISPONIBLES (no las ocupadas)
			const availableSlotsHTML = TIME_SLOTS.filter(time => !bookedSet.has(time))
				.map(time => {
					const hour = parseInt(time.split(':')[0]);
					const minute = time.split(':')[1];
					const endHour = parseInt(minute) === 30 ? (hour + 1).toString().padStart(2, '0') + ':00' : hour.toString().padStart(2, '0') + ':30';
					return `
						<button class="contacto-agenda__time-btn" data-time="${time}">
							${time} - ${endHour}
						</button>
					`;
				})
				.join('');

			timeGrid.innerHTML = availableSlotsHTML;

			// Agregar listeners a botones disponibles
			tc.querySelectorAll('.contacto-agenda__time-btn').forEach(btn => {
				btn.addEventListener('click', handleTimeSelect);
			});

			// Agregar listener al label para volver atrás
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

			// Botón para continuar al formulario (en móvil)
			const continueBtn = tc.querySelector('.contacto-agenda__form-btn');
			if (continueBtn) {
				continueBtn.addEventListener('click', () => {
					// Ocultar calendario, mostrar formulario
					const calendarLeft = document.querySelector('.contacto-agenda__left');
					const formRight = document.querySelector('.contacto-agenda__right');
					const agendaGrid = document.querySelector('.contacto-agenda__grid');
					const form = document.getElementById('contacto-agenda-form');

					if (calendarLeft) calendarLeft.classList.add('is-hidden');
					if (formRight) {
						formRight.classList.add('is-visible');
						formRight.classList.remove('is-hidden');
					}

					// Ajustar altura del grid al formulario
					if (agendaGrid && formRight) {
						setTimeout(() => {
							const formHeight = formRight.offsetHeight;
							agendaGrid.style.minHeight = formHeight + 'px';
						}, 10);
					}

					// Scroll y focus al formulario
					if (form) {
						form.scrollIntoView({ behavior: 'smooth', block: 'start' });
						setTimeout(() => form.focus(), 300);
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
			state.selectedKey = null;
			state.selectedTime = null;

			// Mostrar calendario y ocultar formulario en móvil
			const calendarLeft = document.querySelector('.contacto-agenda__left');
			const formRight = document.querySelector('.contacto-agenda__right');
			const agendaGrid = document.querySelector('.contacto-agenda__grid');

			if (calendarLeft) calendarLeft.classList.remove('is-hidden');
			if (formRight) {
				formRight.classList.add('is-hidden');
				formRight.classList.remove('is-visible');
			}

			// Reset altura del grid
			if (agendaGrid) {
				agendaGrid.style.minHeight = 'auto';
			}

			await renderCalendar();
			renderTimeSlots();
		});
	}

	async function getBookedHours(fecha) {
		const now = Date.now();

		// L1: localStorage (persiste entre páginas)
		try {
			const localStored = localStorage.getItem('avance_booked_' + fecha);
			if (localStored) {
				const parsed = JSON.parse(localStored);
				if (parsed.expiry > now) {
					return {
						success: true,
						data: parsed.hours,
						cached: true,
						source: 'localStorage'
					};
				} else {
					localStorage.removeItem('avance_booked_' + fecha);
				}
			}
		} catch (e) {
			// localStorage no disponible o error
		}

		// L2: estado local del script
		const cachedTime = state.cacheTTL[fecha];
		const isCacheValid = cachedTime && (now - cachedTime) < state.CACHE_DURATION;

		if (isCacheValid && state.hoursCache[fecha]) {
			return {
				success: true,
				data: state.hoursCache[fecha],
				cached: true,
				source: 'memory'
			};
		}

		// Todas las horas están disponibles (sin tabla de calendario)
		return {
			success: true,
			data: [],
			cached: false,
			source: 'default'
		};
	}

	// Invalidar caché para una fecha específica
	window.invalidateHoursCache = function(fecha) {
		delete state.hoursCache[fecha];
		delete state.cacheTTL[fecha];
	};

	// Invalidar TODO el caché (después de completar reserva)
	window.invalidateAllHoursCache = function() {
		state.hoursCache = {};
		state.cacheTTL = {};
	};


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

	// Mostrar calendario inicialmente limpio
	const calendarLeft = document.querySelector('.contacto-agenda__left');
	const formRight = document.querySelector('.contacto-agenda__right');
	const agendaGrid = document.querySelector('.contacto-agenda__grid');

	if (calendarLeft) calendarLeft.classList.remove('is-hidden');
	if (formRight) {
		formRight.classList.add('is-hidden');
		formRight.classList.remove('is-visible');
	}
	if (agendaGrid) {
		agendaGrid.style.minHeight = 'auto';
	}

	await renderCalendar();
	renderTimeSlots();
});
