# Estructura Universal de Admin

## Archivos Base

- `admin-template.html` - Estructura HTML reutilizable
- `../../../assets/css/admin-universal.css` - Estilos profesionales

## Uso

### 1. Crear nueva clase de Admin

```php
class Avance_Mi_Admin {
    public function render_page() {
        // Obtener datos de BD
        $data = $wpdb->get_results("SELECT * FROM tabla");
        $total = $wpdb->get_var("SELECT COUNT(*) FROM tabla");
        
        // Configurar columnas
        $columns = [
            ['field' => 'id', 'label' => 'ID'],
            ['field' => 'nombre', 'label' => 'Nombre'],
            ['field' => 'email', 'label' => 'Email']
        ];
        
        // Renderizar
        $this->render_admin($columns, $data, $total);
    }
    
    private function render_admin($columns, $data, $total) {
        $nonce = wp_create_nonce('admin_nonce');
        ?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/admin-universal.css">
        
        <div class="admin-container">
            <!-- Copiar estructura de admin-template.html y reemplazar placeholders -->
            <div class="admin-header">
                <div class="admin-header-content">
                    <h1 class="admin-title">Mi Admin</h1>
                    <p class="admin-subtitle">Gestiona tus datos</p>
                </div>
                <div class="admin-stats">
                    <div class="admin-stat">
                        <div class="admin-stat-icon">📊</div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-label">Total</div>
                            <div class="admin-stat-value"><?php echo $total; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla dinámica -->
            <div class="admin-search-section">
                <input class="admin-search-input" type="search" placeholder="Buscar...">
            </div>
            
            <div class="admin-table-section">
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <th><?php echo esc_html($col['label']); ?></th>
                                <?php endforeach; ?>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="<?php echo count($columns) + 1; ?>" class="admin-empty">
                                        <div class="admin-empty-state">
                                            <div class="admin-empty-icon">📭</div>
                                            <div class="admin-empty-title">Sin registros</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data as $row): ?>
                                    <tr class="admin-row">
                                        <?php foreach ($columns as $col): ?>
                                            <td><?php echo esc_html($row->{$col['field']} ?? '—'); ?></td>
                                        <?php endforeach; ?>
                                        <td class="admin-col-actions">
                                            <button class="admin-btn admin-btn-view" data-id="<?php echo esc_attr($row->id); ?>">👁️</button>
                                            <button class="admin-btn admin-btn-download" data-id="<?php echo esc_attr($row->id); ?>">⬇️</button>
                                            <button class="admin-btn admin-btn-delete" data-id="<?php echo esc_attr($row->id); ?>">🗑️</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <input type="hidden" class="admin-nonce" value="<?php echo esc_attr($nonce); ?>">
        </div>
        
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/admin.js"></script>
        <?php
    }
}
```

## Características

✅ **Responsive** - Se adapta a móvil y desktop
✅ **Dark Mode** - Soporta tema oscuro automático
✅ **Dinámico** - Las columnas se generan según configuración
✅ **Profesional** - Diseño limpio y moderno
✅ **Accesible** - Labels y ARIA attributes
✅ **Minimalista** - Sin dependencias complejas

## CSS Classes Disponibles

- `.admin-container` - Contenedor principal
- `.admin-header` - Sección de encabezado
- `.admin-stat` - Tarjeta de estadística
- `.admin-table` - Tabla de datos
- `.admin-btn` - Botón estándar
- `.admin-btn-view` - Botón ver
- `.admin-btn-delete` - Botón eliminar (rojo)
- `.admin-pagination` - Controles de paginación
- `.admin-empty-state` - Mensaje sin datos

## Placeholders Disponibles

En `admin-template.html`:

- `{TITLE}` - Título del admin
- `{SUBTITLE}` - Subtítulo
- `{STATS_ITEMS}` - Tarjetas de estadísticas
- `{TABLE_HEADERS}` - Encabezados de tabla
- `{TABLE_ROWS}` - Filas de datos
- `{PAGINATION_INFO}` - Info de paginación
- `{NONCE}` - Token de seguridad
