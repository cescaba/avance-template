/**
 * Counter Animation
 * Anima números de estadísticas desde 0 hasta el valor final
 * Optimizado para performance: Intersection Observer + requestAnimationFrame
 */

class CounterAnimation {
	constructor() {
		this.counters = [];
		this.animatingCounters = new Set();
		this.init();
	}

	init() {
		// Obtener todos los elementos con clase home-features__stat
		const statsElements = document.querySelectorAll('.home-features__stat');

		if (!statsElements.length) return;

		// Crear observador para detectar cuando el elemento entra en la vista
		const observer = new IntersectionObserver(
			(entries) => this.handleIntersection(entries),
			{
				threshold: 0.1, // Activar cuando el 10% del elemento sea visible
				rootMargin: '0px'
			}
		);

		// Procesar cada estadística
		statsElements.forEach((element) => {
			const counter = this.parseCounter(element.textContent);
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

		// Extraer prefijo (caracteres no numéricos al inicio)
		const prefixMatch = trimmed.match(/^[^\d]*/);
		const prefix = prefixMatch ? prefixMatch[0] : '';

		// Extraer sufijo (caracteres no numéricos al final)
		const suffixMatch = trimmed.match(/[^\d]*$/);
		const suffix = suffixMatch ? suffixMatch[0] : '';

		// Extraer número
		const numberMatch = trimmed.match(/\d+/);
		if (!numberMatch) return null;

		const value = parseInt(numberMatch[0], 10);

		return { value, prefix, suffix, original: trimmed };
	}

	/**
	 * Manejador del Intersection Observer
	 * Se ejecuta cuando el elemento entra/sale de la vista
	 */
	handleIntersection(entries) {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				const counter = this.counters.find((c) => c.element === entry.target);
				if (counter && !counter.animated) {
					this.animateCounter(counter);
					counter.animated = true; // Solo animar una vez
				}
			}
		});
	}

	/**
	 * Easing function - Cubic Bezier Premium
	 * Crea una animación suave con desaceleración elegante al final
	 * Similar a: cubic-bezier(0.25, 0.46, 0.45, 0.94)
	 */
	easingPremium(t) {
		// Cubic Bezier custom para animación más premium
		if (t < 0.5) {
			// Primera mitad: aceleración rápida
			return 4 * t * t * t;
		}
		// Segunda mitad: desaceleración suave y elegante
		const f = 2 * t - 2;
		return 0.5 * f * f * f + 1;
	}

	/**
	 * Anima un contador individual
	 * Usa requestAnimationFrame para optimizar performance
	 */
	animateCounter(counter) {
		const { element, value, prefix, suffix } = counter;
		const duration = 2800; // 2.8 segundos (un poco más para efecto premium)
		const startTime = performance.now();
		let animationId;

		const animate = (currentTime) => {
			const elapsed = currentTime - startTime;
			const progress = Math.min(elapsed / duration, 1); // 0 a 1

			// Usar easing premium para animación suave
			const easedProgress = this.easingPremium(progress);
			const currentValue = Math.floor(value * easedProgress);

			// Actualizar el texto del elemento
			element.textContent = `${prefix}${currentValue}${suffix}`;

			// Continuar animación si no hemos terminado
			if (progress < 1) {
				animationId = requestAnimationFrame(animate);
			} else {
				// Asegurar que llega al valor final exacto
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
		new CounterAnimation();
	});
} else {
	new CounterAnimation();
}
