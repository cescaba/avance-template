<?php
/**
 * Customer Processing Order Email Template
 *
 * Template personalizado para email de pedidos en espera de pago
 * Dinámico para diferentes categorías (Libro, Reserva, etc.)
 * Soporta múltiples métodos de pago (Plin, Yape, Transferencia)
 *
 * El template se envía automáticamente cuando un pedido entra en estado "processing"
 * Mantiene el mismo diseño y estructura del checkout personalizado
 *
 * Para personalizarlo por categoría de producto:
 * 1. Usa $order->get_items() para iterar productos
 * 2. Obtén la categoría con: $product = $item->get_product(); $categories = $product->get_category_ids();
 * 3. Personaliza el mensaje según la categoría (Libro, Reserva, etc.)
 *
 * @package WooCommerce\Templates
 * @var WC_Order $order
 * @var bool $sent_to_admin
 * @var bool $plain_text
 */

if (!defined('ABSPATH')) {
	exit;
}

// Evitar procesamiento si es plain text
if ($plain_text) {
	return;
}

// Cargar clases de métodos de pago
require_once get_template_directory() . '/woocommerce/checkout/class-yape-payment.php';
require_once get_template_directory() . '/woocommerce/checkout/class-plin-payment.php';

$yape_payment = new Avance_Yape_Payment($order);
$plin_payment = new Avance_Plin_Payment($order);
$order_items = $order->get_items();
$billing_first_name = $order->get_billing_first_name();
$order_number = $order->get_order_number();
$order_total = $order->get_formatted_order_total();
$order_date = wc_format_datetime($order->get_date_created());
$shipping_total = $order->get_shipping_total();
$subtotal = $order->get_subtotal();
$payment_method = $order->get_payment_method_title();
$billing_phone = $order->get_billing_phone();
$billing_email = $order->get_billing_email();

// Detectar categorías de productos para mostrar/ocultar envío
$has_libro = false;
$has_reserva = false;
foreach ($order_items as $item) {
  $product_id = $item->get_data()['product_id'] ?? 0;
  if ($product_id) {
    $product = wc_get_product($product_id);
    if ($product) {
      $categories = $product->get_category_ids();
      foreach ($categories as $cat_id) {
        $category = get_term($cat_id, 'product_cat');
        if ($category && !is_wp_error($category)) {
          if (strtolower($category->name) === 'libro' || strtolower($category->slug) === 'libro') {
            $has_libro = true;
          }
          if (strtolower($category->name) === 'reserva' || strtolower($category->slug) === 'reserva') {
            $has_reserva = true;
          }
        }
      }
    }
  }
}

// Mostrar envío solo si hay productos Libro (no para Reserva)
$show_shipping = $has_libro && !$has_reserva && $shipping_total > 0;

// Filtro para ocultar imagen del producto en email
add_filter('woocommerce_product_thumbnail_size', function() {
  return array(0, 0);
});

