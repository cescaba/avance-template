/**
 * Avance Menu Handler
 * Gestiona el menú móvil con animaciones elegantes
 *
 * @package Avance_Template
 */

class AvanceMenu {
	constructor() {
		this.menuToggle = document.querySelector('.avance-menu-toggle');
		this.header = document.querySelector('.avance-header');
		this.menuPanel = document.querySelector('.avance-menu-panel');
		this.menuLinks = document.querySelectorAll('.avance-menu-panel__link');

		if (this.menuToggle && this.header && this.menuPanel) {
			this.init();
		}
	}

	init() {
		// Toggle al hacer click en hamburguesa
		this.menuToggle.addEventListener('click', () => this.toggle());

		// Cerrar al hacer click en un link
		this.menuLinks.forEach(link => {
			link.addEventListener('click', () => this.close());
		});

		// Cerrar al hacer click fuera del menú
		document.addEventListener('click', (e) => {
			if (!this.header.contains(e.target) && this.header.classList.contains('is-open')) {
				this.close();
			}
		});
	}

	toggle() {
		if (this.header.classList.contains('is-open')) {
			this.close();
		} else {
			this.open();
		}
	}

	open() {
		this.header.classList.add('is-open');
		this.menuPanel.classList.remove('closing');
	}

	close() {
		// Agregar clase closing para la animación panelOut
		this.menuPanel.classList.add('closing');

		// Esperar a que termine la animación (0.2s) antes de quitar is-open
		setTimeout(() => {
			this.header.classList.remove('is-open');
		}, 200);
	}
}

// Inicializar cuando el DOM esté listo
function initAvanceMenu() {
	if (document.querySelector('.avance-menu-toggle')) {
		new AvanceMenu();
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initAvanceMenu);
} else {
	initAvanceMenu();
}
