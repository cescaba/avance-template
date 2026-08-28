<?php
/**
 * Template Part: Calendario de Agendamiento
 *
 * @package Avance_Template
 *
 * @param array $args {
 *   @type string $host_title     Título del host
 *   @type string $host_meta      Meta del host
 *   @type string $avatar_text    Texto del avatar
 *   @type string $tag_text       Texto del tag (Gratis, etc)
 *   @type string $cal_id         ID base para elementos del calendario
 *   @type string $timezone       Zona horaria (ej: Lima (GMT-5))
 * }
 */

if (!defined('ABSPATH')) {
	exit;
}

$defaults = [
	'host_title'    => 'Avance Empresarial',
	'host_meta'     => 'Sesión de diagnóstico · 30 min · Google Meet',
	'avatar_text'   => 'AE',
	'tag_text'      => 'Gratis',
	'cal_id'        => 'calendarAgenda',
	'timezone'      => 'Lima (GMT-5)',
];

$args = wp_parse_args($args, $defaults);

$prev_month_id = $args['cal_id'] . 'PrevMonth';
$next_month_id = $args['cal_id'] . 'NextMonth';
$month_label_id = $args['cal_id'] . 'MonthLabel';
$cal_grid_id = $args['cal_id'] . 'CalGrid';
$time_slots_id = $args['cal_id'] . 'TimeSlots';
?>

<div class="contacto-agenda__left">
	<div class="contacto-agenda__card">
		<!-- Host Info -->
		<div class="contacto-agenda__host-row">
			<div class="contacto-agenda__avatar"><?php echo esc_html($args['avatar_text']); ?></div>
			<div class="contacto-agenda__host-info">
				<div class="contacto-agenda__host-title"><?php echo esc_html($args['host_title']); ?></div>
				<div class="contacto-agenda__host-meta"><?php echo esc_html($args['host_meta']); ?></div>
			</div>
			<span class="contacto-agenda__tag"><?php echo esc_html($args['tag_text']); ?></span>
		</div>

		<!-- Calendar Navigation -->
		<div class="contacto-agenda__cal-header">
			<button class="contacto-agenda__cal-nav" id="<?php echo esc_attr($prev_month_id); ?>" aria-label="<?php esc_attr_e('Mes anterior', 'avance-template'); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
			</button>
			<div class="contacto-agenda__cal-month-label" id="<?php echo esc_attr($month_label_id); ?>"></div>
			<button class="contacto-agenda__cal-nav" id="<?php echo esc_attr($next_month_id); ?>" aria-label="<?php esc_attr_e('Mes siguiente', 'avance-template'); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>
		</div>

		<!-- Calendar Grid (Weekdays + Days) -->
		<div class="contacto-agenda__cal-grid" id="<?php echo esc_attr($cal_grid_id); ?>">
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('DOM', 'avance-template'); ?></div>
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('LUN', 'avance-template'); ?></div>
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('MAR', 'avance-template'); ?></div>
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('MIÉ', 'avance-template'); ?></div>
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('JUE', 'avance-template'); ?></div>
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('VIE', 'avance-template'); ?></div>
			<div class="contacto-agenda__cal-weekday"><?php esc_html_e('SAB', 'avance-template'); ?></div>
		</div>

		<!-- Time Slots -->
		<div id="<?php echo esc_attr($time_slots_id); ?>" class="contacto-agenda__time-slots"></div>

		<!-- Timezone -->
		<div class="contacto-agenda__tz-row"><?php echo esc_html($args['timezone']); ?></div>
	</div>
</div>
