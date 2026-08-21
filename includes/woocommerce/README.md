# WooCommerce Integration

Contiene toda la lógica de integración con WooCommerce del tema.

## Estructura

```
includes/woocommerce/
├── index.php                    # Índice principal que carga todos los módulos
└── ../mentoria/                 # Módulo de Mentoría y Reservas
    ├── class-mentoria-handler.php          # AJAX handler para procesar reservas
    ├── class-mentoria-updater.php          # Actualiza productos de WooCommerce
    └── class-mentoria-woocommerce-fix.php  # Arregla configuración de checkout
```

## Módulos

### Mentoría (class-mentoria-handler.php)
Maneja el flujo de reserva de sesiones de mentoría:
- Recibe datos del formulario de reserva (plan, fecha, hora, datos cliente)
- Valida y sanitiza información
- Crea carrito en WooCommerce con el producto correspondiente
- Redirige a checkout para procesar pago

### Actualizador (class-mentoria-updater.php)
Sincroniza productos de WooCommerce:
- Crea/actualiza productos: Entrada (S/ 490), Pro (S/ 890), Sesión puntual (S/ 180)
- Se ejecuta automáticamente en primer carga

### Arreglador (class-mentoria-woocommerce-fix.php)
Verifica y arregla configuración:
- Desactiva modo "Coming Soon" si está activo
- Crea/verifica páginas de WooCommerce (Shop, Carrito, Checkout, Mi Cuenta)
- Verifica permalinks
- Se ejecuta en cada carga para asegurar consistencia

## Flujo de Reserva

1. Usuario selecciona plan → fecha → hora → llena datos
2. Click en "Confirmar reserva"
3. AJAX valida datos (frontend + backend)
4. Crea carrito en WooCommerce con producto + meta datos
5. Redirige a `/finalizar-compra/` (checkout)
6. Usuario completa pago

## Configuración

- Nonce: `avance_mentoria_booking`
- Action AJAX: `avance_mentoria_booking`
- JavaScript: `/assets/js/mentoria-reserva.js`
- CSS: `/assets/css/page-mentoria.css`
- Página de mentoría: `/templates/page-mentoria.php`

## Debug

Para debug, habilitar en wp-config.php:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Los logs aparecerán en `/wp-content/debug.log`
