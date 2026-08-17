document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('proposal-form');
	if (!form) return;

	const submitBtn = form.querySelector('button[type="submit"]');
	const nameInput = form.querySelector('input[name="nombre"]');
	const cargoInput = form.querySelector('input[name="cargo"]');
	const empresaInput = form.querySelector('input[name="empresa"]');
	const equipoInput = form.querySelector('input[name="tamaño_equipo"]');
	const emailInput = form.querySelector('input[name="email"]');
	const whatsappInput = form.querySelector('input[name="whatsapp"]');
	const servicioInput = form.querySelector('input[name="servicio_interes"]');
	const desafioInput = form.querySelector('textarea[name="desafio_comercial"]');

	// Validación básica client-side
	function validateForm() {
		const errors = [];

		if (!nameInput.value.trim()) {
			errors.push('El nombre es requerido');
		}

		if (!empresaInput.value.trim()) {
			errors.push('La empresa es requerida');
		}

		if (!emailInput.value.trim()) {
			errors.push('El email es requerido');
		} else if (!isValidEmail(emailInput.value)) {
			errors.push('El email no es válido');
		}

		if (!servicioInput.value.trim()) {
			errors.push('El servicio de interés es requerido');
		}

		if (!desafioInput.value.trim()) {
			errors.push('El desafío comercial es requerido');
		} else if (desafioInput.value.trim().length < 10) {
			errors.push('Por favor describe tu desafío con más detalle');
		}

		return errors;
	}

	function isValidEmail(email) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return emailRegex.test(email);
	}

	// Enviar formulario
	form.addEventListener('submit', (e) => {
		e.preventDefault();

		const errors = validateForm();
		if (errors.length > 0) {
			alert('Errores en el formulario:\n\n' + errors.join('\n'));
			return;
		}

		submitBtn.disabled = true;
		submitBtn.textContent = 'Procesando...';

		const formData = new FormData();
		formData.append('action', 'avance_proposal_submit');
		formData.append('nonce', avanceProposalConfig.nonce);
		formData.append('nombre', nameInput.value.trim());
		formData.append('cargo', cargoInput.value.trim());
		formData.append('empresa', empresaInput.value.trim());
		formData.append('tamaño_equipo', equipoInput.value.trim());
		formData.append('email', emailInput.value.trim());
		formData.append('whatsapp', whatsappInput.value.trim());
		formData.append('servicio_interes', servicioInput.value.trim());
		formData.append('desafio_comercial', desafioInput.value.trim());

		fetch(avanceProposalConfig.ajaxUrl, {
			method: 'POST',
			body: formData,
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.then((result) => {
				if (result.success) {
					submitBtn.textContent = '¡Propuesta enviada!';
					submitBtn.style.backgroundColor = '#28a745';

					// Abrir WhatsApp
					if (result.data && result.data.whatsapp_url) {
						const whatsappUrl = result.data.whatsapp_url;
						const whatsappWindow = window.open(whatsappUrl, '_blank');
						if (!whatsappWindow) {
							alert(
								'No se pudo abrir WhatsApp. Copia y abre esta URL en tu navegador:\n' +
									whatsappUrl
							);
						}
					}

					// Resetear formulario después de 3 segundos
					setTimeout(() => {
						resetForm();
					}, 3000);
				} else {
					submitBtn.textContent = 'Error al enviar';
					submitBtn.disabled = false;
					const errorMsg = result.data?.message || result.message || 'Error desconocido';
					alert('Error: ' + errorMsg);
				}
			})
			.catch((error) => {
				submitBtn.textContent = 'Error al procesar';
				submitBtn.disabled = false;
				alert('Error al procesar la solicitud: ' + error.message);
			});
	});

	function resetForm() {
		form.reset();
		nameInput.value = '';
		cargoInput.value = '';
		empresaInput.value = '';
		equipoInput.value = '';
		emailInput.value = '';
		whatsappInput.value = '';
		servicioInput.value = '';
		desafioInput.value = '';
		submitBtn.textContent = 'Solicitar propuesta personalizada';
		submitBtn.style.backgroundColor = '';
		submitBtn.disabled = false;
	}
});
