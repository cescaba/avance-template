class FAQAccordion {
	constructor() {
		this.questions = document.querySelectorAll('.mentoria-faq__question');
		this.init();
	}

	init() {
		this.questions.forEach(question => {
			question.addEventListener('click', (e) => this.toggle(e));
		});
	}

	toggle(event) {
		const question = event.currentTarget;
		const item = question.closest('.mentoria-faq__item');

		if (!item) return;

		item.classList.toggle('is-active');
	}
}

document.addEventListener('DOMContentLoaded', () => {
	new FAQAccordion();
});
