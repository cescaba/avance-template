# Setup Manual - Página de Checkout + Métodos de Pago

## ✅ Archivos Creados Automáticamente

1. **setup-checkout-page.php** — Crea automáticamente la página `finalizar-compra/`
2. **setup-payment-methods.php** — Activa métodos de pago (Transferencia Bancaria + Pago Manual)
3. Ambos incluidos en `functions.php` y se ejecutan al cargar WordPress

## 🔧 Cómo Probar

### Paso 1: Vaciar caché del navegador
- Abre DevTools (F12)
- Clic derecho en "Recargar" → "Vaciar caché y recargar"
- O entra en modo incógnito

### Paso 2: Ir a WordPress Admin
1. Abre `wp-admin`
2. Ve a **Herramientas → Auditoría Reservas** (o **Tools → Audit Reservations**)
3. Verifica que vea:
   - ✅ WooCommerce Activo
   - ✅ Clases cargadas (Manager, Validator, Cart Handler)
   - ✅ Productos creados
   - ✅ AJAX Handlers registrados
   - ✅ URL Checkout disponible

### Paso 3: Verificar página de checkout
1. Ve a **Páginas → Todas las páginas**
2. Busca "Finalizar Compra" (debe existir y estar publicada)
3. Abre la página → Debe mostrar el formulario de pago de WooCommerce

### Paso 4: Prueba el flujo completo
1. Ve a la página de Mentorías
2. Selecciona un plan
3. Elige una fecha en el calendario
4. Llena el formulario (nombre, email, WhatsApp, notas)
5. Click en "Confirmar Reserva"
6. **DEBE redirigir a `/finalizar-compra/` con el carrito precargado**

## 🛠️ Si Algo No Funciona

### La página no aparece
- Ejecuta esto en terminal (desde la carpeta del sitio):
```bash
wp eval 'delete_option("avance_checkout_page_created"); delete_option("avance_payment_methods_configured");'
wp cache flush
```
- Luego recarga wp-admin

### Los métodos de pago no aparecen
1. Ve a **WooCommerce → Ajustes → Pagos**
2. Verifica que estos estén activos:
   - ✅ Direct Bank Transfer (Transferencia Bancaria)
   - ✅ Cash on Delivery (Pago Manual)

### La URL sigue siendo /home
- Verifica que WooCommerce esté completamente activo
- En terminal: `wp plugin list | grep woocommerce`
- Si está en "inactive", actívalo: `wp plugin activate woocommerce`

## 📝 Configuración Manual (Opcional)

Si prefieres hacer algo manual en lugar de automático:

1. **Ir a Páginas → Agregar nueva**
2. Título: "Finalizar Compra"
3. En el contenido, agregar shortcode: `[woocommerce_checkout]`
4. URL amigable: `finalizar-compra`
5. Publicar
6. Ve a **WooCommerce → Ajustes → Avanzado → Página de checkout**
7. Selecciona "Finalizar Compra"
8. Guardar

## 🚀 Siguientes Pasos

Cuando confirmes que funciona:
1. Configura los datos reales de tu banco en WooCommerce → Pagos
2. Prueba una reserva real
3. Verifica que se registre en la BD (`wp_postmeta`)

---

**IMPORTANTE:** Este setup se ejecuta UNA SOLA VEZ. Las banderas evitan que se duplique.
Si necesitas reset, ejecuta desde terminal:
```bash
wp eval 'delete_option("avance_checkout_page_created"); delete_option("avance_payment_methods_configured");'
```
