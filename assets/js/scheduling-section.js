document.addEventListener('DOMContentLoaded', () => {
	const submitBtn = document.getElementById('contacto-agenda-submit');
	const nameInput = document.getElementById('contacto-agenda-name');
	const phoneInput = document.getElementById('contacto-agenda-phone');
	const topicInput = document.getElementById('contacto-agenda-topic');

	if (!submitBtn || !nameInput || !phoneInput) {
		return;
	}

	function showNotification(msg) {
		const form = document.getElementById('contacto-agenda-form');
		NotificationManager.error(msg, form);
	}

	function extractErrorMessage(result) {
		// Formato esperado: result.message (frontend) o result.data.message (backend)
		const message = result.message || result.data?.message;

		if (!message) {
			return 'Error al agendar. Intenta de nuevo.';
		}

		if (typeof message !== 'string') {
			return 'Error al agendar. Intenta de nuevo.';
		}

		return message.trim();
	}

	function formatDate(dateObj) {
		if (!dateObj || typeof dateObj !== 'object') {
			return null;
		}

		const year = Number.isInteger(dateObj.year) ? dateObj.year : NaN;
		const month = Number.isInteger(dateObj.month) ? dateObj.month + 1 : NaN;
		const day = Number.isInteger(dateObj.day) ? dateObj.day : NaN;

		// Validar rangos válidos
		if (!year || year < 1900 || year > 2100 || !month || month < 1 || month > 12 || !day || day < 1 || day > 31) {
			return null;
		}

		// Intentar crear una fecha válida para validar el día dentro del mes
		const date = new Date(year, month - 1, day);
		if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
			return null;
		}

		return String(year).padStart(4, '0') + '-' +
		       String(month).padStart(2, '0') + '-' +
		       String(day).padStart(2, '0');
	}

	function validateFormData(data) {
		if (!data.nombre || data.nombre.length < 3) {
			return 'El nombre debe tener al menos 3 caracteres';
		}
		if (!data.whatsapp || data.whatsapp.length < 9) {
			return 'El WhatsApp debe ser válido';
		}
		if (!data.tema) {
			return 'Debes seleccionar un tema';
		}
		if (!data.fecha) {
			return 'Fecha inválida';
		}
		if (!data.hora || !/^\d{2}:\d{2}$/.test(data.hora)) {
			return 'Hora inválida';
		}
		return null;
	}

	const form = document.getElementById('contacto-agenda-form');

	form.addEventListener('submit', async (e) => {
		e.preventDefault();

		if (!window.avanceAgendamientoContactoConfig) {
			showNotification('Error de seguridad. Recarga la página.');
			return;
		}

		// Validar fecha y hora
		if (!window.contactoSelectedDate || !window.contactoSelectedTime) {
			showNotification('Falta seleccionar la fecha y hora en el calendario');
			return;
		}

		const fecha = formatDate(window.contactoSelectedDate);
		if (!fecha) {
			showNotification('Fecha no válida. Intenta de nuevo.');
			return;
		}

		const data = {
			action: 'avance_submit_agendamiento',
			nonce: window.avanceAgendamientoContactoConfig.nonce,
			nombre: nameInput.value.trim(),
			whatsapp: phoneInput.value.trim(),
			tema: topicInput.value,
			fecha: fecha,
			hora: window.contactoSelectedTime
		};

		// Validar datos antes de enviar
		const validationError = validateFormData(data);
		if (validationError) {
			showNotification(validationError);
			return;
		}

		submitBtn.textContent = 'Agendando...';
		submitBtn.disabled = true;

		try {
			const response = await fetch(window.avanceAgendamientoContactoConfig.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams(data)
			});

			let result;
			try {
				result = await response.json();
			} catch (parseError) {
				throw new Error('Respuesta inválida del servidor');
			}

			if (!result || typeof result !== 'object') {
				throw new Error('Respuesta inválida del servidor');
			}

			// Validar HTTP status DESPUÉS de parsear JSON (así capturamos el mensaje de error)
			if (!response.ok) {
				const errorMsg = extractErrorMessage(result);
				showNotification(errorMsg);
				return;
			}

			if (result.success !== true) {
				const errorMsg = extractErrorMessage(result);
				showNotification(errorMsg);
				return;
			}

			const whatsappNumber = '51993508652';
			const agendaMessage = `Hola, buenos días.\nAcabo de agendar una sesión de diagnóstico a través del sistema.\n\nDatos de la cita:\nNombre: ${data.nombre}\nFecha: ${data.fecha}\nHora: ${data.hora}\nTema: ${data.tema}`;
			const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(agendaMessage)}`;
			window.open(whatsappUrl, '_blank');

			nameInput.value = '';
			phoneInput.value = '';
			topicInput.value = '';
			window.contactoSelectedDate = null;
			window.contactoSelectedTime = null;

			if (window.resetContactoCalendar) {
				window.resetContactoCalendar();
			}
		} catch (error) {
			const userMsg = error.message.includes('HTTP')
				? 'El servidor no pudo procesar tu solicitud. Intenta de nuevo.'
				: error.message || 'Error de conexión. Verifica tu conexión e intenta de nuevo.';
			showNotification(userMsg);
		} finally {
			submitBtn.textContent = 'Agendar Reunión';
			submitBtn.disabled = false;
		}
	});
});
