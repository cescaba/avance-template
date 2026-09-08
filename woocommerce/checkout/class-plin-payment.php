<?php
/**
 * Plin Payment Display
 *
 * Muestra información de pago Plin en order-received.php
 * Usa el mismo diseño que la tarjeta bancaria
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Plin_Payment {

	private $order;
	private $plin_settings;

	public function __construct($order) {
		$this->order = $order;
		$this->plin_settings = $this->get_plin_settings();
	}

	/**
	 * Obtener configuración de Plin desde el plugin
	 */
	private function get_plin_settings() {
		$settings = array();

		if (function_exists('WC')) {
			$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

			foreach ($payment_gateways as $gateway) {
				if (stripos($gateway->id, 'plin') !== false) {
					$settings = array(
						'numero' => $gateway->get_option('number', ''),
						'qr' => $gateway->get_option('paytm_qr_url', ''),
						'instrucciones' => $gateway->get_option('instructions', ''),
						'titulo' => $gateway->get_option('title', 'Plin'),
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
	 * Verificar si Plin está disponible
	 */
	public function is_plin_payment() {
		return $this->order->get_payment_method() &&
		       stripos($this->order->get_payment_method(), 'plin') !== false;
	}

	/**
	 * Renderizar tarjeta de Plin (mismo diseño que tarjeta bancaria)
	 */
	public function render_payment_card() {
		if (!$this->is_plin_payment() || empty($this->plin_settings)) {
			return '';
		}

		$numero = $this->plin_settings['numero'] ?? '';
		$qr = $this->plin_settings['qr'] ?? '';
		$instrucciones = $this->plin_settings['instrucciones'] ?? '';
		$titulo = $this->plin_settings['titulo'] ?? 'Plin';
		$headline = $this->plin_settings['headline'] ?? '';
		$icon = $this->plin_settings['icon'] ?? '';

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

				<!-- QR -->
				<?php if ($qr) : ?>
				<div class="order-yape-qr" style="margin-top: 20px;">
					<span class="order-bc-cap">Código QR</span>
					<img src="<?php echo esc_url($qr); ?>" alt="Código QR Plin" class="yape-qr-image" style="max-width: 200px; height: auto; display: block; margin: 15px auto;">
				</div>
				<?php endif; ?>
			</div>
		</section>

		<?php
		return ob_get_clean();
	}

	/**
	 * Renderizar tarjeta Plin
	 */
	public function render() {
		return $this->render_payment_card();
	}
}
