/**
 * Avance Nativo - Script principal
 */

document.addEventListener('DOMContentLoaded', function() {
	// Toggle del menú móvil
	const menuToggle = document.getElementById('menu-toggle');
	const primaryMenu = document.getElementById('primary-menu');

	if (menuToggle && primaryMenu) {
		menuToggle.addEventListener('click', function() {
			primaryMenu.classList.toggle('active');
			const isExpanded = primaryMenu.classList.contains('active');
			menuToggle.setAttribute('aria-expanded', isExpanded);
		});

		// Cerrar menú al hacer clic en un link
		const menuLinks = primaryMenu.querySelectorAll('a');
		menuLinks.forEach(link => {
			link.addEventListener('click', function() {
				if (window.innerWidth <= 768) {
					primaryMenu.classList.remove('active');
					menuToggle.setAttribute('aria-expanded', 'false');
				}
			});
		});

		// Cerrar menú cuando se redimensiona la ventana
		window.addEventListener('resize', function() {
			if (window.innerWidth > 768) {
				primaryMenu.classList.remove('active');
				menuToggle.setAttribute('aria-expanded', 'false');
			}
		});
	}

	// Validación básica de formularios
	const forms = document.querySelectorAll('form');
	forms.forEach(form => {
		form.addEventListener('submit', function(e) {
			// Validar campos requeridos
			const requiredFields = form.querySelectorAll('[required]');
			let isValid = true;

			requiredFields.forEach(field => {
				if (!field.value.trim()) {
					isValid = false;
					field.classList.add('error');
				} else {
					field.classList.remove('error');
				}
			});

			if (!isValid) {
				e.preventDefault();
				console.warn('Por favor completa todos los campos requeridos');
			}
		});
	});
});

// Smoothscroll polyfill para navegadores antiguos
if (!window.CSS || !window.CSS.supports('scroll-behavior', 'smooth')) {
	document.documentElement.style.scrollBehavior = 'auto';
}
