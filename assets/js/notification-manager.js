/**
 * Global Notification Manager
 * Gestiona notificaciones con diseño consistente
 */

class NotificationManager {
	static show(message, type = 'error', targetElement = null) {
		const notif = document.createElement('div');
		notif.className = `notification notification--${type}`;
		notif.textContent = message;

		if (targetElement) {
			targetElement.insertAdjacentElement('afterbegin', notif);
		} else {
			document.body.insertAdjacentElement('afterbegin', notif);
		}

		setTimeout(() => notif.remove(), 4000);
		return notif;
	}

	static error(message, targetElement = null) {
		return this.show(message, 'error', targetElement);
	}

	static success(message, targetElement = null) {
		return this.show(message, 'success', targetElement);
	}
}
