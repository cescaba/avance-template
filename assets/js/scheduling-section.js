/**
 * Scheduling Section for Contacto Page
 * Manages calendar and form visibility for mobile 479px
 * Shows calendar initially, form after date selection
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	const config = {
		breakpoint: 479,
	};

	let state = {
		selectedDate: null,
	};

	function init() {
		attachEventListeners();
	}

	function isMobile479() {
		return window.innerWidth <= config.breakpoint;
	}

	function getSchedulingElements() {
		const agendaGrid = document.querySelector('.contacto-agenda__grid');
		return { agendaGrid };
	}

	function attachEventListeners() {
		const agendaContainer = document.querySelector('.contacto-agenda');
		if (!agendaContainer) return;

		const agendaGrid = agendaContainer.querySelector('.contacto-agenda__grid');
		if (!agendaGrid) return;

		// Detectar clicks en botones de días del calendario
		const dayButtons = agendaContainer.querySelectorAll('.contacto-agenda__daybtn');
		dayButtons.forEach(btn => {
			btn.addEventListener('click', function() {
				if (!this.disabled && isMobile479()) {
					state.selectedDate = this.textContent;
					// Agregar clase show-right para mostrar formulario en 479px
					if (agendaGrid) {
						agendaGrid.classList.add('show-right');
					}
				}
			});
		});

		// Detectar navegación de mes (para resetear selección)
		const navButtons = agendaContainer.querySelectorAll('.contacto-agenda__cal-nav');
		navButtons.forEach(btn => {
			btn.addEventListener('click', function() {
				if (isMobile479()) {
					state.selectedDate = null;
					// Remover clase show-right para volver a mostrar calendario
					if (agendaGrid) {
						agendaGrid.classList.remove('show-right');
					}
				}
			});
		});

		// Botón para volver al calendario (si existe)
		const backBtn = agendaContainer.querySelector('[data-action="back-to-calendar"]');
		if (backBtn) {
			backBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (isMobile479()) {
					state.selectedDate = null;
					if (agendaGrid) {
						agendaGrid.classList.remove('show-right');
					}
				}
			});
		}
	}

	// Iniciar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
