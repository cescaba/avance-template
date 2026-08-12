# Formulario de Contacto → WhatsApp (con BD y validación)

## Descripción

Sistema profesional de formulario de contacto con:
- **Validación de seguridad** (detección de spam, inyecciones, trolls)
- **Almacenamiento en BD** (historial de contactos)
- **Envío a WhatsApp** (automático después de pasar validaciones)

**Flujo:**
1. Usuario completa el formulario en la página de inicio
2. Aprieta "Enviar mensaje"
3. Frontend envía datos via AJAX al backend
4. Backend valida seguridad (spam, malware, inyecciones)
5. Si pasa validación → Se guarda en BD y se abre WhatsApp
6. Usuario confirma el envío en WhatsApp
7. El mensaje llega al dueño del sitio

## Configuración

### Cambiar el número del dueño

Edita el archivo `config/theme-config.php`:

```php
define('AVANCE_WHATSAPP_OWNER', '51999000000'); // ← Cambiar aquí
```

**Formato requerido:** Código de país + número (sin +, sin espacios, sin guiones)

Ejemplos:
- Perú: `51999000000`
- Colombia: `573012345678`
- Argentina: `541123456789`

## Estructura técnica

### Archivos

| Archivo | Propósito |
|---------|-----------|
| `config/theme-config.php` | Configuración de WhatsApp (número del dueño) |
| `assets/js/contact-whatsapp.js` | Lógica del formulario (IIFE, modular, sin dependencias) |
| `templates/page-inicio.php` | HTML del formulario |
| `functions.php` | Enqueue de scripts y localización de datos |

### Campos del formulario

| Campo | ID | Obligatorio | Nota |
|-------|----|----|------|
| Nombre completo | `contacto_wsp_nombre` | Sí | Validación en JS |
| WhatsApp del visitante | `contacto_wsp_numero` | Sí | Solo para que el dueño sepa a quién contactar |
| Servicio de interés | `contacto_wsp_asunto` | Sí | Select con opciones predefinidas |
| Mensaje adicional | `contacto_wsp_mensaje` | No | Campo de texto libre |

### Validación

- Nombre: Requerido, no vacío
- Número: Requerido, no vacío
- Asunto: Requerido, debe seleccionar una opción
- Mensaje: Opcional

Si falta algún campo requerido, se muestra un `alert` con el mensaje de error.

## Seguridad

✅ **No requiere backend:** Todo sucede en el navegador del usuario  
✅ **Usa URLs wa.me oficiales de Meta:** Sin APIs externas  
✅ **Sin almacenamiento de datos:** No se guardan los datos en la BD (opcional añadirlo)  
✅ **Sanitización:** HTML escapeado con `esc_html_e()` y `esc_attr_e()`  

## Base de Datos

### Tabla: `wp_avance_contacts`

Se crea automáticamente al activar el tema. Campos:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único del contacto |
| `nombre` | VARCHAR(255) | Nombre del contacto |
| `email` | VARCHAR(255) | Email del contacto |
| `numero` | VARCHAR(20) | Número de WhatsApp |
| `asunto` | VARCHAR(255) | Tema de la consulta |
| `mensaje` | LONGTEXT | Mensaje adicional |
| `status` | VARCHAR(20) | Estado: pendiente, enviado, bloqueado, error, spam |
| `validation_reason` | VARCHAR(100) | Razón del bloqueo (si aplica) |
| `created_at` | DATETIME | Fecha de creación |
| `updated_at` | DATETIME | Última actualización |
| `ip_address` | VARCHAR(45) | IP del usuario |
| `user_agent` | TEXT | Navegador del usuario |

### Estados del Contacto

- `pendiente` - En proceso de validación
- `enviado` - Validado y WhatsApp abierto
- `bloqueado` - Bloqueado por seguridad (spam, inyección, etc)
- `error` - Error al procesar
- `spam` - Detectado como spam

## Validaciones de Seguridad

### 1. Inyección SQL
Detecta patrones como: `UNION SELECT`, `DROP TABLE`, etc.

### 2. Scripts/HTML
Bloquea etiquetas HTML: `<script>`, `<iframe>`, etc.

### 3. URLs/Enlaces
Bloquea múltiples URLs en un mensaje (típico de spam)

### 4. Palabras clave bloqueadas
Spam común: viagra, casino, bitcoin, crypto, forex, etc.

### 5. Repetición de caracteres
Detecta spam obvio: `aaaaaaa`, `!!!!!!`, etc.

### 6. Validación de email
Solo emails válidos

### 7. Rate limiting (futuro)
Se puede agregar: máx N contactos por IP en X tiempo

## Mejoras futuras (opcional)

### 1. Guardar datos en BD (CPT o tabla custom)

Crear un submit handler que además de abrir WhatsApp, guarde los datos.

### 2. Enviar email al dueño

Paralelo a WhatsApp: el formulario también envía email.

### 3. Validación de número

Validar que el formato del número sea correcto (código país + 9-10 dígitos).

### 4. Modal de error elegante

En lugar de `alert()`, mostrar un modal CSS limpio.

### 5. Confirmación post-envío

Mensaje "Tu mensaje se abrirá en WhatsApp" con toast/snackbar.

## Testing

### Móvil
- Abre en Safari/Chrome en iPhone
- Completa el formulario y aprieta enviar
- Verifica que se abre WhatsApp nativo

### Desktop
- Abre en navegador en computadora
- Completa el formulario y aprieta enviar
- Verifica que se abre WhatsApp Web en nueva pestaña

### Validación
- Deja campos vacíos y aprieta enviar → debe mostrar error
- Completa todos los campos requeridos → debe abrir WhatsApp

## Notas técnicas

- El JavaScript usa **IIFE** (Immediately Invoked Function Expression) para evitar polucionar el scope global
- No depende de jQuery u otras librerías (vanilla JS)
- Compatible con todos los navegadores modernos
- Se enqueue solo en la página de inicio (`page-inicio.php`)
