(function() {
	'use strict';

	const modalOverlay = document.getElementById('pf-modal-overlay');
	const modalForm = document.getElementById('pf-modal-form');
	const closeBtn = document.getElementById('pf-close-btn');
	const bannerLink = document.querySelector('.avance-banner__link');

	if (!modalOverlay || !bannerLink) {
		return;
	}

	function openModal() {
		modalOverlay.classList.remove('pf-overlay--hidden');
		document.body.style.overflow = 'hidden';
		modalOverlay.setAttribute('aria-hidden', 'false');
	}

	function closeModal() {
		modalOverlay.classList.add('pf-overlay--hidden');
		document.body.style.overflow = '';
		modalOverlay.setAttribute('aria-hidden', 'true');
	}

	bannerLink.addEventListener('click', (e) => {
		e.preventDefault();
		openModal();
	});

	closeBtn.addEventListener('click', closeModal);

	modalOverlay.addEventListener('click', (e) => {
		if (e.target === modalOverlay) {
			closeModal();
		}
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && !modalOverlay.classList.contains('pf-overlay--hidden')) {
			closeModal();
		}
	});

	modalForm.addEventListener('submit', (e) => {
		e.preventDefault();

		if (!modalForm.checkValidity()) {
			modalForm.reportValidity();
			return;
		}

		const submitBtn = modalForm.querySelector('button[type="submit"]');
		const originalText = submitBtn.innerHTML;
		submitBtn.disabled = true;
		submitBtn.innerHTML = '<span>Descargando...</span>';

		const formData = new FormData(modalForm);
		const data = {
			action: 'avance_submit_pdf_download',
			nonce: formData.get('nonce') || '',
			email: formData.get('email'),
			nombre: formData.get('nombre'),
			telefono: formData.get('telefono'),
			empresa: formData.get('empresa'),
			empleados: formData.get('empleados'),
			industria: formData.get('industria')
		};

		fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams(data)
		})
		.then(response => response.json())
		.then(result => {
			submitBtn.disabled = false;
			submitBtn.innerHTML = originalText;

			if (result.success) {
				NotificationManager.success('PDF descargando...', document.getElementById('pf-notifications'));
				setTimeout(() => {
					window.location.href = result.data.download_url;
					closeModal();
					modalForm.reset();
				}, 300);
			} else {
				const errors = result.data?.errors || {};
				const errorMessage = Object.values(errors).join('\n') || result.data?.message || 'Error al descargar el PDF';
				NotificationManager.error(errorMessage, document.getElementById('pf-notifications'));
			}
		})
		.catch(() => {
			submitBtn.disabled = false;
			submitBtn.innerHTML = originalText;
			NotificationManager.error('Error al procesar la solicitud', document.getElementById('pf-notifications'));
		});
	});
})();
