// Sincronizar con datos de PHP (window.adminData)
console.log('adminData:', window.adminData);

const SCHEMA = (window.adminData?.columns || []).map(col => ({
  key: col.field || col.key,
  label: col.label,
  table: true,
  ...col
}));

let DATA = window.adminData?.data || [];
let FILTERED_DATA = DATA;
let SEARCH_QUERY = '';

// Fallback: si no hay datos, usar ejemplos
if (!DATA || DATA.length === 0) {
  DATA = [
    { id: 1, nombre: 'Test User', email: 'test@example.com', numero: '1234567890', asunto: 'Test', created_at: new Date().toISOString() }
  ];
  FILTERED_DATA = DATA;
}

console.log('SCHEMA:', SCHEMA);
console.log('DATA:', DATA);

// Filtrar datos por búsqueda
function filterData(query) {
  SEARCH_QUERY = query.toLowerCase().trim();

  if (!SEARCH_QUERY) {
    FILTERED_DATA = DATA;
  } else {
    FILTERED_DATA = DATA.filter(row => {
      // Buscar en todos los campos del registro
      return SCHEMA.some(col => {
        const value = String(row[col.key] || '').toLowerCase();
        return value.includes(SEARCH_QUERY);
      });
    });
  }

  renderTable();
}

// Actualizar estadísticas
function updateStats() {
  const statTotal = document.querySelector('.stat-number#stat-total');
  const statDenegado = document.querySelector('.stat-number#stat-denegado');
  const statProceso = document.querySelector('.stat-number#stat-proceso');

  if (statTotal) statTotal.textContent = DATA.length;

  // Contar denegados (buscar en campo 'estado')
  if (statDenegado) {
    const countDenegado = DATA.filter(r => r.estado === 'Denegado' || r.estado === 'denegado').length;
    statDenegado.textContent = countDenegado;
  }

  // Contar en proceso (buscar 'pendiente' o 'proceso')
  if (statProceso) {
    const countProceso = DATA.filter(r => r.estado === 'Pendiente' || r.estado === 'pendiente' || r.estado === 'En proceso').length;
    statProceso.textContent = countProceso;
  }
}

// Renderizar tabla
function renderTable() {
  const thead = document.getElementById('admin-thead');
  const tbody = document.getElementById('admin-tbody');
  const emptyEl = document.getElementById('admin-empty');

  if (!thead || !tbody) return;

  // Actualizar estadísticas
  updateStats();

  // Renderizar encabezados
  if (SCHEMA.length > 0) {
    thead.innerHTML = '<tr>' + SCHEMA.map(col => `<th>${col.label}</th>`).join('') + '<th>Acciones</th></tr>';
  }

  // Renderizar filas
  if (!FILTERED_DATA || FILTERED_DATA.length === 0) {
    tbody.innerHTML = '';
    if (emptyEl) emptyEl.hidden = false;
  } else {
    tbody.innerHTML = FILTERED_DATA.map(row => {
      const cells = SCHEMA.map(col => {
        const value = row[col.key] || '—';
        return `<td>${String(value)}</td>`;
      }).join('');

      const actions = `
        <td class="table-actions">
          <button class="btn-action btn-view" onclick="window.verRegistro(${row.id})">Ver</button>
          <button class="btn-action btn-download" onclick="window.descargarRegistro(${row.id})">Descargar</button>
          <button class="btn-action btn-delete" onclick="window.eliminarRegistro(${row.id})">Eliminar</button>
        </td>
      `;

      return `<tr>${cells}${actions}</tr>`;
    }).join('');
    if (emptyEl) emptyEl.hidden = true;
  }
}