// Preheader personalizado
$preheader = sprintf(
	'Pedido #%s recibido — en espera de pago. Paga %s y envíanos la confirmación.',
	$order_number,
	$order_total
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<title>Tu pedido #<?php echo esc_attr($order_number); ?> — avanceEmpresarial</title>
<!--[if mso]>
<style>body,table,td,a{font-family:'Helvetica Neue',Arial,Helvetica,sans-serif !important}</style>
<![endif]-->
<style>
  @media only screen and (max-width:600px){
    .sp-h{width:20px !important}
    .stack{display:block !important;width:100% !important}
    .stack-pad{padding-top:16px !important}
    .h1{font-size:24px !important}
    .amount{font-size:26px !important}
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:#E7EDE4;">

<!-- Preheader -->
<span style="display:none;font-size:1px;color:#E7EDE4;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;"><?php echo esc_html($preheader); ?></span>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#E7EDE4;">
  <tr>
    <td align="center" style="padding:28px 12px 40px 12px;">

      <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px;max-width:600px;background-color:#F0F4EF;border:1px solid #DCE5D8;border-radius:14px;">

        <!-- Cabecera -->
        <tr>
          <td style="padding:22px 32px;background-color:#2E4141;border-radius:14px 14px 0 0;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
              <tr>
                <td align="left" style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:17px;font-weight:bold;letter-spacing:0.2px;color:#F0F4EF;mso-line-height-rule:exactly;line-height:22px;">
                  avanceEmpresarial
                </td>
                <td align="right" style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:12px;color:#B9CBBD;mso-line-height-rule:exactly;line-height:22px;">
                  Pedido&nbsp;#<?php echo esc_html($order_number); ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Saludo -->
        <tr>
          <td style="padding:32px 32px 0 32px;">
            <p class="h1" style="margin:0 0 12px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:28px;line-height:34px;mso-line-height-rule:exactly;color:#22322F;">Gracias por tu pedido</p>
            <p style="margin:0 0 12px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;mso-line-height-rule:exactly;color:#4A5F55;">Hola <strong style="color:#22322F;"><?php echo esc_html($billing_first_name); ?></strong>,</p>
            <p style="margin:0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;mso-line-height-rule:exactly;color:#4A5F55;">Hemos recibido tu pedido y está <strong style="color:#22322F;">en espera</strong> hasta que confirmemos el pago. Aquí tienes el detalle y las instrucciones para completarlo.</p>
          </td>
        </tr>

        <!-- Bloque de pago (Plin, Yape o Transferencia Bancaria) -->
        <?php
        $payment_method_code = $order->get_payment_method();
        $is_plin = $plin_payment->is_plin_payment();
        $is_yape = $yape_payment->is_yape_payment();
        $is_bank_transfer = in_array($payment_method_code, array('bacs', 'woocommerce_transfer', 'transfer', 'bank_transfer'));

        // Obtener datos bancarios si es transferencia
        $bank_info = array();
        if ($is_bank_transfer) {
          $transfer_methods = array('bacs', 'woocommerce_transfer', 'transfer', 'bank_transfer');
          foreach ($transfer_methods as $method) {
            if ($payment_method_code === $method) {
              $option_key = 'woocommerce_' . $method . '_accounts';
              $accounts = get_option($option_key);
              if (is_array($accounts) && !empty($accounts)) {
                $bank_info = $accounts[0];
                break;
              }
            }
          }
        }
        ?>
        <?php if ($is_plin || $is_yape || $is_bank_transfer) : ?>
        <tr>
          <td style="padding:26px 32px 0 32px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;background-color:#FFFFFF;border:1px solid #D3DECF;border-radius:12px;">
              <tr>
                <td style="padding:18px 22px;background-color:#466060;border-radius:12px 12px 0 0;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                      <td align="left" style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:1.4px;text-transform:uppercase;color:#C4D5C8;mso-line-height-rule:exactly;line-height:16px;">
                        Método de pago
                        <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:17px;font-weight:bold;letter-spacing:0;text-transform:none;color:#FFFFFF;line-height:24px;mso-line-height-rule:exactly;padding-top:3px;"><?php echo esc_html($payment_method); ?></div>
                      </td>
                      <td align="right" class="amount" style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:30px;color:#FFFFFF;mso-line-height-rule:exactly;line-height:34px;white-space:nowrap;">
                        <?php echo wp_kses_post($order_total); ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Contenido según método de pago -->
              <?php if ($is_plin || $is_yape) : ?>
              <!-- PLIN O YAPE: QR + Datos -->
              <tr>
                <td style="padding:22px;">
                  <p style="margin:0 0 16px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;line-height:21px;mso-line-height-rule:exactly;color:#4A5F55;">
                    <?php
                    $payment_label = $is_plin ? 'Plin' : 'Yape';
                    echo sprintf('Escanea el código QR o guarda nuestro número en tus contactos y realiza el pago con %s.', esc_html($payment_label));
                    ?>
                  </p>
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                      <!-- QR -->
                      <td class="stack" width="150" valign="top" style="width:150px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="150" style="width:150px;">
                          <tr>
                            <td align="center" valign="middle" height="150" style="width:150px;height:150px;background-color:#F4F8F2;border:1px dashed #B7C7B4;border-radius:10px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11px;line-height:16px;mso-line-height-rule:exactly;color:#5C7268;">
                              <?php
                              $qr_code = $is_plin ? $plin_payment->get_plin_qr() : ($is_yape ? $yape_payment->get_yape_qr() : '');
                              if ($qr_code) :
                              ?>
                                <img src="<?php echo esc_url($qr_code); ?>" width="150" height="150" alt="Código QR" style="display:block;border:0;border-radius:10px;">
                              <?php else : ?>
                                Código QR<br>(insertar imagen)
                              <?php endif; ?>
                            </td>
                          </tr>
                        </table>
                      </td>

                      <td class="sp-h" width="20" style="width:20px;font-size:0;line-height:0;">&nbsp;</td>

                      <!-- Datos -->
                      <td class="stack stack-pad" valign="top" height="150" style="height:150px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" height="150" style="width:100%;height:150px;border:1px solid #E1E9DE;border-radius:10px;">
                          <tr>
                            <td height="74" valign="middle" style="height:74px;padding:0 16px;border-bottom:1px solid #E1E9DE;">
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Titular</div>
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;color:#22322F;line-height:24px;mso-line-height-rule:exactly;">
                                <?php
                                $headline = $is_plin ? $plin_payment->get_plin_headline() : ($is_yape ? $yape_payment->get_yape_headline() : '');
                                echo esc_html($headline);
                                ?>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td height="74" valign="middle" style="height:74px;padding:0 16px;">
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Número de celular</div>
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;color:#22322F;line-height:24px;mso-line-height-rule:exactly;">
                                <?php
                                $numero = $is_plin ? $plin_payment->get_plin_number() : ($is_yape ? $yape_payment->get_yape_number() : '');
                                echo esc_html($numero);
                                ?>
                              </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Instrucciones -->
              <?php
              $instrucciones = $is_plin ? $plin_payment->get_plin_instructions() : ($is_yape ? $yape_payment->get_yape_instructions() : '');
              if ($instrucciones) :
              ?>
              <tr>
                <td style="padding:0 22px 22px 22px;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#F4F8F2;border:1px solid #E1E9DE;border-radius:10px;">
                    <tr>
                      <td style="padding:16px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;">
                        <p style="margin:0 0 10px 0;font-size:11px;letter-spacing:1.3px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Instrucciones</p>

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                          <?php
                          $instructions = wpautop($instrucciones);
                          $lines = array_filter(array_map('trim', explode('<br />', strip_tags($instructions, '<strong>'))));
                          $step = 1;
                          foreach ($lines as $line) :
                          ?>
                          <tr>
                            <td width="24" valign="top" style="width:24px;padding-bottom:9px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;color:#466060;line-height:21px;mso-line-height-rule:exactly;"><?php echo esc_html($step++); ?>.</td>
                            <td valign="top" style="padding-bottom:9px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#4A5F55;line-height:21px;mso-line-height-rule:exactly;"><?php echo wp_kses_post($line); ?></td>
                          </tr>
                          <?php endforeach; ?>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Botón WhatsApp -->
              <tr>
                <td style="padding:0 22px 22px 22px;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                      <td align="center" bgcolor="#2E4141" style="border-radius:10px;">
                        <?php
                        $numero_limpio = preg_replace('/\D/', '', $is_plin ? $plin_payment->get_plin_number() : ($is_yape ? $yape_payment->get_yape_number() : ''));
                        ?>
                        <a href="https://wa.me/<?php echo esc_attr($numero_limpio); ?>?text=Hola,%20env%C3%ADo%20el%20comprobante%20del%20pedido%20%23<?php echo esc_attr($order_number); ?>" style="display:block;padding:15px 20px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:#F0F4EF;text-decoration:none;border-radius:10px;mso-line-height-rule:exactly;line-height:20px;">Enviar captura por WhatsApp</a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <?php endif; ?>
              <?php endif; ?>

              <!-- TRANSFERENCIA BANCARIA: Tarjeta Bancaria -->
              <?php if ($is_bank_transfer && !empty($bank_info)) : ?>
              <tr>
                <td style="padding:22px;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                      <td style="padding:0 0 16px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;line-height:21px;mso-line-height-rule:exactly;color:#4A5F55;">
                        Realiza la transferencia a la siguiente cuenta:
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <!-- Tarjeta Bancaria -->
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #E1E9DE;border-radius:10px;overflow:hidden;">
                          <!-- Encabezado Tarjeta -->
                          <tr>
                            <td style="padding:18px 22px;background-color:#466060;">
                              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                  <td align="left" style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:1.4px;text-transform:uppercase;color:#C4D5C8;mso-line-height-rule:exactly;line-height:16px;">
                                    Banco
                                    <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:17px;font-weight:bold;letter-spacing:0;text-transform:none;color:#FFFFFF;line-height:24px;mso-line-height-rule:exactly;padding-top:3px;">
                                      <?php echo esc_html($bank_info['bank_name'] ?? $bank_info['banco'] ?? 'Banco'); ?>
                                    </div>
                                  </td>
                                  <td align="right" style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:24px;font-weight:bold;color:#FFFFFF;line-height:24px;mso-line-height-rule:exactly;">
                                    <span style="display:inline-block;width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:50%;line-height:40px;text-align:center;">₿</span>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>

                          <!-- Número de Cuenta -->
                          <tr>
                            <td style="padding:18px 22px;border-bottom:1px solid #E1E9DE;">
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Número de cuenta</div>
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;color:#22322F;line-height:24px;mso-line-height-rule:exactly;word-break:break-all;">
                                <?php echo esc_html($bank_info['account_number'] ?? $bank_info['numero_cuenta'] ?? ''); ?>
                              </div>
                            </td>
                          </tr>

                          <!-- Datos adicionales -->
                          <tr>
                            <td style="padding:14px 22px;border-bottom:1px solid #E1E9DE;">
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Titular</div>
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#22322F;line-height:22px;mso-line-height-rule:exactly;">
                                <?php echo esc_html($bank_info['account_holder'] ?? $bank_info['account_name'] ?? $bank_info['titular'] ?? ''); ?>
                              </div>
                            </td>
                          </tr>

                          <?php if (!empty($bank_info['sort_code'] ?? $bank_info['swift'] ?? null)) : ?>
                          <tr>
                            <td style="padding:14px 22px;border-bottom:1px solid #E1E9DE;">
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Código SWIFT</div>
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#22322F;line-height:22px;mso-line-height-rule:exactly;">
                                <?php echo esc_html($bank_info['sort_code'] ?? $bank_info['swift'] ?? ''); ?>
                              </div>
                            </td>
                          </tr>
                          <?php endif; ?>

                          <!-- Importe -->
                          <tr>
                            <td style="padding:14px 22px;background-color:#F4F8F2;">
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Importe a transferir</div>
                              <div style="font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:18px;font-weight:bold;color:#22322F;line-height:26px;mso-line-height-rule:exactly;">
                                <?php echo wp_kses_post($order_total); ?>
                              </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Nota de confirmación -->
              <tr>
                <td style="padding:0 22px 22px 22px;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#F4F8F2;border:1px solid #E1E9DE;border-radius:10px;">
                    <tr>
                      <td style="padding:16px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;">
                        <p style="margin:0 0 8px 0;font-size:11px;letter-spacing:1.3px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Importante</p>
                        <p style="margin:0;font-size:13.5px;color:#4A5F55;line-height:21px;mso-line-height-rule:exactly;">
                          Por favor, incluye el número de pedido <strong>#<?php echo esc_html($order_number); ?></strong> en la descripción o concepto de la transferencia para que podamos identificar tu pago más rápidamente.
                        </p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <?php endif; ?>
            </table>
          </td>
        </tr>
        <?php endif; ?>

        <!-- Resumen del pedido -->
        <tr>
          <td style="padding:30px 32px 0 32px;">
            <p style="margin:0 0 4px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:1.4px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Resumen del pedido</p>
            <p style="margin:0 0 14px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13px;color:#5C7268;line-height:20px;mso-line-height-rule:exactly;">Pedido #<?php echo esc_html($order_number); ?> · <?php echo esc_html($order_date); ?></p>

            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;background-color:#FFFFFF;border:1px solid #DCE5D8;border-radius:12px;">
              <tr>
                <td style="padding:14px 18px;border-bottom:1px solid #E7EEE4;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:15px;mso-line-height-rule:exactly;">Producto</td>
                <td align="center" width="70" style="width:70px;padding:14px 8px;border-bottom:1px solid #E7EEE4;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:15px;mso-line-height-rule:exactly;">Cant.</td>
                <td align="right" width="110" style="width:110px;padding:14px 18px;border-bottom:1px solid #E7EEE4;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:#6B8177;line-height:15px;mso-line-height-rule:exactly;">Precio</td>
              </tr>

              <!-- Productos (sin imágenes) -->
              <?php foreach ($order_items as $item) : ?>
              <tr>
                <td style="padding:16px 18px;border-bottom:1px solid #E7EEE4;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;">
                  <div style="font-size:15px;font-weight:bold;color:#22322F;line-height:21px;mso-line-height-rule:exactly;"><?php echo esc_html($item->get_name()); ?></div>
                  <?php
                  $product_id = $item->get_data()['product_id'] ?? 0;
                  if ($product_id) {
                    $product = wc_get_product($product_id);
                    if ($product) {
                      $meta = $product->get_attributes();
                      if (!empty($meta)) {
                        foreach ($meta as $attr_name => $attr_value) {
                          echo '<div style="font-size:12.5px;color:#5C7268;line-height:18px;mso-line-height-rule:exactly;">Variante: ' . esc_html($attr_value) . '</div>';
                          break;
                        }
                      }
                    }
                  }
                  ?>
                </td>
                <td align="center" style="padding:16px 8px;border-bottom:1px solid #E7EEE4;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;color:#4A5F55;line-height:21px;mso-line-height-rule:exactly;">×<?php echo esc_html($item->get_quantity()); ?></td>
                <td align="right" style="padding:16px 18px;border-bottom:1px solid #E7EEE4;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#22322F;line-height:21px;mso-line-height-rule:exactly;white-space:nowrap;"><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></td>
              </tr>
              <?php endforeach; ?>

              <!-- Totales -->
              <tr>
                <td colspan="2" style="padding:12px 18px 6px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#5C7268;line-height:20px;mso-line-height-rule:exactly;">Subtotal <span style="font-size:12px;">(sin impuestos)</span></td>
                <td align="right" style="padding:12px 18px 6px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#22322F;line-height:20px;mso-line-height-rule:exactly;white-space:nowrap;"><?php echo wp_kses_post(wc_price($subtotal)); ?></td>
              </tr>
              <?php if ($show_shipping) : ?>
              <tr>
                <td colspan="2" style="padding:0 18px 6px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#5C7268;line-height:20px;mso-line-height-rule:exactly;">Envío</td>
                <td align="right" style="padding:0 18px 6px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#22322F;line-height:20px;mso-line-height-rule:exactly;white-space:nowrap;"><?php echo wp_kses_post(wc_price($shipping_total)); ?></td>
              </tr>
              <?php endif; ?>
              <tr>
                <td colspan="2" style="padding:0 18px 14px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#5C7268;line-height:20px;mso-line-height-rule:exactly;">Método de pago</td>
                <td align="right" style="padding:0 18px 14px 18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13.5px;color:#22322F;line-height:20px;mso-line-height-rule:exactly;white-space:nowrap;"><?php echo esc_html($payment_method); ?></td>
              </tr>
              <tr>
                <td colspan="2" style="padding:14px 18px;background-color:#F4F8F2;border-top:1px solid #DCE5D8;border-radius:0 0 0 12px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:#22322F;line-height:22px;mso-line-height-rule:exactly;">Total</td>
                <td align="right" style="padding:14px 18px;background-color:#F4F8F2;border-top:1px solid #DCE5D8;border-radius:0 0 12px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:20px;color:#22322F;line-height:24px;mso-line-height-rule:exactly;white-space:nowrap;"><?php echo wp_kses_post($order_total); ?></td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Dirección de facturación -->
        <tr>
          <td style="padding:26px 32px 0 32px;">
            <p style="margin:0 0 12px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:1.4px;text-transform:uppercase;color:#6B8177;line-height:16px;mso-line-height-rule:exactly;">Dirección de facturación</p>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;background-color:#FFFFFF;border:1px solid #DCE5D8;border-radius:12px;">
              <tr>
                <td style="padding:18px;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:14px;color:#4A5F55;line-height:22px;mso-line-height-rule:exactly;">
                  <strong style="color:#22322F;"><?php echo esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?></strong><br>
                  <?php echo esc_html($order->get_billing_address_1()); ?><br>
                  <?php echo esc_html($order->get_billing_city() . ' — ' . $order->get_billing_country()); ?><br>
                  <a href="tel:+<?php echo esc_attr(preg_replace('/\D/', '', $billing_phone)); ?>" style="color:#466060;text-decoration:none;"><?php echo esc_html($billing_phone); ?></a><br>
                  <a href="mailto:<?php echo esc_attr($billing_email); ?>" style="color:#466060;text-decoration:underline;"><?php echo esc_html($billing_email); ?></a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Ayuda -->
        <tr>
          <td style="padding:22px 32px 30px 32px;">
            <p style="margin:0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:13px;color:#5C7268;line-height:21px;mso-line-height-rule:exactly;">¿Alguna duda con el pedido #<?php echo esc_html($order_number); ?>? Responde a este correo y te ayudamos.</p>
          </td>
        </tr>

        <!-- Pie -->
        <tr>
          <td style="padding:20px 32px 26px 32px;background-color:#E7EDE4;border-top:1px solid #DCE5D8;border-radius:0 0 14px 14px;">
            <p style="margin:0 0 6px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;color:#3F5449;line-height:18px;mso-line-height-rule:exactly;">avanceEmpresarial</p>
            <p style="margin:0 0 6px 0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11.5px;color:#5C7268;line-height:18px;mso-line-height-rule:exactly;">Av. Arequipa 1234, Lima, Perú</p>
            <p style="margin:0;font-family:Inter,'Helvetica Neue',Arial,Helvetica,sans-serif;font-size:11.5px;color:#5C7268;line-height:18px;mso-line-height-rule:exactly;">Este es un correo transaccional sobre tu pedido. <a href="<?php echo esc_url(home_url('/preferencias')); ?>" style="color:#466060;text-decoration:underline;">Gestionar notificaciones</a></p>
          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>
</body>
</html>
</html>
