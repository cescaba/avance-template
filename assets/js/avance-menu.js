(function () {
	'use strict';

	const headerEl = document.getElementById('avance-header');
	const toggleBtn = document.getElementById('avance-menu-toggle');
	const menuPanel = document.getElementById('avance-menu-panel');
	let isAnimating = false;

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
	 * Get current menu open state
	 */
	function isMenuOpen() {
		return headerEl.classList.contains('is-open');
	}

	/**
	 * Toggle menu open/closed state
	 * @param {boolean} isOpen - Whether menu should be open
	 */
	function setMenuOpen(isOpen) {
		if (isAnimating) return;
		if (isOpen === isMenuOpen()) return;

		isAnimating = true;

		if (isOpen) {
			headerEl.classList.add('is-open');
			menuPanel.classList.remove('closing');
			document.body.classList.add('is-locked');
			menuPanel.setAttribute('aria-hidden', 'false');
			toggleBtn.setAttribute('aria-expanded', 'true');
			toggleBtn.setAttribute('aria-label', 'Cerrar menú');
			updateHeaderHeight();
			isAnimating = false;
		} else {
			menuPanel.classList.add('closing');
			setTimeout(function() {
				headerEl.classList.remove('is-open');
				document.body.classList.remove('is-locked');
				menuPanel.setAttribute('aria-hidden', 'true');
				toggleBtn.setAttribute('aria-expanded', 'false');
				toggleBtn.setAttribute('aria-label', 'Menú');
				menuPanel.classList.remove('closing');
				isAnimating = false;
			}, 200);
		}
	}

	/**
	 * Toggle menu on button click
	 */
	toggleBtn.addEventListener('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		setMenuOpen(!isMenuOpen());
	});

	/**
	 * Close menu when clicking outside (pero no el botón ni el header)
	 * Se ejecuta con un pequeño delay para evitar interferencia con el toggle
	 */
	document.addEventListener('click', function (e) {
		if (!isMenuOpen()) return;
		if (headerEl.contains(e.target)) return;

		setTimeout(function() {
			if (isMenuOpen()) {
				setMenuOpen(false);
			}
		}, 50);
	});

	/**
	 * Close menu when a link is clicked
	 */
	menuPanel.querySelectorAll('a').forEach(function (link) {
		link.addEventListener('click', function (e) {
			e.stopPropagation();
			setMenuOpen(false);
		});
	});

	/**
	 * Close menu on Escape key
	 */
	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && isMenuOpen()) {
			setMenuOpen(false);
		}
	});

	/**
	 * Close menu when viewport transitions to desktop
	 */
	window.matchMedia('(min-width: 1025px)').addEventListener('change', function (event) {
		if (event.matches && isMenuOpen()) {
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
