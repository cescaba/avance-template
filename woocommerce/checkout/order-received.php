<?php
/**
 * Order received page
 *
 * @package WooCommerce\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Obtener order_id - WooCommerce lo pasa como variable de contexto
// Si no está disponible, buscar en URL (igual que form-pay.php)
if ( ! isset( $order_id ) ) {
	$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
}

// Intentar obtenerlo de ?order=
if ( ! $order_id ) {
	$order_id = isset( $_GET['order'] ) ? absint( $_GET['order'] ) : 0;
}

// Si aún no tenemos order_id, intentar obtenerlo de la URL reescrita
if ( ! $order_id ) {
	global $wp;
	if ( isset( $wp->query_vars['order-received'] ) ) {
		$order_id = absint( $wp->query_vars['order-received'] );
	}
}

$order = $order_id ? wc_get_order( $order_id ) : false;

if ( ! $order ) {
	echo '<p>Error: No se encontró la orden. ID: ' . esc_html( $order_id ) . '</p>';
	return;
}

$order_items = $order->get_items();

// Obtener información bancaria - usar ACF si está disponible, sino valores por defecto
$bank_info = array();
if ( function_exists( 'get_field' ) ) {
	$bank_info = get_field( 'informacion_bancaria', 'option' ) ?: array();
}

?>

<div class="order-received-page">

  <!-- Encabezado -->
  <header class="order-masthead">
    <span class="order-eyebrow">Pedido recibido</span>
    <h1 class="order-headline">Gracias, <?php echo esc_html( $order->get_billing_first_name() ); ?>.<br>Tu pedido está reservado.</h1>
    <p class="order-lede">Enviamos una copia de este resumen a <strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>. Completa la transferencia y activamos tu acceso en cuanto la confirmemos.</p>
  </header>

  <!-- Recibo -->
  <section class="order-receipt">
    <div class="order-receipt-head">
      <div class="order-amount">
        <span class="cap">Importe a transferir</span>
        <span class="order-amount val"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
        <span class="order-amount note">Pago único · sin cargos adicionales</span>
      </div>
      <div class="order-stamp">
        <span class="cap">Pedido</span>
        <span class="order-stamp val">#<?php echo esc_html( $order->get_order_number() ); ?></span>
      </div>
    </div>

    <div class="order-perf"></div>

    <div class="order-receipt-body">

      <!-- Items del pedido -->
      <?php foreach ( $order_items as $item ) : ?>
      <div class="order-product-line">
        <div class="order-product">
          <span class="order-product thumb">
            <svg width="20" height="20" viewBox="0 0 256 256" fill="#7C9086" aria-hidden="true"><path d="M128 24a104 104 0 1 0 104 104A104.11 104.11 0 0 0 128 24Zm0 192a88 88 0 1 1 88-88 88.1 88.1 0 0 1-88 88Zm36.44-94.66-48-32A8 8 0 0 0 104 96v64a8 8 0 0 0 12.44 6.66l48-32a8 8 0 0 0 0-13.32ZM120 145.05v-34.1L145.58 128Z"/></svg>
          </span>
          <span class="order-product meta">
            <span class="order-product name"><?php echo esc_html( $item->get_name() ); ?></span>
            <span class="order-product sub">Cantidad × <?php echo esc_html( $item->get_quantity() ); ?> · acceso digital</span>
          </span>
        </div>
        <span class="order-product-price"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
      </div>
      <?php endforeach; ?>

      <!-- Detalles de la orden -->
      <dl class="order-rows">
        <div class="order-row"><dt>Fecha del pedido</dt><dd><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></dd></div>
        <div class="order-row"><dt>Método de pago</dt><dd><?php echo esc_html( $order->get_payment_method_title() ); ?></dd></div>
        <div class="order-row"><dt>Subtotal</dt><dd><?php echo wp_kses_post( wc_price( $order->get_subtotal() ) ); ?></dd></div>
      </dl>

      <!-- Total -->
      <dl class="order-total-line">
        <dt>Total</dt>
        <dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd>
      </dl>
    </div>
  </section>

  <!-- Datos bancarios -->
  <section class="order-section">
    <div class="order-section-head">
      <h2 class="order-section-title">Realiza tu pago a</h2>
      <span class="order-section-note">Validación en 1–2 días hábiles</span>
    </div>

    <div class="order-bankcard">
      <div class="order-bc-top">
        <div class="order-bc-issuer">
          <span class="order-bc-cap">Banco</span>
          <span class="name"><?php echo esc_html( $bank_info['banco'] ?? '[Tu Banco]' ); ?></span>
        </div>
        <span class="order-bc-chip" aria-hidden="true"><i></i></span>
      </div>

      <p class="order-bc-number">
        <span class="order-bc-cap">N.º de cuenta</span>
        <span><?php echo esc_html( $bank_info['numero_cuenta'] ?? '[Tu Número]' ); ?></span>
      </p>

      <dl class="order-bc-foot">
        <div class="order-bc-cell"><dt class="order-bc-cap">Titular</dt><dd class="v"><?php echo esc_html( $bank_info['titular'] ?? '[Tu Nombre]' ); ?></dd></div>
        <div class="order-bc-cell"><dt class="order-bc-cap">Código SWIFT</dt><dd class="v"><?php echo esc_html( $bank_info['swift'] ?? '[SWIFT Code]' ); ?></dd></div>
        <div class="order-bc-cell"><dt class="order-bc-cap">Importe</dt><dd class="v"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
      </dl>
    </div>

    <p class="order-reference">
      <span class="txt">Usa este código como referencia de la transferencia para que podamos identificar tu pago.</span>
      <span class="code">PEDIDO&nbsp;#<?php echo esc_html( $order->get_order_number() ); ?></span>
    </p>
  </section>

  <!-- Dirección de facturación -->
  <section class="order-section">
    <h2 class="order-section-title">Dirección de facturación</h2>
    <dl class="order-billing">
      <div><dt>Nombre completo</dt><dd><?php echo esc_html( $order->get_billing_first_name() ); ?></dd></div>
      <div><dt>Apellido</dt><dd><?php echo esc_html( $order->get_billing_last_name() ); ?></dd></div>
      <div><dt>Correo electrónico</dt><dd><?php echo esc_html( $order->get_billing_email() ); ?></dd></div>
      <div><dt>Número de teléfono</dt><dd><?php echo esc_html( $order->get_billing_phone() ); ?></dd></div>
    </dl>
  </section>

  <!-- Acciones -->
  <div class="order-actions">
    <a class="order-btn order-btn-solid" href="https://wa.me/TU_NUMERO?text=Hola%20tengo%20una%20duda%20con%20mi%20pedido" target="_blank" rel="noopener noreferrer">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11.61 9.58803C11.412 9.48869 10.438 9.01003 10.2567 8.94336C10.0747 8.87736 9.94267 8.84469 9.81 9.04336C9.67867 9.24136 9.29867 9.68736 9.18333 9.81936C9.068 9.95203 8.952 9.96803 8.754 9.86936C8.556 9.76936 7.91733 9.56069 7.16067 8.88603C6.572 8.36069 6.174 7.71203 6.05867 7.51336C5.94333 7.31536 6.04667 7.20803 6.14533 7.10936C6.23467 7.02069 6.344 6.87803 6.44267 6.76269C6.542 6.64669 6.57467 6.56403 6.64133 6.43136C6.70733 6.29936 6.67467 6.18403 6.62467 6.08469C6.57467 5.98536 6.17867 5.01003 6.014 4.61336C5.85267 4.22736 5.68933 4.28003 5.568 4.27336C5.45267 4.26803 5.32067 4.26669 5.188 4.26669C5.056 4.26669 4.84133 4.31603 4.66 4.51469C4.47867 4.71269 3.96667 5.19203 3.96667 6.16736C3.96667 7.14203 4.67667 8.08403 4.77533 8.21669C4.87467 8.34869 6.17267 10.35 8.16 11.208C8.63267 11.412 9.00133 11.534 9.28933 11.6247C9.764 11.776 10.196 11.7547 10.5367 11.7034C10.9173 11.6467 11.7087 11.224 11.874 10.7614C12.0393 10.2987 12.0393 9.90203 11.9893 9.81936C11.94 9.73669 11.8087 9.68736 11.61 9.58803ZM7.99533 14.5234H7.99267C6.81251 14.5235 5.65405 14.2062 4.63867 13.6047L4.398 13.462L1.904 14.1167L2.56933 11.6847L2.41267 11.4354C1.75296 10.3849 1.40396 9.16915 1.406 7.92869C1.40667 4.29536 4.36333 1.33936 7.998 1.33936C9.758 1.33936 11.4127 2.02603 12.6567 3.27136C13.2705 3.88244 13.7569 4.60918 14.088 5.40952C14.4191 6.20987 14.5881 7.06792 14.5853 7.93403C14.5833 11.5674 11.6273 14.5234 7.99533 14.5234ZM13.604 2.32536C12.8695 1.58598 11.9955 0.999719 11.0327 0.600561C10.07 0.201403 9.03756 -0.0027175 7.99533 2.73184e-05C3.62533 2.73184e-05 0.0686667 3.55669 0.0666667 7.92803C0.0666667 9.32536 0.431333 10.6894 1.12533 11.8914L0 16L4.20333 14.8974C5.36573 15.5306 6.66829 15.8625 7.992 15.8627H7.99533C12.3647 15.8627 15.922 12.306 15.924 7.93403C15.9273 6.89226 15.724 5.86017 15.3259 4.89746C14.9278 3.93475 14.3428 3.06053 13.6047 2.32536" fill="#EEF3EC"/></svg>
      Contactar por WhatsApp
    </a>
    <a class="order-btn order-btn-line" href="<?php echo esc_url( home_url() ); ?>">Volver al inicio</a>
  </div>

  <!-- Pie -->
  <p class="order-footnote">
    <svg width="15" height="15" viewBox="0 0 256 256" fill="#7C9086" aria-hidden="true"><path d="M208 80h-32V56a48 48 0 0 0-96 0v24H48a16 16 0 0 0-16 16v112a16 16 0 0 0 16 16h160a16 16 0 0 0 16-16V96a16 16 0 0 0-16-16ZM96 56a32 32 0 0 1 64 0v24H96Zm112 152H48V96h160v112Z"/></svg>
    <span>Datos protegidos. ¿Alguna duda con el pedido #<?php echo esc_html( $order->get_order_number() ); ?>? Escríbenos a <a href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>">soporte@aulanova.com</a></span>
  </p>
</div>
