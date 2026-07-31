# Configurar la Página de Inicio - Avance Nativo

## 📋 Pasos para Crear y Configurar tu Página de Inicio

### Paso 1: Crear una Nueva Página

1. Ve a **Entradas** → **Páginas** → **Añadir nueva**
2. Título: `Inicio` (o el nombre que desees)
3. En el contenido, escribe tu mensaje principal (este aparecerá en la sección hero)
4. **Establece una imagen destacada:** 
   - Haz clic en "Establecer imagen destacada"
   - Sube una imagen de alta calidad (recomendado: 1200x630px)
5. Publica la página

### Paso 2: Asignar la Página como Página de Inicio

1. Ve a **Configuración** → **Lectura**
2. En "La página principal muestra":
   - Selecciona **"Una página estática"**
3. En "Página principal":
   - Selecciona la página "Inicio" que creaste
4. En "Página de entradas":
   - Selecciona cualquier página (por ej: "Blog")
5. **Guardar cambios**

### Paso 3: Crear un Menú Principal

1. Ve a **Apariencia** → **Menús**
2. **Crear nuevo menú** → Dale un nombre (ej: "Menú Principal")
3. **Añade elementos:**
   - Haz clic en "Añadir elementos del menú"
   - Selecciona las páginas y posts que deseas
   - Arrastra para ordenar
4. **Mostrar ubicación:**
   - Marca "Menú Principal"
5. **Guardar menú**

---

## 🎨 Personalizar la Página de Inicio

La página de inicio incluye automáticamente:

### 1. **Sección Hero** (Arriba)
- Muestra el título y contenido de tu página
- Muestra la imagen destacada a la derecha
- Puedes agregar un botón CTA

### 2. **Sección de Características** 
- Muestra 4 tarjetas con características
- Se personaliza en el Personalizador de WordPress

### 3. **Últimas Publicaciones**
- Muestra automáticamente los 3 últimos posts
- Con imagen, autor y fecha
- Link a cada post

### 4. **Sección CTA** (Abajo)
- Sección de llamada a la acción
- Fondo azul con botón blanco
- Personalizable en el Personalizador

---

## ⚙️ Personalización Avanzada

### En el Personalizador de WordPress (Apariencia → Personalizar)

Puedes personalizar:

#### Botón del Hero (Sección Principal)
```
Colores de Avance Nativo → (próximas versiones)
```

Por ahora, edita el archivo `front-page.php` línea 26-28 para cambiar el texto y URL del botón.

#### Botón CTA (Sección Abajo)
Edita el archivo `front-page.php` para cambiar:
- `cta_title` - Título de la sección
- `cta_description` - Descripción
- `cta_button_text` - Texto del botón
- `cta_button_url` - URL del botón

---

## 📝 Ejemplo de Contenido para la Página de Inicio

### Título de la Página:
```
Bienvenido a Avance Empresarial
```

### Contenido Principal:
```
Transformamos tu negocio con soluciones digitales 
innovadoras y personalizadas. Desde diseño web 
hasta estrategia de marketing, te ayudamos a 
crecer en el mundo digital.
```

### Imagen Destacada:
- Sube una imagen profesional de tu equipo, oficina 
  o producto
- Tamaño recomendado: 1200x630px o más

---

## 🎯 Estructura de la Página de Inicio

```
┌─────────────────────────────────────────┐
│  HEADER (Logo, Menú)                    │
├─────────────────────────────────────────┤
│  SECCIÓN HERO                           │
│  [Texto]         [Imagen destacada]    │
│  [Botón CTA]                           │
├─────────────────────────────────────────┤
│  CARACTERÍSTICAS                        │
│  [Card 1] [Card 2] [Card 3] [Card 4]   │
├─────────────────────────────────────────┤
│  ÚLTIMOS POSTS                          │
│  [Post 1] [Post 2] [Post 3]            │
├─────────────────────────────────────────┤
│  SECCIÓN CTA (Llamada a la acción)     │
│  [Título] [Descripción] [Botón]        │
├─────────────────────────────────────────┤
│  FOOTER (Links, Widgets)                │
└─────────────────────────────────────────┘
```

---

## 🎨 Personalizar Características (Sección de Tarjetas)

Para cambiar las características mostradas en la página de inicio:

### Opción 1: Editar el Archivo Directamente

1. Abre tu editor de archivos
2. Ve a: `wp-content/themes/avance-nativo/front-page.php`
3. Busca la línea 37 (Sección de características)
4. Modifica el array:

```php
$features = array(
    array(
        'title' => 'Tu Característica 1',
        'description' => 'Descripción de la característica 1'
    ),
    array(
        'title' => 'Tu Característica 2',
        'description' => 'Descripción de la característica 2'
    ),
);
```

### Opción 2: Usar el Personalizador (Futuro)

Próximamente agregaremos esta opción al Personalizador de WordPress.

---

## 📱 Vista Responsiva

La página de inicio se adapta automáticamente a:

- **Desktop** (1200px+) - 2 columnas en hero, 4 en características
- **Tablet** (768px-1199px) - Ajustes automáticos
- **Móvil** (< 768px) - 1 columna, elementos apilados

---

## ✅ Checklist de Configuración

```
[ ] Creé la página "Inicio" con contenido
[ ] Subí imagen destacada a la página
[ ] Asigné la página como "Página de inicio"
[ ] Creé el menú principal
[ ] Asigné el menú como "Menú Principal"
[ ] Verifiqué que la página se ve bien en móvil
[ ] Agregué posts para la sección de últimas publicaciones
[ ] Probé los botones CTA
```

---

## 🚀 Próximas Personalizaciones

Una vez que tu página de inicio esté funcionando:

1. **Crea más posts** para la sección de últimas publicaciones
2. **Agrega un menú footer** con links útiles
3. **Configura widgets** en el footer
4. **Personaliza el color primario** en Apariencia → Personalizar

---

## 📞 Problemas Comunes

### El contenido no aparece en la sección hero
- Verifica que la página "Inicio" esté publicada
- Revisa que esté asignada como "Página de inicio" en Configuración

### Las imágenes se ven mal
- Sube imágenes en alta resolución (mínimo 1200px de ancho)
- Usa formato JPG o PNG
- Asegúrate de que las imágenes tengan el tamaño correcto

### La página se ve diferente en móvil
- Esto es normal, los estilos se adaptan automáticamente
- Prueba en DevTools (F12) con diferentes tamaños

### El botón CTA no funciona
- Asegúrate de que la URL sea correcta (incluye https://)
- Prueba haciendo clic en el botón

---

**¡Tu página de inicio está lista para impresionar!** 🎉

Para más información, consulta README.md o TROUBLESHOOTING.md
