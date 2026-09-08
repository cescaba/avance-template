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

	const form = document.getElementById('contacto-agenda-form');

	form.addEventListener('submit', async (e) => {
		e.preventDefault();

		// Validar que fecha y hora estén seleccionadas
		if (!window.contactoSelectedDate || !window.contactoSelectedTime) {
			showNotification('Falta seleccionar la fecha y hora en el calendario');
			return;
		}

		if (!window.avanceAgendamientoContactoConfig) {
			alert('Error de seguridad. Recarga la página.');
			return;
		}

		const data = {
			action: 'avance_submit_agendamiento',
			nonce: window.avanceAgendamientoContactoConfig.nonce,
			nombre: nameInput.value.trim(),
			whatsapp: phoneInput.value.trim(),
			tema: topicInput.value,
			fecha: String(window.contactoSelectedDate?.year).padStart(4, '0') + '-' +
				   String(window.contactoSelectedDate?.month + 1).padStart(2, '0') + '-' +
				   String(window.contactoSelectedDate?.day).padStart(2, '0'),
			hora: window.contactoSelectedTime
		};

		submitBtn.textContent = 'Agendando...';

		try {
			const response = await fetch(window.avanceAgendamientoContactoConfig.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams(data)
			});

			const result = await response.json();

			if (result.success) {
				const whatsappNumber = '51993508652';
				const agendaMessage = `Hola, buenos días.
Acabo de agendar una sesión de diagnóstico a través del sistema.

Datos de la cita:
Nombre: ${data.nombre}
Fecha: ${data.fecha}
Hora: ${data.hora}
Tema: ${data.tema}`;

				const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(agendaMessage)}`;
				window.open(whatsappUrl, '_blank');

				nameInput.value = '';
				phoneInput.value = '';
				topicInput.value = '';
				window.contactoSelectedDate = null;
				window.contactoSelectedTime = null;

				// Resetear calendario visualmente
				if (window.resetContactoCalendar) {
					window.resetContactoCalendar();
				}
			} else {
				showNotification(result.data?.message || result.data?.mensaje || 'Error al agendar. Intenta de nuevo.');
			}
		} catch (error) {
			showNotification('Error de conexión. Intenta de nuevo.');
			console.error(error);
		} finally {
			submitBtn.textContent = 'Agendar Reunión';
		}
	});
});
