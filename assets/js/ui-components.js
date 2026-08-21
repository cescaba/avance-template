/**
 * UI Components System
 * Gestiona: Acordeones FAQ + Menú móvil
 *
 * @package Avance_Template
 */

class FAQAccordion {
	constructor() {
		this.questions = document.querySelectorAll('.mentoria-faq__question');
		this.init();
	}

	init() {
		this.questions.forEach(question => {
			question.addEventListener('click', (e) => this.toggle(e));
		});
	}

	toggle(event) {
		const question = event.currentTarget;
		const item = question.closest('.mentoria-faq__item');

		if (!item) return;

		item.classList.toggle('is-active');
	}
}

class MobileMenu {
	constructor() {
		this.hamburger = document.querySelector('.site-header__hamburger');
		this.menu = document.querySelector('.site-header__menu');
		this.header = document.querySelector('.site-header');
		this.body = document.body;
		this.nextElement = this.header ? this.header.nextElementSibling : null;
		this.line1 = document.querySelector('.hamburger-line-1');
		this.line3 = document.querySelector('.hamburger-line-3');

		if (this.hamburger) {
			this.init();
		}
	}

	init() {
		this.hamburger.addEventListener('click', () => this.toggle());

		const menuLinks = document.querySelectorAll('.site-header__menu-list a');
		menuLinks.forEach(link => {
			link.addEventListener('click', () => this.close());
		});
	}

	toggle() {
		this.hamburger.classList.toggle('is-active');
		this.menu.classList.toggle('is-open');
		this.body.classList.toggle('menu-open');

		if (this.hamburger.classList.contains('is-active')) {
			this.line1.setAttribute('y1', '12');
			this.line1.setAttribute('y2', '12');
			this.line3.setAttribute('y1', '12');
			this.line3.setAttribute('y2', '12');
			this.updateMargin();
		} else {
			this.line1.setAttribute('y1', '6');
			this.line1.setAttribute('y2', '6');
			this.line3.setAttribute('y1', '18');
			this.line3.setAttribute('y2', '18');
			this.clearMargin();
		}
	}

	close() {
		this.hamburger.classList.remove('is-active');
		this.menu.classList.remove('is-open');
		this.body.classList.remove('menu-open');
		this.line1.setAttribute('y1', '6');
		this.line1.setAttribute('y2', '6');
		this.line3.setAttribute('y1', '18');
		this.line3.setAttribute('y2', '18');
		this.clearMargin();
	}

	updateMargin() {
		if (this.nextElement) {
			const menuHeight = this.menu.offsetHeight;
			this.nextElement.style.marginTop = menuHeight + 'px';
		}
	}

	clearMargin() {
		if (this.nextElement) {
			this.nextElement.style.marginTop = '0px';
		}
	}
}

// Inicializar componentes cuando el DOM esté listo
function initUIComponents() {
	// FAQ Accordion - Solo si existen elementos FAQ
	if (document.querySelectorAll('.mentoria-faq__question').length) {
		new FAQAccordion();
	}

	// Mobile Menu - Siempre inicializar si existe hamburger
	if (document.querySelector('.site-header__hamburger')) {
		new MobileMenu();
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initUIComponents);
} else {
	initUIComponents();
}
