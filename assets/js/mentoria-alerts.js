/**
 * Mentoria Alerts System
 * Gestiona alertas de error y éxito
 *
 * @package Avance_Template
 */

class MentoriaAlerts {
	static showError(message) {
		const header = document.querySelector('.mentoria-reserva__header');
		if (header) {
			const alert = document.createElement('div');
			alert.className = 'mentoria-alert mentoria-alert--error';
			alert.textContent = message;
			alert.setAttribute('role', 'alert');
			header.insertAdjacentElement('afterend', alert);
			setTimeout(() => alert.remove(), 4000);
		}
	}

	static showSuccess(message) {
		const header = document.querySelector('.mentoria-reserva__header');
		if (header) {
			const alert = document.createElement('div');
			alert.className = 'mentoria-alert mentoria-alert--success';
			alert.textContent = message;
			alert.setAttribute('role', 'alert');
			header.insertAdjacentElement('afterend', alert);
			setTimeout(() => alert.remove(), 4000);
		}
	}
}
