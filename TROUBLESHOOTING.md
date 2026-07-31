# Troubleshooting - Avance Nativo

## ⚠️ Los estilos CSS no aparecen

Si tu página se ve sin estilos (solo texto blanco y negro), sigue estos pasos:

### 1. Verifica que wp_head() esté en header.php
Abre `wp-admin/wp-content/themes/avance-nativo/header.php`

Debe contener entre las etiquetas `<head>`:
```php
<?php wp_head(); ?>
```

**Estado:** ✅ Verificado en línea 17

### 2. Verifica que wp_footer() esté en footer.php
Abre `wp-content/themes/avance-nativo/footer.php`

Debe contener antes del cierre de `</body>`:
```php
<?php wp_footer(); ?>
```

**Estado:** ✅ Verificado en línea 63

### 3. Verifica que el enqueue esté en functions.php

El archivo `functions.php` debe tener esta función:
```php
function avance_nativo_enqueue_assets() {
    wp_enqueue_style(
        'avance-nativo-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'avance_nativo_enqueue_assets' );
```

**Estado:** ✅ Verificado y actualizado

### 4. Borra la caché del navegador

1. Abre tu navegador
2. Presiona **Ctrl + Shift + Delete** (o Cmd + Shift + Delete en Mac)
3. Selecciona "Caché" y "Cookies"
4. Haz clic en "Limpiar datos"
5. Recarga tu sitio

### 5. Verifica que style.css existe

El archivo debe estar en:
```
wp-content/themes/avance-nativo/style.css
```

**Estado:** ✅ Archivo existe (11,668 bytes)

### 6. Verifica los permisos de archivos

Los permisos deben ser:
- Carpetas: `755`
- Archivos PHP: `644`
- Archivos CSS: `644`

En Windows/Local, esto normalmente no es problema.

### 7. Verifica en el código fuente

1. Abre tu sitio en el navegador
2. Presiona **Ctrl + U** para ver código fuente
3. Busca `<link rel="stylesheet" id="avance-nativo-style-css"`

Si no está ahí, hay un problema con el enqueue.

### 8. Activa WP_DEBUG

En `wp-config.php`, agrega:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Luego revisa los logs en `wp-content/debug.log`

---

## 📋 Checklist de Verificación Rápida

- [ ] Tema "Avance Nativo" está activado en Apariencia → Temas
- [ ] wp_head() está en header.php
- [ ] wp_footer() está en footer.php
- [ ] Función de enqueue está en functions.php
- [ ] Archivo style.css existe en la carpeta del tema
- [ ] Caché del navegador fue limpiado
- [ ] No hay errores en wp-content/debug.log

---

## 🔧 Pasos de Diagnóstico Avanzados

### Si nada funciona:

1. **Desactiva y reactiva el tema:**
   - Ve a Apariencia → Temas
   - Activa otro tema temporalmente
   - Luego vuelve a activar Avance Nativo

2. **Verifica errores de PHP:**
   - Añade al inicio de functions.php:
   ```php
   error_log( 'Avance Nativo theme activated' );
   ```
   - Revisa `wp-content/debug.log`

3. **Comprueba la estructura de archivos:**
   ```
   wp-content/themes/avance-nativo/
   ├── style.css ✓
   ├── functions.php ✓
   ├── header.php ✓
   ├── footer.php ✓
   ├── index.php ✓
   ├── single.php
   ├── page.php
   ├── comments.php
   └── js/
       └── main.js
   ```

4. **Reinicia WordPress:**
   - Si usas Local, reinicia el sitio
   - Limpia la caché (si Local tiene esa opción)
   - Recarga el navegador varias veces

---

## 🆘 Errores Comunes

### "Tema no se ve en la lista"
**Solución:**
- Verifica que style.css tenga los metadatos correctos en la parte superior
- Reinicia WordPress

### "Error 500 al activar el tema"
**Solución:**
- Revisa `wp-content/debug.log`
- Aumenta `memory_limit` en `wp-config.php`:
  ```php
  define( 'WP_MEMORY_LIMIT', '256M' );
  ```

### "Menú no aparece"
**Solución:**
- Crea un menú en Apariencia → Menús
- Asigna a "Menú Principal"
- Recarga la página

### "Widgets no aparecen"
**Solución:**
- Ve a Apariencia → Widgets
- Arrastra widgets a las áreas del footer
- Guarda y recarga

---

## 📞 Información Útil

**Versión del Tema:** 1.0.0  
**Requerimientos:** WordPress 5.0+, PHP 7.4+  
**Última Actualización:** 2026-07-31

---

**¿Aún hay problemas? Verifica:**
1. Los logs de error (wp-content/debug.log)
2. La consola del navegador (F12 → Console)
3. El código fuente HTML (Ctrl+U)
