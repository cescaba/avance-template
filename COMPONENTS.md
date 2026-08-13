# Componentes Reutilizables

Guía de uso para los componentes reutilizables del tema Avance.

## 1. Scheduling Component (Agendamiento)

### Ubicación
- Clase: `includes/class-scheduling-component.php`
- Template Part: `template-parts/scheduling-section.php`

### Uso Básico
```php
<?php
$scheduling = new Avance_Scheduling_Component();
echo $scheduling->render();
Avance_Scheduling_Component::enqueue_assets();
?>
```

### Con Configuración Personalizada
```php
<?php
$scheduling = new Avance_Scheduling_Component(array(
    'title' => 'Mi título personalizado',
    'subtitle' => 'Otro subtítulo',
    'kicker' => 'Agendamiento',
    'show_whatsapp_btn' => true,
    'whatsapp_number' => '51936975214',
    'show_feature_cards' => true,
));
echo $scheduling->render();
Avance_Scheduling_Component::enqueue_assets();
?>
```

### Parámetros Disponibles
- `title` (string): Título principal
- `subtitle` (string): Subtítulo
- `kicker` (string): Etiqueta superior
- `show_whatsapp_btn` (bool): Mostrar botón WhatsApp
- `whatsapp_number` (string): Número de WhatsApp
- `show_feature_cards` (bool): Mostrar tarjetas de características
- `container_class` (string): Clase CSS del contenedor

---

## 2. Form Component (Formulario)

### Ubicación
- Clase: `includes/class-form-component.php`
- Template Part: `template-parts/form-section.php`

### Uso Básico
```php
<?php
$form = new Avance_Form_Component();
echo $form->render();
Avance_Form_Component::enqueue_assets();
?>
```

### Con Configuración Personalizada
```php
<?php
$form = new Avance_Form_Component(array(
    'label' => 'MI FORMULARIO',
    'title' => 'Contáctame',
    'description' => 'Completa los datos',
    'button_text' => 'Enviar',
    'whatsapp_text' => 'WhatsApp',
    'show_fields' => array(
        'nombre' => true,
        'email' => true,
        'numero' => false,  // Oculta este campo
        'asunto' => true,
        'mensaje' => true,
    ),
));
echo $form->render();
Avance_Form_Component::enqueue_assets();
?>
```

### Parámetros Disponibles
- `label` (string): Etiqueta del formulario
- `title` (string): Título principal
- `description` (string): Descripción
- `button_text` (string): Texto del botón enviar
- `whatsapp_text` (string): Texto del botón WhatsApp
- `show_fields` (array): Mostrar/ocultar campos específicos
  - `nombre` (bool)
  - `email` (bool)
  - `numero` (bool)
  - `asunto` (bool)
  - `mensaje` (bool)
- `container_class` (string): Clase CSS del contenedor

---

## 3. Template Parts Directos

Si prefieres usar template parts directamente sin clases:

### Scheduling
```php
<?php
get_template_part('template-parts/scheduling-section', null, array(
    'title' => 'Mi título',
    'show_feature_cards' => true,
));
?>
```

### Form
```php
<?php
get_template_part('template-parts/form-section', null, array(
    'title' => 'Formulario',
    'show_fields' => array('nombre' => true, 'email' => true),
));
?>
```

---

## 4. Estructura de Archivos

```
wp-content/themes/avance-template/
├── includes/
│   ├── class-scheduling-component.php    ← Lógica del componente
│   ├── class-form-component.php          ← Lógica del componente
│   └── ...otros archivos
├── template-parts/
│   ├── scheduling-section.php            ← HTML del componente
│   ├── form-section.php                  ← HTML del componente
│   └── ...otros template parts
├── templates/
│   ├── page-inicio.php                   ← Página que usa los componentes
│   └── ...otros templates
└── functions.php                         ← Incluye los componentes
```

---

## 5. Agregar Componentes a Nuevas Páginas

### En un template personalizado
```php
<?php
// En templates/page-contacto.php

get_header();
?>

<main class="site-main">
    
    <?php
    // Usar el componente Scheduling
    $scheduling = new Avance_Scheduling_Component(array(
        'title' => 'Título personalizado para esta página'
    ));
    echo $scheduling->render();
    Avance_Scheduling_Component::enqueue_assets();
    ?>

    <?php
    // Usar el componente Form
    $form = new Avance_Form_Component(array(
        'title' => 'Cuéntame tu desafío'
    ));
    echo $form->render();
    Avance_Form_Component::enqueue_assets();
    ?>

</main>

<?php get_footer(); ?>
```

### En un archivo functions de página específica
```php
<?php
// En includes/class-page-contacto.php

class Avance_Page_Contacto {
    
    public static function render() {
        $scheduling = new Avance_Scheduling_Component();
        return $scheduling->render();
    }
}
```

---

## 6. Personalización Avanzada

### Extender un componente
```php
<?php
class My_Custom_Scheduling extends Avance_Scheduling_Component {
    
    public function __construct($config = array()) {
        $config['title'] = 'Mi título por defecto';
        parent::__construct($config);
    }
    
    public function render() {
        // Personalización adicional
        return parent::render();
    }
}
?>
```

### Filtros (si se necesitan)
Puedes agregar filtros en las clases para mayor flexibilidad:
```php
// En class-scheduling-component.php
public function render() {
    $config = apply_filters('avance_scheduling_config', $this->config);
    // ... resto del código
}

// Uso en otro archivo
add_filter('avance_scheduling_config', function($config) {
    $config['title'] = 'Nuevo título';
    return $config;
});
```

---

## 7. Mejores Prácticas

✅ **Hacer**
- Usar los componentes en múltiples páginas
- Pasar configuración específica del sitio
- Enqueue assets solo cuando sea necesario
- Usar template parts para HTML

❌ **No hacer**
- Modificar HTML en las clases
- Hardcodear valores en las clases
- Enqueue assets globalmente sin necesidad

---

## 8. Soporte

Para agregar nuevos componentes:

1. Crear clase en `includes/class-[nombre]-component.php`
2. Crear template part en `templates/parts/[nombre]-section.php`
3. Incluir la clase en `functions.php`
4. Documentar en este archivo
