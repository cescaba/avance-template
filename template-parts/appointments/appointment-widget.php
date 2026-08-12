<?php
/**
 * Appointment Widget Template Part
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="avance-appointment-widget">
	<div class="appointment-container">
		<!-- TOPBAR -->
		<div class="appointment-topbar">
			<div>
				<div class="appointment-kicker">Agendamiento Directo</div>
				<h1 class="appointment-title">Sesión gratuita de 10 min</h1>
				<div class="appointment-subtitle">Google Meet · Sin compromiso · Confirmación en minutos</div>
			</div>
			<div class="appointment-actions">
				<button type="button" class="appointment-btn-outline">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.87.5 3.62 1.44 5.12L2 22l5.15-1.54a9.87 9.87 0 0 0 4.87 1.28h.01c5.46 0 9.9-4.45 9.9-9.91C21.94 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.36-.53.06-1.02.28-3.44-.72-2.9-1.19-4.77-4.1-4.92-4.3-.14-.2-1.18-1.57-1.18-3s.72-2.13.98-2.42c.26-.29.57-.36.76-.36.19 0 .38.002.55.01.18.008.42-.07.65.5.24.6.82 2.02.9 2.17.07.14.12.31.02.5-.1.19-.15.31-.29.48-.15.17-.31.38-.44.51-.14.14-.29.29-.13.57.17.29.75 1.24 1.62 2.01 1.12.99 2.06 1.3 2.35 1.44.29.14.46.12.63-.07.18-.19.75-.87.95-1.17.19-.29.39-.24.65-.14.26.1 1.66.78 1.94.92.29.14.48.21.55.33.07.12.07.68-.17 1.36z"/></svg>
					Prefiero WhatsApp
				</button>
				<button type="button" class="appointment-btn-solid">Ir a contacto</button>
			</div>
		</div>

		<!-- MAIN GRID -->
		<div class="appointment-grid">

			<!-- CALENDAR CARD -->
			<div class="appointment-card appointment-calendar-card">
				<div class="calendar-host">
					<div class="calendar-avatar">AA</div>
					<div class="calendar-host-info">
						<div class="calendar-host-name">Avance Empresarial</div>
						<div class="calendar-host-meta">Sesión de diagnóstico · 30 min · Google Meet</div>
					</div>
					<span class="calendar-free-tag">Gratis</span>
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

				<div class="calendar-tz">
					<span>◷</span> Lima (GMT-5)
				</div>
				<div class="calendar-integrations">
					<span>◻ Google Calendar</span>
					<span>◻ Google Meet</span>
				</div>
				<div class="calendar-link">avance-empresarial.com/citas</div>
			</div>

			<!-- FORM CARD -->
			<div class="appointment-card appointment-form-card">
				<h2 class="appointment-form-heading">¿QUÉ TE GUSTARÍA TRATAR?</h2>
				<p class="appointment-form-lead">Selecciona el tema principal para que pueda preparar la sesión con información relevante para tu caso.</p>

				<form id="appointment-form">
					<?php wp_nonce_field('avance_appointment_form', 'nonce', false); ?>

					<div class="appointment-row-2">
						<div class="appointment-form-group">
							<label for="appointment-nombre" class="appointment-label">Nombre Completo <span class="required">*</span></label>
							<input type="text" id="appointment-nombre" name="appointment-nombre" class="appointment-input" placeholder="Tu nombre completo" required>
						</div>
						<div class="appointment-form-group">
							<label for="appointment-whatsapp" class="appointment-label">WhatsApp <span class="required">*</span></label>
							<input type="tel" id="appointment-whatsapp" name="appointment-whatsapp" class="appointment-input" placeholder="+51 999 000 000" required>
						</div>
					</div>

					<div class="appointment-form-group">
						<select id="appointment-servicio" name="appointment-servicio" class="appointment-input appointment-select" required>
							<option value="">Selecciona un servicio o tema...</option>
							<option value="Capacitación">Capacitación</option>
							<option value="Consultoría Comercial">Consultoría Comercial</option>
							<option value="Mentoría 1:1">Mentoría 1:1</option>
							<option value="Otro">Otro</option>
						</select>
					</div>

					<input type="hidden" id="appointment-fecha" name="appointment-fecha" value="">
					<input type="hidden" id="appointment-hora" name="appointment-hora" value="09:00">

					<p class="appointment-hint">Al seleccionar el tema, verás qué puntos trataremos en la sesión.</p>

					<button type="submit" class="appointment-button">Agendar Reunión</button>

					<div class="appointment-trust-row">
						<div class="appointment-trust-item">
							<div class="appointment-trust-title">Sin compromiso</div>
							<div class="appointment-trust-desc">100% gratuita</div>
						</div>
						<div class="appointment-trust-item">
							<div class="appointment-trust-title">Respuesta rápida</div>
							<div class="appointment-trust-desc">Confirmación en minutos</div>
						</div>
						<div class="appointment-trust-item">
							<div class="appointment-trust-title">Flexible</div>
							<div class="appointment-trust-desc">Reagenda cuando quieras</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
	.avance-appointment-widget {
		background: #f6f6f8;
		padding: 48px 24px;
		font-family: 'Inter', system-ui, sans-serif;
		color: #1c1c28;
	}

	.appointment-container {
		max-width: 1100px;
		margin: 0 auto;
	}

	/* TOPBAR */
	.appointment-topbar {
		display: flex;
		justify-content: space-between;
		align-items: flex-start;
		flex-wrap: wrap;
		gap: 16px;
		margin-bottom: 32px;
	}

	.appointment-kicker {
		font-size: 12px;
		font-weight: 600;
		letter-spacing: .08em;
		color: #9a9aa5;
		text-transform: uppercase;
		margin-bottom: 10px;
	}

	.appointment-title {
		font-size: 32px;
		font-weight: 700;
		margin: 0 0 8px 0;
		color: #151520;
	}

	.appointment-subtitle {
		font-size: 14px;
		color: #9a9aa5;
	}

	.appointment-actions {
		display: flex;
		gap: 12px;
		align-items: center;
	}

	.appointment-btn-outline {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 11px 18px;
		border: 1px solid #e2e2e8;
		border-radius: 8px;
		background: #fff;
		color: #1c1c28;
		font-size: 14px;
		font-weight: 600;
		font-family: inherit;
		cursor: pointer;
		transition: background .15s ease;
	}
	.appointment-btn-outline:hover { background: #f2f2f5; }

	.appointment-btn-solid {
		padding: 11px 20px;
		border: none;
		border-radius: 8px;
		background: #1c1c28;
		color: #fff;
		font-size: 14px;
		font-weight: 600;
		font-family: inherit;
		cursor: pointer;
		transition: background .15s ease;
	}
	.appointment-btn-solid:hover { background: #2c2c3a; }

	/* GRID */
	.appointment-grid {
		display: grid;
		grid-template-columns: 340px 1fr;
		gap: 24px;
		align-items: start;
	}

	.appointment-card {
		background: #fff;
		border: 1px solid #eaeaef;
		border-radius: 12px;
	}

	/* CALENDAR CARD */
	.appointment-calendar-card { padding: 24px; }

	.calendar-host {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-bottom: 20px;
	}

	.calendar-avatar {
		width: 36px;
		height: 36px;
		border-radius: 50%;
		background: #1c1c28;
		color: #fff;
		font-size: 12px;
		font-weight: 700;
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
	}

	.calendar-host-info { flex: 1; min-width: 0; }
	.calendar-host-name { font-size: 14px; font-weight: 600; color: #151520; }
	.calendar-host-meta { font-size: 12px; color: #9a9aa5; }

	.calendar-free-tag {
		font-size: 11px;
		font-weight: 600;
		color: #3a9a5c;
		background: #eaf7ef;
		padding: 4px 10px;
		border-radius: 20px;
		white-space: nowrap;
	}

	.calendar-nav {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-bottom: 14px;
	}

	.calendar-nav-btn {
		border: none;
		background: none;
		color: #c0c0c8;
		font-size: 16px;
		cursor: pointer;
		padding: 4px 8px;
		transition: color .15s ease;
	}
	.calendar-nav-btn:hover { color: #1c1c28; }

	.calendar-month-label { font-size: 14px; font-weight: 600; color: #151520; }

	.calendar-weekdays {
		display: grid;
		grid-template-columns: repeat(7, 1fr);
		text-align: center;
		margin-bottom: 6px;
	}
	.calendar-weekdays div {
		font-size: 10px;
		font-weight: 600;
		color: #b0b0b8;
		padding: 6px 0;
	}

	.calendar-days {
		display: grid;
		grid-template-columns: repeat(7, 1fr);
		gap: 2px;
	}

	.calendar-day {
		aspect-ratio: 1;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 13px;
		border-radius: 8px;
		cursor: pointer;
		color: #1c1c28;
		background: transparent;
		transition: background .15s ease, color .15s ease;
	}
	.calendar-day:hover { background: #eef0f3; }
	.calendar-day.is-empty { cursor: default; }
	.calendar-day.is-empty:hover { background: transparent; }
	.calendar-day.is-selected {
		background: #1c1c28;
		color: #fff;
		font-weight: 600;
	}
	.calendar-day.is-selected:hover { background: #1c1c28; }

	.calendar-tz {
		display: flex;
		align-items: center;
		gap: 6px;
		margin-top: 18px;
		padding-top: 16px;
		border-top: 1px solid #f0f0f4;
		font-size: 12px;
		color: #9a9aa5;
	}

	.calendar-integrations {
		display: flex;
		gap: 16px;
		margin-top: 10px;
		font-size: 11px;
		color: #b0b0b8;
	}

	.calendar-link { font-size: 11px; color: #c0c0c8; margin-top: 4px; }

	/* FORM CARD */
	.appointment-form-card { padding: 32px; }

	.appointment-form-heading {
		font-size: 15px;
		font-weight: 700;
		margin: 0 0 8px 0;
		color: #151520;
		letter-spacing: .02em;
	}

	.appointment-form-lead {
		font-size: 13px;
		color: #9a9aa5;
		margin: 0 0 24px 0;
		line-height: 1.5;
	}

	.appointment-row-2 {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
		margin-bottom: 20px;
	}

	.appointment-form-group { margin-bottom: 16px; }

	.appointment-label {
		display: block;
		font-size: 13px;
		font-weight: 600;
		color: #151520;
		margin-bottom: 8px;
	}

	.required { color: #e0555f; }

	.appointment-input,
	.appointment-select {
		width: 100%;
		padding: 12px 14px;
		border: 1px solid #e2e2e8;
		border-radius: 8px;
		font-size: 14px;
		font-family: inherit;
		color: #1c1c28;
		background: #fff;
		transition: border-color .2s ease, box-shadow .2s ease;
	}

	.appointment-input:focus,
	.appointment-select:focus {
		outline: none;
		border-color: #1c1c28;
		box-shadow: 0 0 0 3px #ececef;
	}

	.appointment-select { appearance: none; }

	.appointment-hint {
		text-align: center;
		font-size: 12px;
		color: #b0b0b8;
		font-style: italic;
		margin: 0 0 24px 0;
	}

	.appointment-button {
		padding: 13px 26px;
		border: none;
		border-radius: 8px;
		background: #1c1c28;
		color: #fff;
		font-size: 14px;
		font-weight: 600;
		font-family: inherit;
		cursor: pointer;
		margin-bottom: 32px;
		transition: background .15s ease;
	}
	.appointment-button:hover { background: #2c2c3a; }

	.appointment-trust-row {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 16px;
		padding-top: 24px;
		border-top: 1px solid #f0f0f4;
	}

	.appointment-trust-item { text-align: center; }
	.appointment-trust-title { font-size: 13px; font-weight: 600; color: #151520; margin-bottom: 4px; }
	.appointment-trust-desc { font-size: 11px; color: #b0b0b8; }

	@media (max-width: 760px) {
		.appointment-grid { grid-template-columns: 1fr; }
		.appointment-row-2 { grid-template-columns: 1fr; }
		.appointment-trust-row { grid-template-columns: 1fr; gap: 12px; }
	}

	@media (max-width: 600px) {
		.avance-appointment-widget { padding: 30px 15px; }
		.appointment-title { font-size: 24px; }
		.appointment-input, .appointment-select, .appointment-button { font-size: 16px; }
	}
</style>

<script>
	(function () {
		var monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
		var today = new Date();
		var state = { year: today.getFullYear(), month: today.getMonth(), selectedDay: today.getDate() };

		var labelEl = document.getElementById('cal-month-label');
		var daysEl = document.getElementById('cal-days');
		var fechaInput = document.getElementById('appointment-fecha');

		function pad(n) { return n < 10 ? '0' + n : '' + n; }

		function render() {
			labelEl.textContent = monthNames[state.month] + ' ' + state.year;
			daysEl.innerHTML = '';
			var firstDay = new Date(state.year, state.month, 1).getDay();
			var daysInMonth = new Date(state.year, state.month + 1, 0).getDate();

			for (var i = 0; i < firstDay; i++) {
				var empty = document.createElement('div');
				empty.className = 'calendar-day is-empty';
				daysEl.appendChild(empty);
			}

			for (var d = 1; d <= daysInMonth; d++) {
				var cell = document.createElement('div');
				cell.className = 'calendar-day' + (d === state.selectedDay ? ' is-selected' : '');
				cell.textContent = d;
				cell.addEventListener('click', (function (day) {
					return function () {
						state.selectedDay = day;
						fechaInput.value = state.year + '-' + pad(state.month + 1) + '-' + pad(day);
						render();
					};
				})(d));
				daysEl.appendChild(cell);
			}
		}

		document.getElementById('cal-prev').addEventListener('click', function () {
			state.month--;
			if (state.month < 0) { state.month = 11; state.year--; }
			render();
		});
		document.getElementById('cal-next').addEventListener('click', function () {
			state.month++;
			if (state.month > 11) { state.month = 0; state.year++; }
			render();
		});

		render();
	})();
</script>
