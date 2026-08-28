document.addEventListener('DOMContentLoaded', () => {
	// Form handling para scheduling/contacto
	const submitBtn = document.getElementById('contacto-agenda-submit');
	const nameInput = document.getElementById('contacto-agenda-name');
	const phoneInput = document.getElementById('contacto-agenda-phone');
	const topicInput = document.getElementById('contacto-agenda-topic');
	const backBtn = document.getElementById('contacto-agenda-back');

	if (!submitBtn || !nameInput || !phoneInput || !topicInput) {
		return;
	}

	function updateSubmitButtonState() {
		const isValid = nameInput.value.trim() &&
			phoneInput.value.trim() &&
			topicInput.value &&
			window.contactoSelectedDate &&
			window.contactoSelectedTime;
		submitBtn.disabled = !isValid;
	}

	window.updateSubmitButtonState = updateSubmitButtonState;

	nameInput.addEventListener('input', updateSubmitButtonState);
	phoneInput.addEventListener('input', updateSubmitButtonState);
	topicInput.addEventListener('change', updateSubmitButtonState);

	submitBtn.addEventListener('click', async (e) => {
		e.preventDefault();

		if (!window.avanceAgendamientoContactoConfig) {
			alert('Error de seguridad. Recarga la página.');
			return;
		}

		const data = {
			action: 'avance_agendamiento_contacto',
			nonce: window.avanceAgendamientoContactoConfig.nonce,
			nombre: nameInput.value.trim(),
			whatsapp: phoneInput.value.trim(),
			tema: topicInput.value,
			fecha: window.contactoSelectedDate?.year + '-' + (window.contactoSelectedDate?.month + 1) + '-' + window.contactoSelectedDate?.day,
			hora: window.contactoSelectedTime
		};

		submitBtn.disabled = true;
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
				const agendaMessage = `Hola, acabo de agendar una sesión de diagnóstico:
📅 Fecha: ${data.fecha}
🕐 Hora: ${data.hora}
📋 Tema: ${data.tema}
👤 Nombre: ${data.nombre}`;

				const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(agendaMessage)}`;
				window.open(whatsappUrl, '_blank');

				nameInput.value = '';
				phoneInput.value = '';
				topicInput.value = '';
				window.contactoSelectedDate = null;
				window.contactoSelectedTime = null;
				updateSubmitButtonState();

				alert('¡Agendamiento confirmado! Se abrirá WhatsApp para enviar los detalles.');
			} else {
				alert(result.data.mensaje || 'Error al agendar. Intenta de nuevo.');
			}
		} catch (error) {
			alert('Error de conexión. Intenta de nuevo.');
			console.error(error);
		} finally {
			updateSubmitButtonState();
			submitBtn.textContent = 'Agendar Reunión';
		}
	});
});
