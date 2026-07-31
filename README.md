# Avance Nativo - Tema WordPress 100% Nativo

Un tema WordPress completamente nativo, seguro y responsivo sin dependencias externas.

## 📋 Características

### Estructura Base
- ✅ **style.css** - Metadatos del tema y estilos CSS vanilla
- ✅ **functions.php** - Setup, enqueue de recursos, seguridad
- ✅ **header.php** - Cabecera con logo, título y menú
- ✅ **footer.php** - Pie de página con widgets y copyright
- ✅ **index.php** - Template principal
- ✅ **single.php** - Template para posts individuales
- ✅ **page.php** - Template para páginas
- ✅ **comments.php** - Template para comentarios

### Estilos y Diseño
- 📱 **Mobile-first responsive** - Diseño adaptable desde móviles
- 🎨 **Variables CSS** - Colores y espaciado personalizables
- 🌈 **Sistema de colores** - Primarios, secundarios y neutros
- 📐 **Tipografía escalada** - Tamaños responsivos
- ⚡ **CSS vanilla** - Sin frameworks ni preprocesadores

### Menú y Navegación
- 🧭 **Menú principal** - Soporte para jerarquía de menú
- 📲 **Menú móvil** - Toggle funcional con JavaScript
- 🔗 **Menú footer** - Área de navegación en pie de página
- ✨ **Walker personalizado** - Renderizado seguro de menú

### Seguridad
- 🔐 **Nonces** - Protección contra CSRF
- 🛡️ **Sanitización** - Limpieza de entrada de usuarios
- 🔒 **Escapado** - Todos los outputs escapados correctamente
- 🚫 **XSS Protection** - Funciones wp_kses para contenido HTML
- 🔍 **Verificación de seguridad** - Eliminación de headers innecesarios

### Características WordPress
- 💾 **Soporte para miniaturas** - Tamaños de imagen personalizados
- 🎯 **Soporte para menús** - Menú primario y footer
- 📦 **Widgets** - 3 áreas de widgets en footer
- 🎨 **Personalización** - Panel de personalización para colores
- 🔤 **i18n Ready** - Totalmente traducible
- 📝 **Soporta comentarios** - Template personalizado
- 🔗 **Soporte de enlaces HTML5** - Feeds automáticos

## 🚀 Instalación

1. Descarga el tema en `wp-content/themes/avance-nativo/`
2. Ve a Apariencia → Temas en el administrador de WordPress
3. Activa "Avance Nativo"
4. Configura tu menú en Apariencia → Menús
5. Personaliza colores en Apariencia → Personalizar

## 📖 Uso

### Configurar Menú Principal
1. Apariencia → Menús
2. Crea un nuevo menú o edita uno existente
3. Asigna a "Menú Principal"

### Añadir Widget en Footer
1. Apariencia → Widgets
2. Arrastra widgets a "Footer - Columna 1, 2 o 3"

### Personalizar Colores
1. Apariencia → Personalizar
2. Ve a "Colores de Avance Nativo"
3. Ajusta el color primario

### Cambiar Logo
1. Apariencia → Personalizar
2. Ve a "Identidad del sitio"
3. Carga tu logo personalizado

## 🔒 Medidas de Seguridad Implementadas

```php
// Sanitización de entrada
sanitize_text_field()   // Texto simple
sanitize_email()        // Emails
sanitize_hex_color()    // Colores

// Escapado de salida
esc_html()              // Texto
esc_attr()              // Atributos HTML
esc_url()               // URLs
esc_html__()            // Strings traducibles
wp_kses_post()          // Contenido HTML filtrado

// Protección CSRF
wp_nonce_field()        // Crear nonce
wp_verify_nonce()       // Verificar nonce

// Verificación de permisos
current_user_can()      // Verificar capacidades
```

## 🎨 Variables CSS Disponibles

### Colores
```css
--color-primary: #2563eb
--color-primary-dark: #1e40af
--color-primary-light: #3b82f6
--color-secondary: #64748b
--color-success: #10b981
--color-warning: #f59e0b
--color-error: #ef4444
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
--font-family-base: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto
--font-size-base: 1rem
--font-size-lg: 1.125rem
--font-size-xl: 1.25rem
--line-height-normal: 1.5
--line-height-relaxed: 1.75
```

## 📱 Breakpoints Responsivos

- **Móvil**: hasta 480px
- **Tablet**: hasta 768px
- **Desktop**: 768px en adelante

## 🔧 Funciones Útiles

```php
// Obtener información del sitio
avance_nativo_get_site_title()       // Nombre escapado
avance_nativo_get_site_description() // Descripción escapada
avance_nativo_get_copyright_year()   // Año actual
avance_nativo_get_site_logo()        // Logo personalizado

// Menú
avance_nativo_get_primary_menu()     // Obtener menú principal
avance_nativo_fallback_menu()        // Menú de fallback
```

## 🎯 Filtros Disponibles

- `nav_menu_css_class` - Modificar clases de menú
- `nav_menu_link_attributes` - Modificar atributos de links
- `nav_menu_item_title` - Modificar título de item

## 📦 Dependencias

- **WordPress**: 5.0+
- **PHP**: 7.4+
- **No usa**: Plugins, frameworks CSS, ACF

## 🔄 Actualizaciones

Las actualizaciones deben hacerse manualmente. Se recomienda crear un child theme para personalizaciones.

## 📄 Licencia

GPL v2 o superior

## 🤝 Soporte

Para reportar bugs o sugerir mejoras, contacta al equipo de desarrollo.

---

Creado con ❤️ para Avance Empresarial
