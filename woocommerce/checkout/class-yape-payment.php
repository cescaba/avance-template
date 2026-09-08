<?php
/**
 * Yape Payment Display
 *
 * Muestra información de pago Yape en order-received.php
 * Usa el mismo diseño que la tarjeta bancaria
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Yape_Payment {

	private $order;
	private $yape_settings;

	public function __construct($order) {
		$this->order = $order;
		$this->yape_settings = $this->get_yape_settings();
	}

	/**
	 * Obtener configuración de Yape desde el plugin
	 */
	private function get_yape_settings() {
		$settings = array();

		if (function_exists('WC')) {
			$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

			foreach ($payment_gateways as $gateway) {
				if (stripos($gateway->id, 'yape') !== false) {
					$settings = array(
						'numero' => $gateway->get_option('number', ''),
						'qr' => $gateway->get_option('paytm_qr_url', ''),
						'instrucciones' => $gateway->get_option('instructions', ''),
						'titulo' => $gateway->get_option('title', 'Yape'),
						'headline' => $gateway->get_option('headline', ''),
						'icon' => $gateway->get_option('icon', ''),
						'description' => $gateway->get_option('description', ''),
					);
					break;
				}
			}
		}

		return $settings;
	}

	/**
	 * Verificar si Yape está disponible
	 */
	public function is_yape_payment() {
		return $this->order->get_payment_method() &&
		       stripos($this->order->get_payment_method(), 'yape') !== false;
	}

	/**
	 * Renderizar tarjeta de Yape (mismo diseño que tarjeta bancaria)
	 */
	public function render_payment_card() {
		if (!$this->is_yape_payment() || empty($this->yape_settings)) {
			return '';
		}

		$numero = $this->yape_settings['numero'] ?? '';
		$qr = $this->yape_settings['qr'] ?? '';
		$instrucciones = $this->yape_settings['instrucciones'] ?? '';
		$titulo = $this->yape_settings['titulo'] ?? 'Yape';
		$headline = $this->yape_settings['headline'] ?? '';
		$icon = $this->yape_settings['icon'] ?? '';

		ob_start();
		?>

		<section class="order-section">
			<div class="order-section-head">
				<h2 class="order-section-title">Realiza tu pago a <?php echo esc_html($titulo); ?></h2>
				<span class="order-section-note">Validación en 1–2 días hábiles</span>
			</div>

			<div class="order-bankcard">
				<div class="order-bc-top">
					<div class="order-bc-issuer">
						<span class="order-bc-cap">Método de pago</span>
						<span class="name"><?php echo esc_html($titulo); ?></span>
					</div>
					<span class="order-bc-chip" aria-hidden="true"><i></i></span>
				</div>

				<!-- QR -->
				<?php if ($qr) : ?>
				<div class="order-yape-qr">
					<span class="order-bc-cap">Código QR</span>
					<img src="<?php echo esc_url($qr); ?>" alt="Código QR Yape" class="yape-qr-image" style="max-width: 200px; height: auto; display: block; margin: 15px auto;">
				</div>
				<?php endif; ?>

				<!-- Información de pago -->
				<dl class="order-bc-foot">
					<?php if ($headline) : ?>
					<div class="order-bc-cell">
						<dt class="order-bc-cap">Titular</dt>
						<dd class="v"><?php echo esc_html($headline); ?></dd>
					</div>
					<?php endif; ?>

					<?php if ($numero) : ?>
					<div class="order-bc-cell">
						<dt class="order-bc-cap">Número de celular</dt>
						<dd class="v"><?php echo esc_html($numero); ?></dd>
					</div>
					<?php endif; ?>

					<div class="order-bc-cell">
						<dt class="order-bc-cap">Importe</dt>
						<dd class="v"><?php echo wp_kses_post($this->order->get_formatted_order_total()); ?></dd>
					</div>
				</dl>

				<!-- Instrucciones -->
				<?php if ($instrucciones) : ?>
				<div class="order-yape-instructions" style="background: #f8f8f8; padding: 15px; border-radius: 8px; margin-top: 15px; color: #000000;">
					<span class="order-bc-cap" style="color: #000000;">Instrucciones:</span>
					<div class="yape-instructions-text" style="margin-top: 10px; font-size: 14px; color: #000000;">
						<?php echo wp_kses_post(wpautop($instrucciones)); ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</section>

		<?php
		return ob_get_clean();
	}

	/**
	 * Renderizar tarjeta Yape
	 */
	public function render() {
		return $this->render_payment_card();
	}
}
