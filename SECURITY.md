# Guía de Seguridad - Avance Nativo

## 🔒 Prácticas de Seguridad Implementadas

### 1. Sanitización de Entrada

Todas las entradas de usuario son sanitizadas antes de ser procesadas:

```php
// Sanitizar texto simple
$text = sanitize_text_field( $_POST['text'] );

// Sanitizar email
$email = sanitize_email( $_POST['email'] );

// Sanitizar número entero
$id = absint( $_GET['id'] );

// Sanitizar URL
$url = esc_url_raw( $_POST['url'] );

// Sanitizar HTML
$html = wp_kses_post( $_POST['content'] );
```

### 2. Escapado de Salida

Todos los datos mostrados en HTML son escapados:

```php
// Escapar texto
echo esc_html( $variable );

// Escapar atributos HTML
echo '<div class="' . esc_attr( $class ) . '">';

// Escapar URL
echo '<a href="' . esc_url( $url ) . '">';

// Escapar con traducción
echo esc_html__( 'Mensaje', 'avance-nativo' );

// Escapar HTML permitido
echo wp_kses_post( $content );
```

### 3. Verificación CSRF (Cross-Site Request Forgery)

Los formularios usan nonces para prevenir ataques CSRF:

```php
// En el formulario
wp_nonce_field( 'action_name', 'security_nonce' );

// Al procesar
if ( ! isset( $_POST['security_nonce'] ) || 
     ! wp_verify_nonce( $_POST['security_nonce'], 'action_name' ) ) {
    die( 'Verification failed' );
}
```

### 4. Verificación de Permisos

Se verifica siempre que el usuario tiene permisos antes de ejecutar acciones:

```php
// Verificar capacidad del usuario
if ( ! current_user_can( 'edit_posts' ) ) {
    wp_die( 'No tienes permiso' );
}

// En AJAX
check_ajax_referer( 'nonce_action', 'security' );
```

### 5. Protección Against XSS (Cross-Site Scripting)

- No se ejecuta código no escapado
- Se usa `wp_kses()` para permitir HTML seguro
- Se escapan todos los atributos HTML
- Se valida y sanitiza JavaScript

### 6. SQL Injection Protection

Se usa `$wpdb->prepare()` para todas las consultas:

```php
global $wpdb;
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $wpdb->posts WHERE ID = %d",
        $post_id
    )
);
```

### 7. Eliminación de Información Sensible

```php
// Eliminar versión de WordPress del header
remove_action( 'wp_head', 'wp_generator' );

// Quitar versión de estilos y scripts
add_filter( 'script_loader_src', 'remove_version' );
add_filter( 'style_loader_src', 'remove_version' );
```

## 🛡️ Headers de Seguridad

Se recomienda agregar estos headers en `.htaccess`:

```apache
# Prevenir clickjacking
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

## 📋 Auditoría de Seguridad

### Checklist Diario
- [ ] Mantener WordPress actualizado
- [ ] Actualizar plugins y temas
- [ ] Revisar logs de error
- [ ] Monitorear actividad de usuarios

### Checklist Semanal
- [ ] Revisar comentarios spam
- [ ] Comprobar uploads maliciosos
- [ ] Verificar usuarios inactivos
- [ ] Analizar tráfico anómalo

### Checklist Mensual
- [ ] Backup completo
- [ ] Revisar permisos de archivos
- [ ] Auditar plugins activos
- [ ] Revisar acceso administrativo

## 🔑 Configuración Recomendada en wp-config.php

```php
// Usar HTTPS
define( 'WP_HOME', 'https://example.com' );
define( 'WP_SITEURL', 'https://example.com' );

// Desabilitar edición de archivos
define( 'DISALLOW_FILE_EDIT', true );

// Cambiar prefijo de tabla
$table_prefix = 'wp_abcd1234_';

// Salts y keys únicos
define( 'AUTH_KEY', 'unique-salt-here' );
define( 'SECURE_AUTH_KEY', 'unique-salt-here' );
// ... más salts
```

## 🚨 Respuesta a Incidentes de Seguridad

### Si se detecta una vulnerabilidad:

1. **Aislamiento**
   - Desactivar el tema si es necesario
   - Bloquear acceso administrativo sospechoso

2. **Investigación**
   - Revisar logs de acceso
   - Comprobar cambios en archivos
   - Identificar la causa raíz

3. **Remediación**
   - Aplicar parches
   - Resetear contraseñas
   - Auditar cambios

4. **Comunicación**
   - Notificar a usuarios afectados
   - Documentar el incidente
   - Implementar mejoras

## 📚 Recursos Adicionales

- [WordPress Security](https://wordpress.org/support/category/security/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Plugin Security Handbook](https://developer.wordpress.org/plugins/security/)

## 🔐 Buenas Prácticas para Desarrolladores

### Nunca hagas esto:
```php
// ❌ NO: SQL injection
$wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID = " . $_GET['id'] );

// ❌ NO: XSS
echo $_POST['content'];

// ❌ NO: CSRF sin verificación
if ( isset( $_POST['action'] ) ) {
    update_option( 'setting', $_POST['value'] );
}

// ❌ NO: Contraseñas en código
define( 'DB_PASSWORD', 'password123' );

// ❌ NO: eval() o similares
eval( $_POST['code'] );
```

### Siempre haz esto:
```php
// ✅ SI: Sanitización y escapado
$user_input = sanitize_text_field( $_POST['text'] );
echo esc_html( $user_input );

// ✅ SI: Verificar permisos
if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
}

// ✅ SI: Usar API de WordPress
$results = get_posts( array( 'post__in' => array( $post_id ) ) );

// ✅ SI: Prepared statements
$wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", $post_id ) );

// ✅ SI: Verificar nonces
wp_verify_nonce( $_POST['nonce'], 'action' );
```

---

**Última actualización**: 2026-07-31
**Versión del tema**: 1.0.0
