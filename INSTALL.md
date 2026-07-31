# Guía de Instalación - Avance Nativo

## 🚀 Instalación Rápida

### Paso 1: Activar el Tema

1. Accede al panel de administración de WordPress (wp-admin)
2. Ve a **Apariencia** → **Temas**
3. Busca "Avance Nativo"
4. Haz clic en **Activar**

### Paso 2: Configuración Básica

#### Identidad del Sitio
1. **Apariencia** → **Personalizar**
2. **Identidad del sitio**
3. Configura:
   - Título del sitio
   - Descripción
   - Logo personalizado (opcional)

#### Menú Principal
1. **Apariencia** → **Menús**
2. **Crear nuevo menú** (si no existe)
3. Añade tus páginas y posts
4. **Mostrar ubicación**: Marca "Menú Principal"
5. **Guardar menú**

#### Menú Footer
1. **Apariencia** → **Menús**
2. Crea o edita un menú
3. **Mostrar ubicación**: Marca "Menú del Footer"
4. **Guardar menú**

#### Widgets
1. **Apariencia** → **Widgets**
2. Arrastra widgets a:
   - Footer - Columna 1
   - Footer - Columna 2
   - Footer - Columna 3
3. Configura cada widget según necesites

#### Personalización de Colores
1. **Apariencia** → **Personalizar**
2. **Colores de Avance Nativo**
3. Ajusta el "Color Primario"
4. **Publicar**

### Paso 3: Crear Contenido

1. Crea tus primeras **Páginas**:
   - Inicio
   - Sobre Nosotros
   - Contacto

2. Crea tu primer **Post**:
   - Ve a **Entradas**
   - **Añadir nueva**
   - Escribe tu contenido
   - Publica

3. Organiza tu contenido:
   - Crea **Categorías**
   - Añade **Etiquetas**
   - Asigna imágenes destacadas

## 📋 Checklist de Configuración

- [ ] Tema activado
- [ ] Título y descripción del sitio
- [ ] Logo personalizado
- [ ] Menú principal creado
- [ ] Menú footer creado
- [ ] Widgets del footer configurados
- [ ] Color primario personalizado
- [ ] Páginas principales creadas
- [ ] Primer post publicado
- [ ] Comentarios habilitados (opcional)

## 🎨 Personalización

### Cambiar Colores Globales

Los colores se pueden personalizar editando `style.css`:

```css
:root {
  --color-primary: #2563eb;        /* Tu color principal */
  --color-primary-dark: #1e40af;   /* Versión oscura */
  --color-primary-light: #3b82f6;  /* Versión clara */
}
```

### Modificar Tipografía

En `style.css`, busca la sección "TIPOGRAFÍA":

```css
body {
  font-family: 'Tu Fuente', sans-serif;
}
```

### Agregar tu Logo

1. **Apariencia** → **Personalizar**
2. **Identidad del sitio**
3. Haz clic en "Logo"
4. Sube tu imagen
5. Publica los cambios

## 🔧 Troubleshooting

### El menú no aparece
- Verifica que has creado un menú en **Apariencia** → **Menús**
- Asegúrate de que está asignado a "Menú Principal"
- Limpia el cache del navegador

### Los widgets no aparecen
- Ve a **Apariencia** → **Widgets**
- Arrastra widgets a las áreas del footer
- Verifica que los widgets tengan contenido

### La página se ve distinta en móvil
- Esto es normal, el tema es mobile-first
- Prueba en diferentes tamaños de pantalla
- Abre DevTools (F12) y activa "Responsive Design Mode"

### El color personalizado no cambia
- Abre **Apariencia** → **Personalizar**
- Ve a "Colores de Avance Nativo"
- Cambia el "Color Primario"
- Haz clic en **Publicar**
- Limpia el cache del navegador

## 📱 Testing Responsivo

Para verificar que el tema se ve bien en móvil:

1. Abre tu sitio en navegador
2. Presiona **F12** (DevTools)
3. Haz clic en el icono de dispositivo móvil
4. Prueba diferentes tamaños:
   - Móvil: 375px
   - Tablet: 768px
   - Desktop: 1200px+

## 🔐 Seguridad Post-Instalación

1. **Cambiar contraseña de admin**
   - Usuarios → Tu perfil → Cambiar contraseña

2. **Actualizar WordPress**
   - Ir a Panel de Control
   - Si hay actualizaciones disponibles, aplícalas

3. **Limpiar comentarios spam**
   - Comentarios → Spam (si hay)
   - Marcar como spam y eliminar

4. **Revisar usuarios**
   - Usuarios → Todos
   - Eliminar usuarios innecesarios

## 📞 Soporte

Si tienes problemas:

1. Consulta la [Documentación Completa](README.md)
2. Revisa la [Guía de Seguridad](SECURITY.md)
3. Verifica los logs de error de WordPress

## 🎯 Próximos Pasos

Después de instalar y configurar:

1. Crea contenido de calidad
2. Optimiza para SEO
3. Configura Google Analytics
4. Haz backup regular
5. Mantén WordPress y el tema actualizados

---

**¡Tu sitio está listo para despegar! 🚀**
