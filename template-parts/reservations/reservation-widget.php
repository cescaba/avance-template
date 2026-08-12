<?php
/**
 * Reservation Widget Template Part - Premium Design
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

$products = Avance_Reservations_Manager::get_reservation_products();

if (empty($products)) {
	echo '<p style="text-align: center; color: #999;">No hay mentorías disponibles en este momento.</p>';
	return;
}

// Preparar datos de productos con información adicional
$products_formatted = array();
foreach ($products as $index => $product) {
	$products_formatted[] = array(
		'id' => $product['id'],
		'title' => $product['title'],
		'price' => $product['price'],
		'description' => $product['description'],
		'meta' => $index === 1 ? 'Para crecer acelerado' : ($index === 0 ? 'Para empezar' : 'Máximo acompañamiento'),
		'recommended' => $index === 1 ? true : false,
		'unit' => 'sesión',
		'features' => array(
			'Sesiones 1:1 personalizadas',
			'Plan de acción concreto',
			'Acceso directo',
		),
	);
}
?>

<div class="avance-reservations-widget">
	<div class="reservations-container">
		<div class="reservations-grid">

			<!-- COLUMNA IZQUIERDA: PLANES -->
			<div class="reservations-left">
				<div class="reservations-kicker">Elige tu plan y agenda</div>
				<h1 class="reservations-title">Reserva tu primera sesión</h1>

				<div class="plans-list">
					<?php foreach ($products_formatted as $product) : ?>
						<label class="plan-card<?php echo !empty($product['recommended']) ? ' is-selected' : ''; ?>" data-product-id="<?php echo esc_attr($product['id']); ?>">
							<input type="radio" name="plan" class="plan-card__radio" value="<?php echo esc_attr($product['id']); ?>" <?php echo !empty($product['recommended']) ? 'checked' : ''; ?>>
							<span class="plan-card__dot"></span>
							<div class="plan-card__main">
								<div class="plan-card__top">
									<span class="plan-card__name"><?php echo esc_html($product['title']); ?></span>
									<?php if (!empty($product['recommended'])) : ?>
										<span class="plan-card__badge">Recomendado</span>
									<?php endif; ?>
								</div>
								<div class="plan-card__meta"><?php echo esc_html($product['meta']); ?></div>
								<?php if (!empty($product['features'])) : ?>
									<div class="plan-card__features">
										<?php foreach ($product['features'] as $feature) : ?>
											<div class="plan-card__feature">✓ <?php echo esc_html($feature); ?></div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
							<div class="plan-card__price"><?php echo wc_price($product['price']); ?><span>/<?php echo esc_html($product['unit']); ?></span></div>
						</label>
					<?php endforeach; ?>
				</div>

				<div class="reservations-testimonial">
					<p class="reservations-testimonial__quote">"La mentoría fue un antes y un después. En 3 meses reorganicé mi equipo, redefiní mi propuesta de valor y cerré los mejores contratos de mi carrera."</p>
					<div class="reservations-testimonial__author">
						<div class="reservations-testimonial__avatar"></div>
						<div>
							<div class="reservations-testimonial__name">Ana P.</div>
							<div class="reservations-testimonial__role">Directora Comercial, Lima</div>
						</div>
					</div>
				</div>
			</div>

			<!-- COLUMNA DERECHA: AGENDA + FORM -->
			<div class="reservations-right">
				<div class="agenda-card">
					<div class="agenda-card__kicker">Agenda tu sesión</div>
					<div class="agenda-card__selected" id="agenda-selected-label">Selecciona un plan</div>

					<div class="calendar-host">
						<div class="calendar-avatar">AE</div>
						<div class="calendar-host-info">
							<div class="calendar-host-name">Avance Empresarial</div>
							<div class="calendar-host-meta">Sesión de diagnóstico · 30 min · Google Meet</div>
						</div>
						<span class="calendar-free-dot"></span>
					</div>

					<div class="calendar-nav">
						<button type="button" class="calendar-nav-btn" id="cal-prev">‹</button>
						<div class="calendar-month-label" id="cal-month-label"></div>
						<button type="button" class="calendar-nav-btn" id="cal-next">›</button>
					</div>

					<div class="calendar-weekdays">
						<div>DOM</div><div>LUN</div><div>MAR</div><div>MIÉ</div><div>JUE</div><div>VIE</div><div>SÁB</div>
					</div>
					<div class="calendar-days" id="cal-days"></div>

					<div class="calendar-tz"><span>◷</span> Lima (GMT-5)</div>
					<div class="calendar-integrations">
						<span>◻ Google Calendar</span>
						<span>◻ Google Meet</span>
					</div>
				</div>

				<div class="reservation-form-card">
					<div class="reservation-form-kicker">Tus datos</div>
					<form id="reservation-form">
						<?php wp_nonce_field('avance_reservation_form', 'nonce', false); ?>
						<input type="hidden" id="reservation-product-id" name="product_id" value="">
						<input type="hidden" id="reservation-fecha" name="fecha_reserva" value="">

						<div class="reservation-row-2">
							<div class="reservation-form-group">
								<label for="reservation-nombre" class="reservation-label">Nombre <span class="required">*</span></label>
								<input type="text" id="reservation-nombre" name="nombre" class="reservation-input" placeholder="Tu nombre" required>
							</div>
							<div class="reservation-form-group">
								<label for="reservation-whatsapp" class="reservation-label">WhatsApp <span class="required">*</span></label>
								<input type="tel" id="reservation-whatsapp" name="whatsapp" class="reservation-input" placeholder="+51 999 000 000" required>
							</div>
						</div>

						<div class="reservation-form-group">
							<label for="reservation-email" class="reservation-label">Email <span class="required">*</span></label>
							<input type="email" id="reservation-email" name="email" class="reservation-input" placeholder="tu@email.com" required>
						</div>

						<div class="reservation-form-group">
							<label for="reservation-notas" class="reservation-label">¿Cuál es tu principal desafío ahora mismo?</label>
							<textarea id="reservation-notas" name="notas" class="reservation-textarea" placeholder="Cuéntame en pocas palabras..." rows="3"></textarea>
						</div>

						<button type="submit" class="reservation-button" id="reservation-submit">Confirmar reserva</button>

						<div class="reservation-payment-methods">
							<label><input type="checkbox" disabled> Visa / Mastercard</label>
							<label><input type="checkbox" disabled> Transferencia</label>
							<label><input type="checkbox" disabled> Yape / Plin</label>
						</div>
						<div class="reservation-payment-note">( Pago 100% seguro. Confirmación inmediata por email y WhatsApp )</div>
					</form>
				</div>

				<button type="button" class="reservation-whatsapp-btn">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.87.5 3.62 1.44 5.12L2 22l5.15-1.54a9.87 9.87 0 0 0 4.87 1.28h.01c5.46 0 9.9-4.45 9.9-9.91C21.94 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.36-.53.06-1.02.28-3.44-.72-2.9-1.19-4.77-4.1-4.92-4.3-.14-.2-1.18-1.57-1.18-3s.72-2.13.98-2.42c.26-.29.57-.36.76-.36.19 0 .38.002.55.01.18.008.42-.07.65.5.24.6.82 2.02.9 2.17.07.14.12.31.02.5-.1.19-.15.31-.29.48-.15.17-.31.38-.44.51-.14.14-.29.29-.13.57.17.29.75 1.24 1.62 2.01 1.12.99 2.06 1.3 2.35 1.44.29.14.46.12.63-.07.18-.19.75-.87.95-1.17.19-.29.39-.24.65-.14.26.1 1.66.78 1.94.92.29.14.48.21.55.33.07.12.07.68-.17 1.36z"/></svg>
					Prefiero consultar por WhatsApp primero
				</button>
			</div>
		</div>
	</div>
</div>

<script>
(function () {
	var monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
	var today = new Date();
	var cal = { year: today.getFullYear(), month: today.getMonth(), selectedDay: today.getDate() };
	var labelEl = document.getElementById('cal-month-label');
	var daysEl = document.getElementById('cal-days');
	var fechaInput = document.getElementById('reservation-fecha');

	function pad(n) { return n < 10 ? '0' + n : '' + n; }

	function renderCalendar() {
		labelEl.textContent = monthNames[cal.month] + ' ' + cal.year;
		daysEl.innerHTML = '';
		var firstDay = new Date(cal.year, cal.month, 1).getDay();
		var daysInMonth = new Date(cal.year, cal.month + 1, 0).getDate();
		for (var i = 0; i < firstDay; i++) {
			var empty = document.createElement('div');
			empty.className = 'calendar-day is-empty';
			daysEl.appendChild(empty);
		}
		var todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
		for (var d = 1; d <= daysInMonth; d++) {
			var cellDate = new Date(cal.year, cal.month, d);
			var cell = document.createElement('div');
			cell.className = 'calendar-day' + (d === cal.selectedDay ? ' is-selected' : '') + (cellDate < todayDate ? ' is-empty' : '');
			cell.textContent = d;
			if (cellDate >= todayDate) {
				cell.addEventListener('click', (function (day) {
					return function () {
						cal.selectedDay = day;
						fechaInput.value = cal.year + '-' + pad(cal.month + 1) + '-' + pad(day);
						renderCalendar();
					};
				})(d));
			}
			daysEl.appendChild(cell);
		}
	}
	document.getElementById('cal-prev').addEventListener('click', function () { cal.month--; if (cal.month < 0) { cal.month = 11; cal.year--; } renderCalendar(); });
	document.getElementById('cal-next').addEventListener('click', function () { cal.month++; if (cal.month > 11) { cal.month = 0; cal.year++; } renderCalendar(); });
	renderCalendar();

	var cards = document.querySelectorAll('.plan-card');
	var selectedLabel = document.getElementById('agenda-selected-label');
	var productIdInput = document.getElementById('reservation-product-id');
	var submitBtn = document.getElementById('reservation-submit');

	function selectCard(card) {
		cards.forEach(function (c) { c.classList.remove('is-selected'); });
		card.classList.add('is-selected');
		card.querySelector('.plan-card__radio').checked = true;
		var name = card.querySelector('.plan-card__name').textContent.trim();
		var price = card.querySelector('.plan-card__price').textContent.trim();
		selectedLabel.textContent = name + ' · ' + price;
		submitBtn.textContent = 'Confirmar reserva · ' + price + ' →';
		productIdInput.value = card.getAttribute('data-product-id');
	}

	cards.forEach(function (card) {
		card.addEventListener('click', function () { selectCard(card); });
	});

	var preselected = document.querySelector('.plan-card.is-selected') || cards[0];
	if (preselected) selectCard(preselected);
})();
</script>
