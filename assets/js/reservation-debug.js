/**
 * Reservation Debug - Debuguear flujo completo
 */

(function() {
	'use strict';

	// Log en consola
	window.avanceDebug = function(message, data) {
		console.log('%c[Avance Debug]', 'color: #0073aa; font-weight: bold;', message, data || '');
	};

	// Interceptar fetch original
	const originalFetch = window.fetch;
	window.fetch = function(...args) {
		const url = args[0];
		const options = args[1] || {};

		// Si es nuestro AJAX
		if (typeof url === 'string' && url.includes('admin-ajax.php')) {
			avanceDebug('📤 AJAX REQUEST:', {
				url: url,
				method: options.method || 'GET',
				body: args[1]?.body ? 'FormData enviado' : 'sin body'
			});
		}

		// Llamar al fetch original y debuguear respuesta
		return originalFetch.apply(this, args)
			.then(response => {
				if (typeof url === 'string' && url.includes('admin-ajax.php')) {
					avanceDebug('📥 AJAX RESPONSE:', {
						status: response.status,
						statusText: response.statusText,
						url: response.url
					});
				}
				return response;
			})
			.catch(error => {
				if (typeof url === 'string' && url.includes('admin-ajax.php')) {
					avanceDebug('❌ AJAX ERROR:', error.message);
				}
				throw error;
			});
	};

	// Interceptar redirecciones
	const originalLocationHref = Object.getOwnPropertyDescriptor(Location.prototype, 'href');
	Object.defineProperty(window.location, 'href', {
		get: originalLocationHref.get,
		set: function(url) {
			avanceDebug('🔄 REDIRECT DETECTADO:', url);
			console.log('%cURL de redirección:', 'color: #28a745; font-weight: bold;', url);
			return originalLocationHref.set.call(this, url);
		}
	});

	// Log cuando se carga
	avanceDebug('✅ Debug iniciado. Abre DevTools (F12) → Console para ver el flujo.');
})();
