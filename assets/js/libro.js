/**
 * Libro - Compra de libro B2B
 */

function comprarLibro() {
	const ajaxUrl = window.LIBRO_CONFIG?.ajaxUrl;
	const checkoutUrl = window.LIBRO_CONFIG?.checkoutUrl;

	if (!ajaxUrl || !checkoutUrl) {
		console.error('Configuración de compra incompleta', window.LIBRO_CONFIG);
		return;
	}

	fetch(ajaxUrl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'action=comprar_libro'
	})
	.then(response => {
		if (!response.ok) throw new Error(`HTTP ${response.status}`);
		window.location.href = checkoutUrl;
	})
	.catch(error => {
		console.error('Error en compra:', error);
		window.location.href = checkoutUrl;
	});
}
