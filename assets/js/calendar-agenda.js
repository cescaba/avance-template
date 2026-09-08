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
		hoursCache: {}, // {fecha: [horas]}
		cacheTTL: {}, // {fecha: timestamp} - cuándo se cacheó
		CACHE_DURATION: 5 * 60 * 1000 // 5 minutos
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

		try {
			const action = calendarType === 'mentoria' ? 'avance_get_mentoria_hours' : 'avance_get_available_hours';
			const response = await fetch(`${AJAX_URL}?action=${action}&fecha=${fecha}&t=${now}`, {
				cache: 'no-store'
			});

			// Validar HTTP status
			if (!response.ok) {
				console.error(`HTTP ${response.status} fetching booked hours for ${fecha}`);
				return {
					success: false,
					error: `HTTP ${response.status}`,
					data: []
				};
			}

			// Validar JSON
			let responseData;
			try {
				responseData = await response.json();
			} catch (parseError) {
				console.error('JSON parse error for booked hours:', parseError);
				return {
					success: false,
					error: 'Respuesta inválida del servidor',
					data: []
				};
			}

			// Validar estructura de respuesta
			if (!responseData || typeof responseData !== 'object') {
				console.error('Invalid response structure for booked hours:', responseData);
				return {
					success: false,
					error: 'Estructura de respuesta inválida',
					data: []
				};
			}

			// Si el backend reporta éxito
			if (responseData.success !== true) {
				const errorMsg = responseData.message || 'Error desconocido del servidor';
				console.warn(`Backend error for booked hours (${fecha}):`, errorMsg);
				return {
					success: false,
					error: errorMsg,
					data: []
				};
			}

			// Validar que booked_hours sea un array
			const booked = Array.isArray(responseData.data?.booked_hours) ? responseData.data.booked_hours : [];

			// Guardar en localStorage (30 minutos) - persiste entre recargas
			try {
				localStorage.setItem('avance_booked_' + fecha, JSON.stringify({
					hours: booked,
					expiry: now + (30 * 60 * 1000)
				}));
			} catch (e) {
				// localStorage lleno o no disponible
			}

			// Guardar en caché local (10 minutos)
			state.hoursCache[fecha] = booked;
			state.cacheTTL[fecha] = now;

			return {
				success: true,
				data: booked,
				cached: false,
				source: 'api'
			};
		} catch (error) {
			console.error('Network error fetching booked hours:', error);
			return {
				success: false,
				error: error.message || 'Error de conexión',
				data: []
			};
		}
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

	// Función para prefetch horas del mes (batch - una query para 30 fechas)
	async function prefetchMonthHours() {
		const { viewYear: y, viewMonth: m } = state;
		const total = daysInMonth(y, m);
		const today = new Date();
		const todayKey = formatDateKey(today.getFullYear(), today.getMonth(), today.getDate());

		// Recopilar fechas disponibles para prefetch (próximos 30 días)
		const fechasToPrefetch = [];
		for (let d = 1; d <= total; d++) {
			const key = formatDateKey(y, m, d);
			const currentDate = new Date(y, m, d);
			const canSelect = !currentDate < today && key !== todayKey;

			if (canSelect && fechasToPrefetch.length < 30) {
				fechasToPrefetch.push(key);
			}
		}

		if (fechasToPrefetch.length === 0) return;

		// Batch prefetch: UNA query para 30 fechas (10x más rápido)
		try {
			const fechasParam = fechasToPrefetch.join(',');
			const url = `${AJAX_URL}?action=avance_get_booked_hours_batch&fechas=${encodeURIComponent(fechasParam)}`;
			const response = await fetch(url, {
				cache: 'no-store'
			});

			if (!response.ok) return;

			const result = await response.json();
			if (!result.success || !result.data?.booked_hours) return;

			// Guardar cada fecha en caché (localStorage + memoria)
			const bookedHours = result.data.booked_hours;
			const now = Date.now();

			Object.keys(bookedHours).forEach(fecha => {
				const hours = bookedHours[fecha];

				// localStorage (30 minutos, persiste recargas)
				try {
					localStorage.setItem('avance_booked_' + fecha, JSON.stringify({
						hours: hours,
						expiry: now + (30 * 60 * 1000)
					}));
				} catch (e) {}

				// Estado local (10 minutos)
				state.hoursCache[fecha] = hours;
				state.cacheTTL[fecha] = now;
			});

			console.debug('✓ Prefetch completado:', fechasToPrefetch.length, 'fechas en 1 query');
		} catch (err) {
			console.debug('Prefetch error:', err);
		}
	}

	initializeMobileState();
	await renderCalendar();
	prefetchMonthHours();
});
