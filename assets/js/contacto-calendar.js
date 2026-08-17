/**
 * Contacto Page Calendar
 *
 * Initializes Flatpickr date picker for scheduling
 */

(function() {
	// Check if flatpickr is available
	if (typeof flatpickr === 'undefined') {
		return;
	}

	// Get today's date for minimum date
	const today = new Date();

	// Initialize Consultoría date picker
	if (document.getElementById('contacto-fecha-consultoría')) {
		flatpickr('#contacto-fecha-consultoría', {
			minDate: today,
			dateFormat: 'd/m/Y',
			locale: 'es',
			disable: [
				// Disable past dates
				function(date) {
					return date < today;
				}
			],
			onChange: function(selectedDates) {
			}
		});
	}

	// Initialize Mentoría date picker
	if (document.getElementById('contacto-fecha-mentoría')) {
		flatpickr('#contacto-fecha-mentoría', {
			minDate: today,
			dateFormat: 'd/m/Y',
			locale: 'es',
			disable: [
				// Disable past dates
				function(date) {
					return date < today;
				}
			],
			onChange: function(selectedDates) {
			}
		});
	}
})();