// Funciones de acciones - Globales para onclick
window.verRegistro = function(id) {
  const modalBody = document.getElementById('modal-body');
  if (!modalBody) {
    console.error('Modal body no encontrado');
    return;
  }

  // Intentar usar AJAX si está configurado
  if (window.adminConfig && window.adminConfig.actions && window.adminConfig.actions.get_record) {
    const params = new URLSearchParams({
      action: window.adminConfig.actions.get_record,
      id: id,
      nonce: window.adminConfig.nonce
    });

    fetch(window.adminConfig.ajaxUrl, { method: 'POST', body: params })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          modalBody.innerHTML = json.data.html || json.data;
          const modal = document.getElementById('modal-overlay');
          if (modal) {
            modal.removeAttribute('hidden');
            console.log('Modal abierto con AJAX');
          }
        } else {
          console.error('Error AJAX:', json.data);
          alert('Error: ' + (json.data?.message || 'No se pudo obtener el registro'));
        }
      })
      .catch(err => {
        console.error('Error en AJAX:', err);
        alert('Error en la solicitud');
      });
  } else {
    // Fallback: mostrar datos locales
    const registro = DATA.find(r => String(r.id) === String(id));
    if (!registro) {
      console.error('Registro no encontrado:', id);
      return;
    }

    let html = '';
    SCHEMA.forEach((col) => {
      const value = registro[col.key];
      const isEmpty = !value || value === '—';

      // Hacer campos largos (mensaje, asunto) de ancho completo
      const isFullWidth = col.key === 'mensaje' || col.key === 'asunto' || col.key === 'respuestas';

      html += `
        <div class="modal-field${isFullWidth ? ' full-width' : ''}">
          <div class="modal-label">${col.label}</div>
          <div class="modal-value${isEmpty ? ' empty' : ''}">
            ${isEmpty ? '—' : String(value)}
          </div>
        </div>
      `;
    });

    modalBody.innerHTML = html;

    const modal = document.getElementById('modal-overlay');
    if (modal) {
      modal.removeAttribute('hidden');
      console.log('Modal abierto localmente');
    }
  }
};

window.closeModal = function() {
  const modal = document.getElementById('modal-overlay');
  if (modal) {
    modal.setAttribute('hidden', '');
    console.log('Modal cerrado');
  }
};

window.descargarRegistro = function(id) {
  const registro = DATA.find(r => String(r.id) === String(id));
  if (registro) {
    const csv = SCHEMA.map(col => '"' + (registro[col.key] || '') + '"').join(',');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'registro-' + id + '.csv';
    link.click();
    console.log('Descargar registro:', id);
  }
};

window.eliminarRegistro = function(id) {
  if (confirm('¿Estás seguro de que quieres eliminar este registro?')) {
    DATA = DATA.filter(r => String(r.id) !== String(id));
    renderTable();
    console.log('Eliminado registro:', id);
  }
};

window.descargarTodo = function() {
  if (!DATA || DATA.length === 0) {
    alert('No hay datos para descargar');
    return;
  }

  const headers = SCHEMA.map(col => '"' + col.label + '"').join(',');
  const rows = DATA.map(row => SCHEMA.map(col => '"' + (row[col.key] || '') + '"').join(',')).join('\r\n');
  const csv = headers + '\r\n' + rows;

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'registros-completo.csv';
  link.click();
  console.log('Descargar todo');
};

// Event listener para botón Descargar todo
const btnDescargarTodo = document.getElementById('btn-download-all');
if (btnDescargarTodo) {
  btnDescargarTodo.addEventListener('click', window.descargarTodo);
}

// Event listener para buscador
const searchInput = document.getElementById('search-input');
if (searchInput) {
  searchInput.addEventListener('input', (e) => {
    filterData(e.target.value);
  });
}

// Cerrar modal al hacer clic fuera del contenido
document.addEventListener('click', (e) => {
  const modal = document.getElementById('modal-overlay');
  if (modal && e.target === modal) {
    window.closeModal();
  }
});

// Cerrar modal con tecla Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    window.closeModal();
  }
});

// Renderizar al cargar
document.addEventListener('DOMContentLoaded', () => {
  renderTable();
});

// Si el DOM ya está listo
if (document.readyState !== 'loading') {
  renderTable();
}
