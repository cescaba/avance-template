/**
 * Admin Interface - Maneja interacciones de los admins
 */

document.addEventListener('DOMContentLoaded', function() {
	const container = document.querySelector('.admin-container');
	if (!container) return;

	const nonce = document.querySelector('.admin-nonce')?.value;
	const tableName = document.querySelector('.admin-table-name')?.value;

	if (!nonce || !tableName) return;

	// Modal para ver detalles
	const modal = createModal();
	document.body.appendChild(modal);

	// Botones Ver
	document.querySelectorAll('.admin-btn-view').forEach(btn => {
		btn.addEventListener('click', () => viewRecord(btn.dataset.id, nonce, tableName, modal));
	});

	// Botones Descargar (individual)
	document.querySelectorAll('.admin-btn-download').forEach(btn => {
		btn.addEventListener('click', () => downloadRecord(btn.dataset.id, nonce, tableName));
	});

	// Botones Eliminar
	document.querySelectorAll('.admin-btn-delete').forEach(btn => {
		btn.addEventListener('click', () => deleteRecord(btn.dataset.id, nonce, tableName));
	});

	// Botón Descargar Todo
	document.querySelector('.admin-btn-download-all')?.addEventListener('click', () => {
		downloadAll(nonce, tableName);
	});

	// Búsqueda
	const searchInput = document.querySelector('.admin-search-input');
	if (searchInput) {
		searchInput.addEventListener('input', (e) => filterTable(e.target.value));
	}
});

function createModal() {
	const modal = document.createElement('div');
	modal.className = 'admin-modal';
	modal.innerHTML = `
		<div class="admin-modal-backdrop"></div>
		<div class="admin-modal-content">
			<div class="admin-modal-header">
				<h2>Detalles del Registro</h2>
				<button class="admin-modal-close" aria-label="Cerrar">&times;</button>
			</div>
			<div class="admin-modal-body"></div>
			<div class="admin-modal-footer">
				<button class="admin-modal-btn-close">Cerrar</button>
			</div>
		</div>
	`;

	const backdrop = modal.querySelector('.admin-modal-backdrop');
	const closeBtn = modal.querySelector('.admin-modal-close');
	const closeFooterBtn = modal.querySelector('.admin-modal-btn-close');

	const closeModal = () => modal.classList.remove('active');

	backdrop.addEventListener('click', closeModal);
	closeBtn.addEventListener('click', closeModal);
	closeFooterBtn.addEventListener('click', closeModal);

	return modal;
}

function viewRecord(id, nonce, tableName, modal) {
	const action = tableName.replace(/(wp_avance_|_)/g, (match) => {
		if (match === '_') return '_';
		return match === 'wp_avance_' ? '' : match;
	});

	const ajaxAction = getAjaxAction(tableName, 'get_record');

	fetch(ajaxurl, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: new URLSearchParams({
			action: ajaxAction,
			id: id,
			nonce: nonce
		})
	})
	.then(res => res.json())
	.then(data => {
		if (data.success) {
			modal.querySelector('.admin-modal-body').innerHTML = data.data.html;
			modal.classList.add('active');
		} else {
			alert(data.data?.message || 'Error al obtener el registro');
		}
	})
	.catch(err => {
		console.error('Error:', err);
		alert('Error al obtener el registro');
	});
}

function downloadRecord(id, nonce, tableName) {
	const ajaxAction = getAjaxAction(tableName, 'download_record');

	const form = document.createElement('form');
	form.method = 'POST';
	form.action = ajaxurl;
	form.style.display = 'none';

	form.innerHTML = `
		<input type="hidden" name="action" value="${ajaxAction}">
		<input type="hidden" name="id" value="${id}">
		<input type="hidden" name="nonce" value="${nonce}">
	`;

	document.body.appendChild(form);
	form.submit();
	document.body.removeChild(form);
}

function downloadAll(nonce, tableName) {
	const ajaxAction = getAjaxAction(tableName, 'download_all');

	const form = document.createElement('form');
	form.method = 'POST';
	form.action = ajaxurl;
	form.style.display = 'none';

	form.innerHTML = `
		<input type="hidden" name="action" value="${ajaxAction}">
		<input type="hidden" name="nonce" value="${nonce}">
	`;

	document.body.appendChild(form);
	form.submit();
	document.body.removeChild(form);
}

function deleteRecord(id, nonce, tableName) {
	if (!confirm('¿Estás seguro de que deseas eliminar este registro?')) {
		return;
	}

	const ajaxAction = getAjaxAction(tableName, 'delete_record');

	fetch(ajaxurl, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: new URLSearchParams({
			action: ajaxAction,
			id: id,
			nonce: nonce
		})
	})
	.then(res => res.json())
	.then(data => {
		if (data.success) {
			alert(data.data.message || 'Registro eliminado correctamente');
			location.reload();
		} else {
			alert(data.data?.message || 'Error al eliminar');
		}
	})
	.catch(err => {
		console.error('Error:', err);
		alert('Error al eliminar el registro');
	});
}

function filterTable(query) {
	const rows = document.querySelectorAll('.admin-row');
	let visibleCount = 0;

	rows.forEach(row => {
		const text = row.textContent.toLowerCase();
		const matches = text.includes(query.toLowerCase());
		row.style.display = matches ? '' : 'none';
		if (matches) visibleCount++;
	});

	const resultsSpan = document.querySelector('.admin-search-results');
	if (resultsSpan) {
		if (query) {
			resultsSpan.textContent = `${visibleCount} resultado(s)`;
		} else {
			resultsSpan.textContent = '';
		}
	}
}

function getAjaxAction(tableName, action) {
	const actions = {
		'wp_avance_contacts': 'contacts',
		'wp_avance_proposals': 'proposals',
		'wp_avance_agendamiento_contacto': 'agendamientos',
		'wp_avance_diagnosticos': 'diagnosticos'
	};

	const prefix = actions[tableName] || 'admin';
	return `${prefix}_${action}`;
}
