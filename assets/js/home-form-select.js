/**
 * Home Form Custom Select Component
 */

class HomeFormSelect {
	constructor(triggerId, menuId, hiddenSelectId) {
		this.trigger = document.getElementById(triggerId);
		this.menu = document.getElementById(menuId);
		this.hiddenSelect = document.getElementById(hiddenSelectId);
		this.options = this.menu ? this.menu.querySelectorAll('.home-form__select-option') : [];
		this.value = this.hiddenSelect?.value || '';

		if (this.trigger && this.menu) {
			this.init();
		}
	}

	init() {
		// Trigger click
		this.trigger.addEventListener('click', (e) => {
			e.stopPropagation();
			this.toggleDropdown();
		});

		// Option clicks
		this.options.forEach(option => {
			option.addEventListener('click', (e) => {
				e.stopPropagation();
				this.selectOption(option);
			});
		});

		// Close on outside click
		document.addEventListener('click', (e) => {
			if (!e.target.closest('.home-form__select-wrapper')) {
				this.closeDropdown();
			}
		});

		// Keyboard navigation
		this.trigger.addEventListener('keydown', (e) => this.handleKeyboard(e));
	}

	toggleDropdown() {
		if (this.menu.classList.contains('open')) {
			this.closeDropdown();
		} else {
			this.openDropdown();
		}
	}

	openDropdown() {
		this.menu.classList.add('open');
		this.trigger.classList.add('active');
	}

	closeDropdown() {
		this.menu.classList.remove('open');
		this.trigger.classList.remove('active');
	}

	selectOption(option) {
		const value = option.dataset.value;
		const text = option.textContent;

		// Update hidden select
		this.hiddenSelect.value = value;
		this.value = value;

		// Update trigger display
		const valueSpan = this.trigger.querySelector('.home-form__select-value');
		if (valueSpan) {
			valueSpan.textContent = text;
		}

		// Update selected state
		this.options.forEach(opt => {
			opt.classList.remove('selected');
			if (opt.dataset.value === value) {
				opt.classList.add('selected');
			}
		});

		// Close dropdown
		this.closeDropdown();

		// Trigger change event
		const event = new Event('change', { bubbles: true });
		this.hiddenSelect.dispatchEvent(event);
	}

	handleKeyboard(e) {
		if (e.key === 'Enter' || e.key === ' ') {
			e.preventDefault();
			this.toggleDropdown();
		} else if (e.key === 'Escape') {
			this.closeDropdown();
		} else if (e.key === 'ArrowDown' && this.menu.classList.contains('open')) {
			e.preventDefault();
			this.focusNextOption();
		} else if (e.key === 'ArrowUp' && this.menu.classList.contains('open')) {
			e.preventDefault();
			this.focusPrevOption();
		}
	}

	focusNextOption() {
		const selected = this.menu.querySelector('.home-form__select-option.selected');
		const index = Array.from(this.options).indexOf(selected || this.options[0]);
		const next = this.options[index + 1];
		if (next) {
			this.selectOption(next);
		}
	}

	focusPrevOption() {
		const selected = this.menu.querySelector('.home-form__select-option.selected');
		const index = Array.from(this.options).indexOf(selected);
		const prev = this.options[index - 1];
		if (prev) {
			this.selectOption(prev);
		}
	}
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
	new HomeFormSelect('home-form-select-trigger', 'home-form-select-menu', 'contacto_wsp_asunto');
});
