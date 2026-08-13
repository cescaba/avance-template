document.addEventListener('DOMContentLoaded', () => {
	const hamburger = document.querySelector('.site-header__hamburger');
	const menu = document.querySelector('.site-header__menu');
	const header = document.querySelector('.site-header');
	const body = document.body;
	const nextElement = header.nextElementSibling;
	const line1 = document.querySelector('.hamburger-line-1');
	const line3 = document.querySelector('.hamburger-line-3');

	if (hamburger) {
		hamburger.addEventListener('click', () => {
			hamburger.classList.toggle('is-active');
			menu.classList.toggle('is-open');
			body.classList.toggle('menu-open');

			// Cambiar posición de líneas para la X
			if (hamburger.classList.contains('is-active')) {
				line1.setAttribute('y1', '12');
				line1.setAttribute('y2', '12');
				line3.setAttribute('y1', '12');
				line3.setAttribute('y2', '12');
			} else {
				line1.setAttribute('y1', '6');
				line1.setAttribute('y2', '6');
				line3.setAttribute('y1', '18');
				line3.setAttribute('y2', '18');
			}

			// Desplazar contenido - calcular altura del menú
			if (menu.classList.contains('is-open') && nextElement) {
				const menuHeight = menu.offsetHeight;
				nextElement.style.marginTop = menuHeight + 'px';
			} else if (nextElement) {
				nextElement.style.marginTop = '0px';
			}
		});

		// Cerrar menú al hacer clic en un enlace
		const menuLinks = document.querySelectorAll('.site-header__menu-list a');
		menuLinks.forEach(link => {
			link.addEventListener('click', () => {
				hamburger.classList.remove('is-active');
				menu.classList.remove('is-open');
				body.classList.remove('menu-open');
				line1.setAttribute('y1', '6');
				line1.setAttribute('y2', '6');
				line3.setAttribute('y1', '18');
				line3.setAttribute('y2', '18');
				if (nextElement) nextElement.style.marginTop = '0px';
			});
		});
	}
});
