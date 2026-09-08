<?php
/**
 * Checkout Form - Diseño Premium
 *
 * Template personalizado para checkout con diseño premium
 * Procesa datos, crea orden y redirige a form-pay.php
 *
 * @package WooCommerce\Templates
 * @var WC_Checkout $checkout
 */

if (!defined('ABSPATH')) {
	exit;
}

// PROCESAR FORMULARIO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['woocommerce_pay'])) {
	// Validar nonce
	if (!isset($_POST['woocommerce-pay-nonce']) || !wp_verify_nonce($_POST['woocommerce-pay-nonce'], 'woocommerce-pay')) {
		wp_die('Error de seguridad: nonce inválido');
	}

	// Obtener datos
	$billing_first_name = sanitize_text_field($_POST['billing_first_name'] ?? '');
	$billing_last_name = sanitize_text_field($_POST['billing_last_name'] ?? '');
	$billing_email = sanitize_email($_POST['billing_email'] ?? '');
	$billing_phone = sanitize_text_field($_POST['billing_phone'] ?? '');
	$billing_address_1 = sanitize_text_field($_POST['billing_address_1'] ?? '');
	$billing_city = sanitize_text_field($_POST['billing_city'] ?? '');
	$billing_postcode = sanitize_text_field($_POST['billing_postcode'] ?? '');
	$billing_country = sanitize_text_field($_POST['billing_country'] ?? '');

	// Validar datos
	if (empty($billing_first_name) || empty($billing_last_name) || empty($billing_email) || empty($billing_phone) || empty($billing_address_1) || empty($billing_city) || empty($billing_postcode) || empty($billing_country)) {
		wp_die('Por favor completa todos los campos requeridos');
	}

	// Validar carrito
	if (WC()->cart->is_empty()) {
		wp_die('El carrito está vacío');
	}

	// CREAR ORDEN
	$order = wc_create_order();

	if (is_wp_error($order)) {
		wp_die('Error al crear la orden: ' . $order->get_error_message());
	}

	// Añadir producto del carrito a la orden
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		$product = $cart_item['data'];
		$order->add_product($product, $cart_item['quantity']);
	}

	// Establecer datos de facturación
	$order->set_billing_first_name($billing_first_name);
	$order->set_billing_last_name($billing_last_name);
	$order->set_billing_email($billing_email);
	$order->set_billing_phone($billing_phone);
	$order->set_billing_address_1($billing_address_1);
	$order->set_billing_city($billing_city);
	$order->set_billing_postcode($billing_postcode);
	$order->set_billing_country($billing_country);

	// Calcular totales
	$order->calculate_totals();
	$order->save();

	// Limpiar carrito
	WC()->cart->empty_cart();

	// REDIRIGIR A FORM-PAY
	wp_redirect($order->get_checkout_payment_url());
	exit;
}

do_action('woocommerce_before_checkout_form', $checkout);
?>

