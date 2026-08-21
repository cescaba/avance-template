/**
 * Animations System
 * Gestiona: Scroll animations + Counter animations
 * Optimizado para performance: Intersection Observer + requestAnimationFrame
 *
 * @package Avance_Template
 */

class AnimationsSystem {
	constructor() {
		this.counters = [];
		this.animatingCounters = new Set();
		this.initScrollAnimations();
		this.initCounterAnimations();
	}

	/**
	 * Inicializa scroll animations para elementos con [data-animate]
	 */
	initScrollAnimations() {
		if (!('IntersectionObserver' in window)) {
			document.documentElement.classList.add('no-intersection-observer');
			return;
		}

		const options = {
			threshold: 0.1,
			rootMargin: '0px 0px -50px 0px'
		};

		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting && entry.target.classList.contains('animate-on-scroll')) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		}, options);

		document.querySelectorAll('[data-animate]').forEach(element => {
			observer.observe(element);
		});
	}

	/**
	 * Inicializa counter animations para .home-features__stat
	 */
	initCounterAnimations() {
		if (!('IntersectionObserver' in window)) {
			return;
		}

		const statsElements = document.querySelectorAll('.home-features__stat');

		if (!statsElements.length) return;

		const observer = new IntersectionObserver(
			(entries) => this.handleCounterIntersection(entries),
			{
				threshold: 0.1,
				rootMargin: '0px'
			}
		);

		statsElements.forEach((element) => {
			// Primero intenta usar data-value si existe
			let counter = null;
			if (element.dataset.value) {
				const value = parseInt(element.dataset.value, 10);
				const suffix = element.dataset.suffix || '';
				counter = { value, prefix: '', suffix, original: element.textContent };
			} else {
				// Fallback: parsear del textContent
				counter = this.parseCounter(element.textContent);
			}

			if (counter) {
				this.counters.push({
					element,
					...counter,
					animated: false
				});
				observer.observe(element);
			}
		});
	}

	/**
	 * Parsea el texto y extrae número, prefijo y sufijo
	 * Ej: "+50" → { value: 50, prefix: "+", suffix: "" }
	 *     "100%" → { value: 100, prefix: "", suffix: "%" }
	 */
	parseCounter(text) {
		const trimmed = text.trim();

		const prefixMatch = trimmed.match(/^[^\d]*/);
		const prefix = prefixMatch ? prefixMatch[0] : '';

		const suffixMatch = trimmed.match(/[^\d]*$/);
		const suffix = suffixMatch ? suffixMatch[0] : '';

		const numberMatch = trimmed.match(/\d+/);
		if (!numberMatch) return null;

		const value = parseInt(numberMatch[0], 10);

		return { value, prefix, suffix, original: trimmed };
	}

	/**
	 * Manejador del Intersection Observer para counters
	 */
	handleCounterIntersection(entries) {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				const counter = this.counters.find((c) => c.element === entry.target);
				if (counter && !counter.animated) {
					this.animateCounter(counter);
					counter.animated = true;
				}
			}
		});
	}

	/**
	 * Easing function - Cubic Bezier Premium
	 */
	easingPremium(t) {
		if (t < 0.5) {
			return 4 * t * t * t;
		}
		const f = 2 * t - 2;
		return 0.5 * f * f * f + 1;
	}

	/**
	 * Anima un contador individual
	 */
	animateCounter(counter) {
		const { element, value, prefix, suffix } = counter;
		const duration = 2800;
		const startTime = performance.now();
		let animationId;

		const animate = (currentTime) => {
			const elapsed = currentTime - startTime;
			const progress = Math.min(elapsed / duration, 1);

			const easedProgress = this.easingPremium(progress);
			const currentValue = Math.floor(value * easedProgress);

			element.textContent = `${prefix}${currentValue}${suffix}`;

			if (progress < 1) {
				animationId = requestAnimationFrame(animate);
			} else {
				element.textContent = `${prefix}${value}${suffix}`;
				this.animatingCounters.delete(animationId);
			}
		};

		animationId = requestAnimationFrame(animate);
		this.animatingCounters.add(animationId);
	}
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => {
		new AnimationsSystem();
	});
} else {
	new AnimationsSystem();
}
