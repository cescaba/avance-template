(function () {
	'use strict';

	const headerEl = document.getElementById('avance-header');
	const toggleBtn = document.getElementById('avance-menu-toggle');
	const menuPanel = document.getElementById('avance-menu-panel');

	if (!headerEl || !toggleBtn || !menuPanel) {
		return;
	}

	/**
	 * Update header height CSS variable for responsive calculations
	 */
	function updateHeaderHeight() {
		const container = headerEl.querySelector('.avance-header__container');
		if (container) {
			const height = Math.round(container.getBoundingClientRect().height);
			document.documentElement.style.setProperty('--header-h', height + 'px');
		}
	}

	/**
	 * Toggle menu open/closed state
	 * @param {boolean} isOpen - Whether menu should be open
	 */
	function setMenuOpen(isOpen) {
		if (isOpen) {
			// Abriendo: agregar is-open y remover closing
			headerEl.classList.add('is-open');
			menuPanel.classList.remove('closing');
			document.body.classList.add('is-locked');
			menuPanel.setAttribute('aria-hidden', 'false');
			toggleBtn.setAttribute('aria-expanded', 'true');
			toggleBtn.setAttribute('aria-label', 'Cerrar menú');
			updateHeaderHeight();
		} else {
			// Cerrando: agregar closing y esperar animación
			menuPanel.classList.add('closing');

			// Esperar a que termine la animación panelOut (200ms)
			setTimeout(function() {
				headerEl.classList.remove('is-open');
				document.body.classList.remove('is-locked');
				menuPanel.setAttribute('aria-hidden', 'true');
				toggleBtn.setAttribute('aria-expanded', 'false');
				toggleBtn.setAttribute('aria-label', 'Menú');
			}, 200);
		}
	}

	/**
	 * Toggle menu on button click
	 */
	toggleBtn.addEventListener('click', function () {
		const isCurrentlyOpen = headerEl.classList.contains('is-open');
		setMenuOpen(!isCurrentlyOpen);
	});

	/**
	 * Close menu when a link is clicked
	 */
	menuPanel.querySelectorAll('a').forEach(function (link) {
		link.addEventListener('click', function () {
			setMenuOpen(false);
		});
	});

	/**
	 * Close menu on Escape key
	 */
	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			setMenuOpen(false);
		}
	});

	/**
	 * Close menu when viewport transitions to desktop
	 */
	window.matchMedia('(min-width: 1025px)').addEventListener('change', function (event) {
		if (event.matches) {
			setMenuOpen(false);
		}
	});

	/**
	 * Update header height on window resize
	 */
	window.addEventListener('resize', updateHeaderHeight);

	// Initial setup
	updateHeaderHeight();
})();
