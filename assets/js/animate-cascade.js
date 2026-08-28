/**
 * Animate Cascade - Animaciones en cascara reutilizables con Intersection Observer
 * Aplica animaciones fadeInUp con delays automáticos cuando el usuario hace scroll
 *
 * Uso: <div class="animate-cascade"><div>Item 1</div><div>Item 2</div></div>
 *
 * @package Avance_Template
 */

class AnimateCascade {
	constructor(delayStep = 0.15) {
		this.delayStep = delayStep;
		this.animationDuration = 0.9;
		this.animationTiming = 'cubic-bezier(0.25, 0.46, 0.45, 0.94)';
		this.animatedContainers = new Set();
		this.init();
	}

	init() {
		// Configurar Intersection Observer
		const observerOptions = {
			root: null,
			rootMargin: '0px',
			threshold: 0
		};

		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting && !this.animatedContainers.has(entry.target)) {
					this.animateContainer(entry.target);
					this.animatedContainers.add(entry.target);
					// Nota: no desuscribir para permitir re-animaciones si es necesario
				}
			});
		}, observerOptions);

		// Observar todos los .animate-cascade
		const cascades = document.querySelectorAll('.animate-cascade');
		cascades.forEach((container) => {
			observer.observe(container);
		});
	}

	animateContainer(container) {
		const children = container.children;
		if (children.length === 0) return;

		Array.from(children).forEach((child, index) => {
			const delay = index * this.delayStep;
			const animationValue = `fadeInUp ${this.animationDuration}s ${this.animationTiming} ${delay}s forwards`;

			child.style.opacity = '0';
			child.style.transform = 'translateY(20px)';
			child.style.animation = animationValue;
			child.style.willChange = 'opacity, transform';
		});
	}
}

// Inicializar cuando DOM esté listo
function initAnimateCascade() {
	if (document.querySelectorAll('.animate-cascade').length > 0) {
		new AnimateCascade();
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initAnimateCascade);
} else {
	initAnimateCascade();
}
