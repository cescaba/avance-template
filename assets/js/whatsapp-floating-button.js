(function() {
  'use strict';

  function init() {
    const wspBtn = document.querySelector('.wsp-floating-btn');
    const wspModal = document.getElementById('wspModal');
    const wspMessage = document.getElementById('wspMessage');
    const wspSendBtn = document.getElementById('wspSendBtn');
    const wspCloseBtn = document.querySelector('.wsp-modal-close');
    const wspOverlay = document.querySelector('.wsp-modal-overlay');

    if (!wspBtn || !wspModal || !wspMessage) {
      return;
    }

    function openModal() {
      wspModal.hidden = false;
      wspModal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      document.body.classList.add('wsp-modal-open');
      setTimeout(() => {
        wspMessage.focus();
      }, 100);
    }

    function closeModal() {
      wspModal.hidden = true;
      wspModal.style.display = 'none';
      document.body.style.overflow = '';
      document.body.classList.remove('wsp-modal-open');
      wspMessage.value = '';
    }

    function sendToWhatsApp() {
      const message = wspMessage.value.trim();

      if (!message) {
        wspMessage.focus();
        return;
      }

      const phone = typeof wspConfig !== 'undefined' ? wspConfig.phone : '51993508652';
      const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

      window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
      closeModal();
    }

    wspBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openModal();
    });

    if (wspCloseBtn) {
      wspCloseBtn.addEventListener('click', closeModal);
    }

    if (wspOverlay) {
      wspOverlay.addEventListener('click', closeModal);
    }

    wspSendBtn.addEventListener('click', sendToWhatsApp);

    wspMessage.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' && e.ctrlKey) {
        e.preventDefault();
        sendToWhatsApp();
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !wspModal.hidden) {
        closeModal();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
