document.addEventListener('DOMContentLoaded', () => {
  initAnimations();
});

function initAnimations() {
  if (!('IntersectionObserver' in window)) {
    document.documentElement.classList.add('no-intersection-observer');
    return;
  }

  const options = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const element = entry.target;

        if (element.classList.contains('animate-on-scroll')) {
          element.classList.add('is-visible');
          observer.unobserve(element);
        }

        if (element.classList.contains('counter')) {
          animateCounter(element);
          observer.unobserve(element);
        }
      }
    });
  }, options);

  document.querySelectorAll('[data-animate]').forEach(element => {
    observer.observe(element);
  });
}

function animateCounter(element) {
  const finalValue = parseInt(element.dataset.value, 10);
  const duration = 2000;
  const steps = 60;
  const stepDuration = duration / steps;
  let currentStep = 0;

  const interval = setInterval(() => {
    currentStep++;
    const progress = currentStep / steps;
    const easeOut = 1 - Math.pow(1 - progress, 3);
    const currentValue = Math.floor(finalValue * easeOut);

    element.textContent = currentValue + (element.dataset.suffix || '');

    if (currentStep >= steps) {
      element.textContent = finalValue + (element.dataset.suffix || '');
      clearInterval(interval);
    }
  }, stepDuration);
}
