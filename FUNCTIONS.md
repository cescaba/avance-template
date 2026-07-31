# Referencia de Funciones - Avance Nativo

Documentación de todas las funciones personalizadas disponibles en el tema.

## 🎨 Funciones de Utilidad

### avance_nativo_get_site_title()

Obtiene el título del sitio escapado de forma segura.

**Uso:**
```php
echo avance_nativo_get_site_title();
```

**Retorna:** `string` Título del sitio escapado

**Ejemplo:**
```php
$title = avance_nativo_get_site_title();
// Resultado: "Mi Sitio Web"
```

---

### avance_nativo_get_site_description()

Obtiene la descripción del sitio escapada.

**Uso:**
```php
echo avance_nativo_get_site_description();
```

**Retorna:** `string` Descripción escapada o cadena vacía

**Ejemplo:**
```php
$desc = avance_nativo_get_site_description();
// Resultado: "Una descripción increíble"
```

---

### avance_nativo_get_site_logo()

Obtiene el HTML del logo personalizado.

**Uso:**
```php
echo avance_nativo_get_site_logo();
```

**Retorna:** `string` HTML del logo o cadena vacía

**Ejemplo:**
```php
$logo = avance_nativo_get_site_logo();
// Resultado: <img src="..." class="custom-logo" ...>
```

---

### avance_nativo_get_copyright_year()

Obtiene el año actual para el copyright.

**Uso:**
```php
echo avance_nativo_get_copyright_year();
```

**Retorna:** `string` Año actual (ej: "2026")

**Ejemplo:**
```php
echo '&copy; ' . avance_nativo_get_copyright_year();
// Resultado: © 2026
```

---

### avance_nativo_get_primary_menu()

Obtiene el menú principal en array. ⚠️ **Nota:** Usa echo directamente en el template.

**Uso:**
```php
wp_nav_menu( array(
    'theme_location' => 'primary',
    'container'      => 'nav',
    'container_class' => 'primary-navigation',
) );
```

**Retorna:** `void` Imprime el menú directamente

---

### avance_nativo_fallback_menu()

Menú de fallback cuando no existe un menú asignado.

**Uso automático:** Se ejecuta automáticamente si no hay menú.

**Retorna:** `void` Imprime un mensaje de fallback

---

## 🔐 Funciones de Seguridad

### avance_nativo_kses_post_html()

Sanitiza contenido HTML permitiendo etiquetas seguras.

**Uso:**
```php
$safe_html = avance_nativo_kses_post_html( $user_content );
```

**Parámetros:**
- `$content` (string) Contenido a sanitizar

**Retorna:** `string` Contenido sanitizado

**Etiquetas permitidas:**
- `<a>`, `<b>`, `<strong>`, `<i>`, `<em>`
- `<br>`, `<p>`, `<span>`, `<div>`

**Ejemplo:**
```php
$user_text = '<p>Hola <strong>mundo</strong></p>';
$safe = avance_nativo_kses_post_html( $user_text );
echo $safe; // Seguro de mostrar
```

---

## 🎯 Hooks y Filtros

### Acciones

#### avance_nativo_setup

Se ejecuta al configurar el tema.

```php
add_action( 'after_setup_theme', 'avance_nativo_setup' );
```

#### avance_nativo_enqueue_assets

Se ejecuta al encolar estilos y scripts.

```php
add_action( 'wp_enqueue_scripts', 'avance_nativo_enqueue_assets' );
```

#### avance_nativo_dequeue_default_styles

Se ejecuta para eliminar estilos innecesarios.

```php
add_action( 'wp_enqueue_scripts', 'avance_nativo_dequeue_default_styles', 20 );
```

#### avance_nativo_customize_register

Se ejecuta para registrar opciones de personalización.

```php
add_action( 'customize_register', 'avance_nativo_customize_register' );
```

#### avance_nativo_custom_colors

Se ejecuta en wp_head para agregar colores personalizados.

```php
add_action( 'wp_head', 'avance_nativo_custom_colors' );
```

---

### Filtros de WordPress Utilizados

