<?php
/**
 * Pay for order form - Checkout
 *
 * @package WooCommerce\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Obtener el order ID - WooCommerce lo pasa como variable de contexto
// Si no está disponible, buscar en URL
if ( ! isset( $order_id ) ) {
	$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
}

// Si aún no tenemos order_id, intentar obtenerlo de la URL de pago
if ( ! $order_id && isset( $_GET['pay_for_order'] ) ) {
	// Extraer order_id de la URL
	global $wp;
	if ( isset( $wp->query_vars['order-pay'] ) ) {
		$order_id = absint( $wp->query_vars['order-pay'] );
	}
}

$order = $order_id ? wc_get_order( $order_id ) : false;

if ( ! $order ) {
	echo '<p>Error: No se encontró la orden. ID: ' . esc_html( $order_id ) . '</p>';
	return;
}

?>

<div class="checkout-wrap">

  <form method="post" class="checkout woocommerce-checkout checkout-grid" name="post_data" action="" enctype="multipart/form-data" id="payForm">

    <!-- Pago -->
    <div class="checkout-panel checkout-panel-main">
      <div class="checkout-head">
        <h1 class="checkout-title">Pagar el pedido</h1>
        <p class="checkout-subtitle">Elige cómo quieres pagar. Tu pedido queda confirmado en cuanto validemos el pago.</p>
      </div>

      <hr class="checkout-rule">

      <!-- Métodos de pago -->
      <div class="checkout-pay-block">
        <h2 class="checkout-section-title">Método de pago</h2>
        <div class="checkout-methods">
          <?php
          if ( WC()->payment_gateways()->get_available_payment_gateways() ) :
            foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $gateway ) :
              ?>
              <label class="checkout-method">
                <span class="checkout-method-top">
                  <input type="radio" name="payment_method" id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="input-radio" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
                  <span class="checkout-method-name"><?php echo esc_html( $gateway->get_title() ); ?></span>
                  <span class="checkout-method-badge"><?php echo esc_html( apply_filters( 'woocommerce_payment_gateway_label', $gateway->get_title(), $gateway ) ); ?></span>
                </span>
                <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                <span class="checkout-method-panel">
                  <?php $gateway->payment_fields(); ?>
                  <span class="checkout-method-desc"><?php echo wp_kses_post( $gateway->get_description() ); ?></span>
                </span>
                <?php endif; ?>
              </label>
              <?php
            endforeach;
          endif;
          ?>
        </div>
      </div>

      <p class="checkout-privacy">Tus datos personales se utilizarán para procesar tu pedido, mejorar tu experiencia en esta web y otros propósitos descritos en nuestra <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">política de privacidad</a>.</p>

    </div>

    <!-- Resumen -->
    <aside class="checkout-panel checkout-panel-side">
      <h2 class="checkout-section-title">Resumen del pedido</h2>

      <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
        <?php
          $product_id = intval( $item['product_id'] );
          $product = $product_id ? wc_get_product( $product_id ) : false;
        ?>
      <div class="checkout-line-item">
        <div class="checkout-line-thumb">
          <?php if ( $product && $product->get_image() ) : ?>
            <?php echo wp_kses_post( $product->get_image( array( 66, 66 ) ) ); ?>
          <?php else : ?>
            <svg width="26" height="26" viewBox="0 0 256 256" fill="#7C9086" aria-hidden="true"><path d="M232 104a8 8 0 0 0 8-8V64a16 16 0 0 0-16-16H32a16 16 0 0 0-16 16v32a8 8 0 0 0 8 8 24 24 0 0 1 0 48 8 8 0 0 0-8 8v32a16 16 0 0 0 16 16h192a16 16 0 0 0 16-16v-32a8 8 0 0 0-8-8 24 24 0 0 1 0-48Zm-8 15.3V152a40 40 0 0 0 0 76.7V192H32v-39.3a40 40 0 0 0 0-76.7V64h192Z"/></svg>
          <?php endif; ?>
        </div>
        <div class="checkout-line-info">
          <p class="checkout-line-name"><?php echo esc_html( $item->get_name() ); ?></p>
          <p class="checkout-line-meta">Cantidad: × <?php echo esc_html( $item->get_quantity() ); ?></p>
        </div>
        <div class="checkout-line-price"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></div>
      </div>
      <?php endforeach; ?>

      <div class="checkout-totals">
        <div class="checkout-totals-row"><span>Subtotal</span><strong><?php echo wp_kses_post( wc_price( $order->get_subtotal() ) ); ?></strong></div>
      </div>

      <div class="checkout-total">
        <span class="checkout-total-label">Total</span>
        <span class="checkout-total-value"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
      </div>

      <div class="checkout-actions">
        <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
        <input type="hidden" name="woocommerce_pay" value="1" />
        <button type="submit" class="checkout-btn-pay" id="place_order">
          Pagar por el pedido · <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
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
