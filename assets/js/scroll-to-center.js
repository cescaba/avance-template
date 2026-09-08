/**
 * Smooth scroll to center
 * Maneja todos los links internos (#id) y centra la sección en pantalla
 *
 * @package Avance_Template
 */

(function() {
	'use strict';

	function scrollToCenter(element) {
		if (!element) return;

		element.scrollIntoView({
			behavior: 'smooth',
			block: 'center'
		});
	}

	// Manejar clicks en links internos
	document.addEventListener('click', (e) => {
		const link = e.target.closest('a[href^="#"]');
		if (!link) return;

		const hash = link.getAttribute('href');
		if (!hash || hash === '#') return;

		const target = document.querySelector(hash);
		if (!target) return;

		e.preventDefault();
		scrollToCenter(target);
		window.history.pushState(null, null, hash);
	});

	// Manejar scroll al cargar página con hash (#)
	window.addEventListener('load', () => {
		if (window.location.hash) {
			const target = document.querySelector(window.location.hash);
			if (target) {
				setTimeout(() => scrollToCenter(target), 100);
			}
		}
	});

	// Manejar cambio de hash manual
	window.addEventListener('hashchange', () => {
		if (window.location.hash) {
			const target = document.querySelector(window.location.hash);
			if (target) {
				scrollToCenter(target);
			}
		}
	});
})();
