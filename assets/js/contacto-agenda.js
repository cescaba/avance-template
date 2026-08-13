document.addEventListener('DOMContentLoaded', () => {
	const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
	const TOPIC_NOTES = {
		'': 'Al seleccionar el tema, verás qué temas trataremos en la sesión para aprovecharla al máximo.',
		'Diagnóstico de negocio': 'Revisaremos el estado actual de tu negocio y detectaremos oportunidades inmediatas de mejora.',
		'Estrategia comercial': 'Hablaremos de canales, mensaje y presupuesto para atraer más clientes.',
		'Capacitación ejecutiva': 'Identificaremos qué tipo de capacitación se ajusta mejor a las necesidades de tu equipo.',
		'Mentoría 1:1': 'Analizaremos tus desafíos como líder y diseñaremos un plan de mentoría personalizado.',
		'Otro tema': 'Cuéntame en la sesión qué necesitas y lo adaptamos sobre la marcha.'
	};

	const state = {
		viewYear: new Date().getFullYear(),
		viewMonth: new Date().getMonth(),
		selectedKey: null
	};

	const calGrid = document.getElementById('contactoAgendaCalGrid');
	const monthLabel = document.getElementById('contactoAgendaMonthLabel');
	const nameInput = document.getElementById('contacto-agenda-name');
	const phoneInput = document.getElementById('contacto-agenda-phone');
	const topicSelect = document.getElementById('contacto-agenda-topic');
	const topicSelectTrigger = document.getElementById('contacto-agenda-topic-trigger');
	const topicSelectMenu = document.getElementById('contacto-agenda-topic-menu');
	const topicOptions = document.querySelectorAll('.contacto-agenda__select-option');
	const topicNote = document.getElementById('contacto-agenda-note');
	const submitBtn = document.getElementById('contacto-agenda-submit');

	function daysInMonth(y, m) {
		return new Date(y, m + 1, 0).getDate();
	}

	function renderCalendar() {
		const { viewYear: y, viewMonth: m, selectedKey } = state;
		monthLabel.textContent = MONTHS[m] + ' ' + y;

		calGrid.querySelectorAll('.contacto-agenda__daybtn').forEach(el => el.remove());

		const today = new Date();
		const todayKey = today.getFullYear() + '-' + today.getMonth() + '-' + today.getDate();
		const firstWeekday = new Date(y, m, 1).getDay();
		const total = daysInMonth(y, m);
		const frag = document.createDocumentFragment();

		for (let i = 0; i < firstWeekday; i++) {
			const pad = document.createElement('div');
			pad.className = 'contacto-agenda__daybtn contacto-agenda__daybtn.pad';
			frag.appendChild(pad);
		}

		for (let d = 1; d <= total; d++) {
			const key = y + '-' + m + '-' + d;
			const weekday = new Date(y, m, d).getDay();
			const isWeekend = weekday === 0 || weekday === 6;
			const isSelected = key === selectedKey;
			const isToday = key === todayKey;

			const btn = document.createElement('button');
			btn.className = 'contacto-agenda__daybtn' + (isWeekend ? ' weekend' : '') + (isSelected ? ' selected' : '');
			btn.textContent = d;
			btn.dataset.key = key;
			if (isToday && !isSelected) {
				const dot = document.createElement('span');
				dot.className = 'dot';
				btn.appendChild(dot);
			}
			btn.addEventListener('click', () => {
				state.selectedKey = key;
				renderCalendar();
				updateSubmitState();
			});
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

	document.getElementById('contactoAgendaPrevMonth').addEventListener('click', () => {
		state.viewMonth -= 1;
		if (state.viewMonth < 0) {
			state.viewMonth = 11;
			state.viewYear -= 1;
		}
		renderCalendar();
	});

	document.getElementById('contactoAgendaNextMonth').addEventListener('click', () => {
		state.viewMonth += 1;
		if (state.viewMonth > 11) {
			state.viewMonth = 0;
			state.viewYear += 1;
		}
		renderCalendar();
	});

	// Manejar select personalizado
	if (topicSelectTrigger && topicSelectMenu && topicOptions.length > 0) {
		topicSelectTrigger.addEventListener('click', (e) => {
			e.stopPropagation();
			topicSelectMenu.classList.toggle('open');
			console.log('Click en trigger, open:', topicSelectMenu.classList.contains('open'));
		});

		topicOptions.forEach(option => {
			option.addEventListener('click', (e) => {
				e.stopPropagation();
				const value = option.getAttribute('data-value');
				topicSelect.value = value;
				topicSelectTrigger.textContent = option.textContent;
				topicSelectMenu.classList.remove('open');

				// Actualizar opción seleccionada
				topicOptions.forEach(opt => opt.classList.remove('selected'));
				option.classList.add('selected');

				topicNote.textContent = TOPIC_NOTES[value] || TOPIC_NOTES[''];
				updateSubmitState();
				console.log('Opción seleccionada:', value);
			});
		});

		// Cerrar el menú al hacer click fuera
		document.addEventListener('click', (e) => {
			if (!e.target.closest('.contacto-agenda__select-wrapper')) {
				topicSelectMenu.classList.remove('open');
			}
		});
	} else {
		console.error('No se encontraron los elementos del select personalizado');
		console.log('topicSelectTrigger:', topicSelectTrigger);
		console.log('topicSelectMenu:', topicSelectMenu);
		console.log('topicOptions:', topicOptions);
	}

	nameInput.addEventListener('input', updateSubmitState);
	phoneInput.addEventListener('input', updateSubmitState);

	function updateSubmitState() {
		const canSubmit = !!(nameInput.value.trim() && phoneInput.value.trim() && topicSelect.value && state.selectedKey);
		submitBtn.disabled = !canSubmit;
	}

	submitBtn.addEventListener('click', () => {
		if (!submitBtn.disabled) {
			handleSubmit();
		}
	});

	function handleSubmit() {
		const nombre = nameInput.value.trim();
		const numero = phoneInput.value.trim();
		const tema = topicSelect.value;
		const fecha = state.selectedKey;

		if (!nombre || !numero || !tema || !fecha) {
			alert('Por favor completa todos los campos');
			return;
		}

		const data = {
			action: 'avance_agendamiento_submit',
			nonce: avanceAgendamientoConfig.nonce,
			nombre,
			numero,
			tema,
			fecha,
		};

		submitBtn.disabled = true;
		submitBtn.textContent = 'Procesando...';

		fetch(avanceAgendamientoConfig.ajaxUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams(data),
		})
			.then(response => {
				if (!response.ok) {
					throw new Error('Error en la respuesta del servidor');
				}
				return response.json();
			})
			.then(result => {
				console.log('Respuesta AJAX:', result);

				if (result.success) {
					submitBtn.textContent = 'Reunión agendada ✓';
					submitBtn.style.backgroundColor = '#034732';

					// Abrir WhatsApp
					if (result.data && result.data.whatsapp_url) {
						const whatsappUrl = result.data.whatsapp_url;
						console.log('URL de WhatsApp:', whatsappUrl);

						// Abrir en nueva pestaña
						const whatsappWindow = window.open(whatsappUrl, '_blank');
						if (!whatsappWindow) {
							alert('No se pudo abrir WhatsApp. Copia y abre esta URL en tu navegador:\n' + whatsappUrl);
						}
					} else {
						console.error('No se encontró whatsapp_url en la respuesta');
						alert('Agendamiento guardado. Abre WhatsApp manualmente.');
					}

					// Resetear formulario después de 3 segundos
					setTimeout(() => {
						resetForm();
					}, 3000);
				} else {
					submitBtn.textContent = 'Error al agendar';
					submitBtn.disabled = false;
					const errorMsg = result.data?.message || result.message || 'Error desconocido';
					alert('Error: ' + errorMsg);
					console.error('Error en respuesta:', result);
				}
			})
			.catch(error => {
				console.error('Error en fetch:', error);
				submitBtn.textContent = 'Error al agendar';
				submitBtn.disabled = false;
				alert('Error al procesar la solicitud: ' + error.message);
			});
	}

	function resetForm() {
		nameInput.value = '';
		phoneInput.value = '';
		topicSelect.value = '';
		state.selectedKey = null;
		renderCalendar();
		submitBtn.textContent = 'Agendar Reunión';
		updateSubmitState();
	}

	renderCalendar();
	updateSubmitState();
});