- `nav_menu_css_class` - Modificar clases de menú
- `nav_menu_link_attributes` - Modificar atributos de links
- `nav_menu_item_title` - Modificar título de items
- `style_loader_src` - Eliminar versión de estilos
- `script_loader_src` - Eliminar versión de scripts

---

## 📦 Walker Personalizado

### Avance_Nativo_Menu_Walker

Walker personalizado para renderizar menús de forma segura.

**Características:**
- Escapado de atributos
- Soporte para submenús
- URLs seguras
- Atributos accesibles

**Uso:**
```php
wp_nav_menu( array(
    'theme_location' => 'primary',
    'walker'         => new Avance_Nativo_Menu_Walker(),
) );
```

---

## 📋 Variables Globales de JavaScript

El tema proporciona una variable global de JavaScript:

```javascript
// En tu archivo JavaScript
console.log( avanceNativo.nonce ); // Nonce CSRF
```

**Propiedades disponibles:**
- `avanceNativo.nonce` - Nonce para verificación CSRF

---

## 🎨 Variables CSS Disponibles

### Colores

```css
--color-primary: #2563eb
--color-primary-dark: #1e40af
--color-primary-light: #3b82f6
--color-secondary: #64748b
--color-secondary-light: #94a3b8
--color-success: #10b981
--color-warning: #f59e0b
--color-error: #ef4444
--color-info: #0ea5e9
```

### Neutros

```css
--color-white: #ffffff
--color-black: #000000
--color-gray-50 a --color-gray-900
```

### Espaciado

```css
--spacing-xs: 0.25rem
--spacing-sm: 0.5rem
--spacing-md: 1rem
--spacing-lg: 1.5rem
--spacing-xl: 2rem
--spacing-2xl: 3rem
--spacing-3xl: 4rem
--spacing-4xl: 6rem
```

### Tipografía

```css
--font-family-base: system fonts stack
--font-size-sm: 0.875rem
--font-size-base: 1rem
--font-size-lg: 1.125rem
--line-height-tight: 1.2
--line-height-normal: 1.5
--line-height-relaxed: 1.75
```

### Bordes

```css
--border-radius-sm: 0.375rem
--border-radius-md: 0.5rem
--border-radius-lg: 0.75rem
--border-radius-xl: 1rem
```

### Sombras

```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05)
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1)
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1)
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1)
```

### Transiciones

```css
--transition-fast: 150ms ease-in-out
--transition-normal: 300ms ease-in-out
--transition-slow: 500ms ease-in-out
```

---

## 🔧 Personalización en Child Theme

Para crear un child theme, crea una carpeta `avance-nativo-child` con:

**wp-content/themes/avance-nativo-child/style.css:**
```css
/*
Theme Name: Avance Nativo Child
Theme URI: https://example.com
Description: Child theme de Avance Nativo
Author: Tu Nombre
Template: avance-nativo
Version: 1.0.0
*/

@import url("../avance-nativo/style.css");

/* Tus estilos personalizados aquí */
```

**wp-content/themes/avance-nativo-child/functions.php:**
```php
<?php
// Tus funciones personalizadas aquí
function avance_nativo_child_custom_function() {
    // Tu código
}
```

---

## 📚 Ejemplos Prácticos

### Personalizar el Loop

En `index.php`:
```php
<?php
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        // Tu código aquí
    }
}
?>
```

### Agregar un Campo Personalizado

En `functions.php`:
```php
function mi_campo_personalizado() {
    $value = get_post_meta( get_the_ID(), 'mi_campo', true );
    echo esc_html( $value );
}
```

### Crear un Shortcode

En `functions.php`:
```php
function mi_shortcode() {
    return '<p>Mi contenido</p>';
}
add_shortcode( 'mi_shortcode', 'mi_shortcode' );
```

Uso en posts:
```
[mi_shortcode]
```

---

## 🚨 Funciones Deprecadas

Ninguna función está deprecada en esta versión.

---

## 📝 Notas de Desarrollo

- Siempre sanitiza entradas de usuario
- Siempre escapa outputs
- Usa funciones de WordPress en lugar de funciones PHP puras
- Respeta los permisos del usuario
- Mantén compatibilidad con PHP 7.4+

---

**Última actualización:** 2026-07-31  
**Versión:** 1.0.0
