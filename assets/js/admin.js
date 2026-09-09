/**
 * Admin System - Maneja todas las acciones de admin
 * @package Avance_Template
 */

(function() {
	'use strict';

	const Admin = {
		init() {
			this.cacheElements();
			this.attachEventListeners();
		},

		cacheElements() {
			this.nonce = document.querySelector('.admin-nonce')?.value;
			this.actionPrefix = document.querySelector('.admin-action-prefix')?.value || 'contacts';
			this.ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php';
			this.viewBtns = document.querySelectorAll('.admin-btn-view');
			this.deleteBtns = document.querySelectorAll('.admin-btn-delete');
			this.downloadBtns = document.querySelectorAll('.admin-btn-download');
			this.downloadAllBtn = document.querySelector('.admin-btn-download-all');
			this.modal = document.getElementById('admin-modal') || this.createModal();
		},

		attachEventListeners() {
			this.viewBtns.forEach(btn => {
				btn.addEventListener('click', () => this.handleView(btn));
			});

			this.deleteBtns.forEach(btn => {
				btn.addEventListener('click', () => this.handleDelete(btn));
			});

			this.downloadBtns.forEach(btn => {
				btn.addEventListener('click', () => this.handleDownload(btn));
			});

			if (this.downloadAllBtn) {
				this.downloadAllBtn.addEventListener('click', () => this.handleDownloadAll());
			}

			document.addEventListener('click', (e) => {
				if (e.target.classList.contains('admin-modal-close')) {
					this.closeModal();
				}
				if (e.target === this.modal) {
					this.closeModal();
				}
			});
		},

		handleView(btn) {
			const id = btn.getAttribute('data-id');
			const actionName = this.getActionName('get_record');

			fetch(this.ajaxUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: actionName,
					id: id,
					nonce: this.nonce,
				}),
			})
				.then(r => r.json())
				.then(data => {
					if (data.success) {
						this.showModal(data.data.html);
					} else {
						alert(data.data.message || 'Error al cargar registro');
					}
				})
				.catch(err => alert('Error de conexión'));
		},

		handleDelete(btn) {
			if (!confirm('¿Estás seguro de que deseas eliminar este registro?')) {
				return;
			}

			const id = btn.getAttribute('data-id');
			const actionName = this.getActionName('delete_record');
			const row = btn.closest('tr');

			fetch(ajaxurl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: actionName,
					id: id,
					nonce: this.nonce,
				}),
			})
				.then(r => r.json())
				.then(data => {
					if (data.success) {
						row.remove();
						this.showNotification('Registro eliminado correctamente', 'success');
					} else {
						alert(data.data.message || 'Error al eliminar');
					}
				})
				.catch(err => alert('Error de conexión'));
		},

		handleDownload(btn) {
			const id = btn.getAttribute('data-id');
			const actionName = this.getActionName('download_record');

			fetch(ajaxurl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: actionName,
					id: id,
					nonce: this.nonce,
				}),
			})
				.then(r => r.blob())
				.then(blob => {
					const url = window.URL.createObjectURL(blob);
					const a = document.createElement('a');
					a.href = url;
					a.download = `registro_${id}.csv`;
					document.body.appendChild(a);
					a.click();
					window.URL.revokeObjectURL(url);
					a.remove();
				})
				.catch(err => alert('Error al descargar'));
		},

		handleDownloadAll() {
			const actionName = this.getActionName('download_all');

			fetch(ajaxurl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
					action: actionName,
					nonce: this.nonce,
				}),
			})
				.then(r => r.blob())
				.then(blob => {
					const url = window.URL.createObjectURL(blob);
					const a = document.createElement('a');
					a.href = url;
					a.download = `registros_${new Date().getTime()}.csv`;
					document.body.appendChild(a);
					a.click();
					window.URL.revokeObjectURL(url);
					a.remove();
				})
				.catch(err => alert('Error al descargar'));
		},

		getActionName(action) {
			return `${this.actionPrefix}_${action}`;
		},

		createModal() {
			const modal = document.createElement('div');
			modal.id = 'admin-modal';
			modal.className = 'admin-modal';
			modal.innerHTML = `
				<div class="admin-modal-content">
					<div class="admin-modal-header-wrapper">
						<button class="admin-modal-close" type="button" aria-label="Cerrar">&times;</button>
					</div>
					<div class="admin-modal-body"></div>
				</div>
			`;
			document.body.appendChild(modal);
			return modal;
		},

		showModal(html) {
			const body = this.modal.querySelector('.admin-modal-body');
			body.innerHTML = html;
			this.modal.classList.add('active');
		},

		closeModal() {
			this.modal.classList.remove('active');
		},

		showNotification(message, type = 'info') {
			const notification = document.createElement('div');
			notification.className = `admin-notification admin-notification-${type}`;
			notification.textContent = message;
			document.body.appendChild(notification);

			setTimeout(() => {
				notification.remove();
			}, 3000);
		},
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => {
			Admin.init();
		});
	} else {
		Admin.init();
	}
})();
