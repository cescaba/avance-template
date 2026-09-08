document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('mentoria-booking-form');
	const submitBtn = document.getElementById('mentoriaSubmitBtn');
	const backBtn = document.getElementById('mentoriaBackBtn');

	if (!form || !submitBtn) return;

	function showNotification(msg, type = 'error') {
		alert(msg);
	}

	function validateForm(data) {
		if (!data.first_name || data.first_name.length < 3) {
			return 'El nombre debe tener al menos 3 caracteres';
		}
		if (!data.email || !data.email.includes('@')) {
			return 'Email válido requerido';
		}
		const digitsOnly = data.whatsapp.replace(/[^0-9]/g, '');
		if (digitsOnly.length < 9 || digitsOnly.length > 15) {
			return 'WhatsApp debe tener entre 9 y 15 dígitos';
		}
		if (!data.desafio || data.desafio.length < 10) {
			return 'Describe tu desafío (mínimo 10 caracteres)';
		}
		if (!window.mentoriaSelectedDate || !window.mentoriaSelectedTime) {
			return 'Selecciona fecha y hora en el calendario';
		}
		return null;
	}

	form.addEventListener('submit', async (e) => {
		e.preventDefault();

		const data = {
			first_name: document.getElementById('mentoriaFirstName').value,
			email: document.getElementById('mentoriaEmail').value,
			whatsapp: document.getElementById('mentoriaWhatsapp').value,
			desafio: document.getElementById('mentoriaDesafio').value,
			fecha: `${window.mentoriaSelectedDate.year}-${String(window.mentoriaSelectedDate.month + 1).padStart(2, '0')}-${String(window.mentoriaSelectedDate.day).padStart(2, '0')}`,
			hora: window.mentoriaSelectedTime,
		};

		const error = validateForm(data);
		if (error) {
			showNotification(error, 'error');
			return;
		}

		submitBtn.disabled = true;
		submitBtn.textContent = 'Procesando...';

		try {
			// TODO: Integrar con WooCommerce checkout
			// Por ahora, simular éxito
			showNotification('¡Reserva confirmada! Redirigiendo a pago...', 'success');
			
			// Simular redirección a WooCommerce
			setTimeout(() => {
				window.location.href = '/checkout';
			}, 2000);
		} catch (error) {
			showNotification('Error al procesar: ' + error.message, 'error');
			submitBtn.disabled = false;
			submitBtn.textContent = 'Pagar y Confirmar →';
		}
	});

	if (backBtn) {
		backBtn.addEventListener('click', () => {
			window.mentoriaSelectedDate = null;
			window.mentoriaSelectedTime = null;
			if (window.resetMentoriaCalendar) {
				window.resetMentoriaCalendar();
			}
		});
	}
});
