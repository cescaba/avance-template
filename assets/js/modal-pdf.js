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

	bannerLink.addEventListener('click', function(e) {
		e.preventDefault();
		openModal();
	});

	closeBtn.addEventListener('click', closeModal);

	modalOverlay.addEventListener('click', function(e) {
		if (e.target === modalOverlay) {
			closeModal();
		}
	});

	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && !modalOverlay.classList.contains('pf-overlay--hidden')) {
			closeModal();
		}
	});

	modalForm.addEventListener('submit', function(e) {
		e.preventDefault();

		const formData = new FormData(modalForm);
		const data = {
			email: formData.get('email'),
			nombre: formData.get('nombre'),
			telefono: formData.get('telefono'),
			empresa: formData.get('empresa'),
			empleados: formData.get('empleados'),
			industria: formData.get('industria')
		};

		console.log('Formulario enviado:', data);

		if (typeof avancePdfSubmit === 'function') {
			avancePdfSubmit(data);
		}

		closeModal();
		modalForm.reset();
	});
})();
