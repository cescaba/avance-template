/**
 * Diagnostico Submit Handler
 * Maneja el envío del formulario de diagnóstico al servidor
 * Flujo: Validar → Guardar en BD → Abrir WhatsApp
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	const config = {
		ajaxUrl: (typeof avanceDiagnosticoConfig !== 'undefined' && avanceDiagnosticoConfig.ajaxUrl)
			? avanceDiagnosticoConfig.ajaxUrl
			: '/wp-admin/admin-ajax.php',
		nonce: (typeof avanceDiagnosticoConfig !== 'undefined' && avanceDiagnosticoConfig.nonce)
			? avanceDiagnosticoConfig.nonce
			: '',
	};

	let submitBtn = null;

	// Hook al formulario de diagnóstico
	window.avanceDiagnosticoSubmit = function(formData, button) {
		submitBtn = button;
		submitDiagnostico(formData);
	};

	function submitDiagnostico(formData) {
		updateButtonStatus('Procesando...');

		// Preparar datos para AJAX
		const ajaxData = new FormData();
		ajaxData.append('action', 'avance_submit_diagnostico');
		ajaxData.append('nonce', config.nonce);
		ajaxData.append('nombreCompleto', formData.nombreCompleto);
		ajaxData.append('email', formData.email);
		ajaxData.append('whatsapp', formData.whatsapp);

		// Agregar respuestas (array)
		if (Array.isArray(formData.respuestas)) {
			formData.respuestas.forEach((respuesta, index) => {
				ajaxData.append('respuestas[]', respuesta);
			});
		}

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: ajaxData,
		})
			.then(response => {
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.then(response => {
				if (response.success && response.data && response.data.url) {
					updateButtonStatus('Abriendo WhatsApp...');

					setTimeout(() => {
						openWhatsApp(response.data.url);

						setTimeout(() => {
							updateButtonStatus('Se envió su diagnóstico');
							// El quiz se reiniciará automáticamente después del setTimeout en diagnostico-quiz.js
						}, 800);
					}, 1500);
				} else {
					updateButtonStatus('Se envió su diagnóstico');
					showError(response.data?.message || 'Ocurrió un error. Intenta de nuevo.');
				}
			})
			.catch(error => {
				updateButtonStatus('Se envió su diagnóstico');
				showError('Error de conexión. Intenta de nuevo.');
			});
	}

	function updateButtonStatus(message) {
		if (submitBtn) {
			submitBtn.textContent = message;
		}
	}

	function openWhatsApp(url) {
		window.open(url, '_blank');
	}

	function showError(message) {
		alert(message);
	}
})();