<div class="checkout-wrap">

	<form method="post" class="checkout woocommerce-checkout checkout-grid" name="post_data" action="" enctype="multipart/form-data" id="payForm">

		<!-- Panel Principal: Formulario -->
		<div class="checkout-panel checkout-panel-main">

			<div class="checkout-head">
				<h1 class="checkout-title">Finalizar la compra</h1>
				<p class="checkout-subtitle">Completa tus datos y elige cómo quieres pagar. Tu pedido queda confirmado en cuanto validemos el pago.</p>
			</div>

			<hr class="checkout-rule">

			<!-- Facturación -->
			<div class="checkout-form-block">
				<h2 class="checkout-section-title">Datos de facturación</h2>
				<div class="checkout-fields">
					<div class="checkout-field">
						<label class="checkout-form-label" for="billing_first_name">Nombre <span class="checkout-req">*</span></label>
						<input type="text" class="checkout-form-input" id="billing_first_name" name="billing_first_name" value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>" placeholder="Nombre" autocomplete="given-name" required>
					</div>
					<div class="checkout-field">
						<label class="checkout-form-label" for="billing_last_name">Apellido <span class="checkout-req">*</span></label>
						<input type="text" class="checkout-form-input" id="billing_last_name" name="billing_last_name" value="<?php echo esc_attr($checkout->get_value('billing_last_name')); ?>" placeholder="Apellido" autocomplete="family-name" required>
					</div>
					<div class="checkout-field">
						<label class="checkout-form-label" for="billing_email">Email <span class="checkout-req">*</span></label>
						<input type="email" class="checkout-form-input" id="billing_email" name="billing_email" value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>" placeholder="tucorreo@ejemplo.com" autocomplete="email" required>
					</div>
					<div class="checkout-field">
						<label class="checkout-form-label" for="billing_phone">Teléfono <span class="checkout-req">*</span></label>
						<input type="tel" class="checkout-form-input" id="billing_phone" name="billing_phone" value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>" placeholder="+51 999 999 999" autocomplete="tel" required>
					</div>
				</div>
			</div>

			<hr class="checkout-rule">

			<!-- Envío -->
			<div class="checkout-form-block">
				<h2 class="checkout-section-title">Dirección de envío</h2>
				<div class="checkout-fields">
					<div class="checkout-field checkout-field-wide">
						<label class="checkout-form-label" for="billing_address_1">Dirección <span class="checkout-req">*</span></label>
						<input type="text" class="checkout-form-input" id="billing_address_1" name="billing_address_1" value="<?php echo esc_attr($checkout->get_value('billing_address_1')); ?>" placeholder="Calle y número" autocomplete="street-address" required>
					</div>
					<div class="checkout-field">
						<label class="checkout-form-label" for="billing_city">Ciudad <span class="checkout-req">*</span></label>
						<input type="text" class="checkout-form-input" id="billing_city" name="billing_city" value="<?php echo esc_attr($checkout->get_value('billing_city')); ?>" placeholder="Lima" autocomplete="address-level2" required>
					</div>
					<div class="checkout-field">
						<label class="checkout-form-label" for="billing_postcode">Código postal <span class="checkout-req">*</span></label>
						<input type="text" class="checkout-form-input" id="billing_postcode" name="billing_postcode" value="<?php echo esc_attr($checkout->get_value('billing_postcode')); ?>" placeholder="15074" autocomplete="postal-code" required>
					</div>
					<div class="checkout-field checkout-field-wide">
						<label class="checkout-form-label" for="billing_country">País <span class="checkout-req">*</span></label>
						<select class="checkout-form-input" id="billing_country" name="billing_country" autocomplete="country-name" required>
							<option value="">Selecciona un país</option>
							<option value="PE" <?php selected($checkout->get_value('billing_country'), 'PE'); ?>>Perú</option>
							<option value="CL" <?php selected($checkout->get_value('billing_country'), 'CL'); ?>>Chile</option>
							<option value="CO" <?php selected($checkout->get_value('billing_country'), 'CO'); ?>>Colombia</option>
							<option value="MX" <?php selected($checkout->get_value('billing_country'), 'MX'); ?>>México</option>
							<option value="AR" <?php selected($checkout->get_value('billing_country'), 'AR'); ?>>Argentina</option>
							<option value="ES" <?php selected($checkout->get_value('billing_country'), 'ES'); ?>>España</option>
							<option value="US" <?php selected($checkout->get_value('billing_country'), 'US'); ?>>Estados Unidos</option>
						</select>
					</div>
				</div>
			</div>

			<p class="checkout-privacy">Tus datos personales se utilizarán para procesar tu pedido, mejorar tu experiencia en esta web y otros propósitos descritos en nuestra <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">política de privacidad</a>.</p>

		</div>

		<!-- Panel Lateral: Resumen -->
		<aside class="checkout-panel checkout-panel-side">
			<h2 class="checkout-section-title">Resumen del pedido</h2>

			<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
				<?php
					$product = $cart_item['data'];
				?>
				<div class="checkout-line-item">
					<div class="checkout-line-thumb">
						<?php if ($product->get_image()) : ?>
							<?php echo wp_kses_post($product->get_image(array(66, 66))); ?>
						<?php else : ?>
							<svg width="26" height="26" viewBox="0 0 256 256" fill="#7C9086" aria-hidden="true"><path d="M232 104a8 8 0 0 0 8-8V64a16 16 0 0 0-16-16H32a16 16 0 0 0-16 16v32a8 8 0 0 0 8 8 24 24 0 0 1 0 48 8 8 0 0 0-8 8v32a16 16 0 0 0 16 16h192a16 16 0 0 0 16-16v-32a8 8 0 0 0-8-8 24 24 0 0 1 0-48Zm-8 15.3V152a40 40 0 0 0 0 76.7V192H32v-39.3a40 40 0 0 0 0-76.7V64h192Z"/></svg>
						<?php endif; ?>
					</div>
					<div class="checkout-line-info">
						<p class="checkout-line-name"><?php echo esc_html($product->get_name()); ?></p>
						<p class="checkout-line-meta">Cantidad: × <?php echo esc_html($cart_item['quantity']); ?></p>
					</div>
					<div class="checkout-line-price"><?php echo wp_kses_post(WC()->cart->get_product_subtotal($product, $cart_item['quantity'])); ?></div>
				</div>
			<?php endforeach; ?>

			<div class="checkout-totals">
				<div class="checkout-totals-row"><span>Subtotal</span><strong><?php echo wp_kses_post(wc_price(WC()->cart->get_subtotal())); ?></strong></div>
			</div>

			<div class="checkout-total">
				<span class="checkout-total-label">Total</span>
				<span class="checkout-total-value"><?php echo wp_kses_post(WC()->cart->get_total()); ?></span>
			</div>

			<div class="checkout-actions">
				<?php wp_nonce_field('woocommerce-pay', 'woocommerce-pay-nonce'); ?>
				<input type="hidden" name="woocommerce_pay" value="1" />
				<button class="checkout-btn-pay" type="submit" id="place_order">
					Pagar por el pedido · <?php echo wp_kses_post(WC()->cart->get_total()); ?>
				</button>

				<p class="checkout-secure">
					<svg width="14" height="14" viewBox="0 0 256 256" fill="#466060" aria-hidden="true"><path d="M208 80h-32V56a48 48 0 0 0-96 0v24H48a16 16 0 0 0-16 16v112a16 16 0 0 0 16 16h160a16 16 0 0 0 16-16V96a16 16 0 0 0-16-16ZM96 56a32 32 0 0 1 64 0v24H96Zm112 152H48V96h160v112Z"/></svg>
					<span>Pago seguro con cifrado SSL</span>
				</p>
			</div>

			<ul class="checkout-trust">
				<li><svg width="15" height="15" viewBox="0 0 256 256" fill="#466060" aria-hidden="true"><path d="m229.66 77.66-128 128a8 8 0 0 1-11.32 0l-56-56a8 8 0 0 1 11.32-11.32L96 188.69 218.34 66.34a8 8 0 0 1 11.32 11.32Z"/></svg><span>Confirmación y acceso por correo</span></li>
				<li><svg width="15" height="15" viewBox="0 0 256 256" fill="#466060" aria-hidden="true"><path d="m229.66 77.66-128 128a8 8 0 0 1-11.32 0l-56-56a8 8 0 0 1 11.32-11.32L96 188.69 218.34 66.34a8 8 0 0 1 11.32 11.32Z"/></svg><span>Boleta o factura electrónica</span></li>
				<li><svg width="15" height="15" viewBox="0 0 256 256" fill="#466060" aria-hidden="true"><path d="m229.66 77.66-128 128a8 8 0 0 1-11.32 0l-56-56a8 8 0 0 1 11.32-11.32L96 188.69 218.34 66.34a8 8 0 0 1 11.32 11.32Z"/></svg><span>Soporte en soporte@aulanova.com</span></li>
			</ul>
		</aside>

	</form>

</div>

<?php
do_action('woocommerce_after_checkout_form', $checkout);
