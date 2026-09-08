/**
 * Unified Select Component
 * Reemplaza: form-section-select.js, scheduling-select.js, proposal-select.js
 * Generic select handler para todos los .avance-select-wrapper
 */

class AvanceSelect {
	constructor(triggerId, dropdownId, hiddenSelectId) {
		this.trigger = document.getElementById(triggerId);
		this.dropdown = document.getElementById(dropdownId);
		this.hiddenSelect = document.getElementById(hiddenSelectId);
		this.options = this.dropdown ? this.dropdown.querySelectorAll('.avance-select-option') : [];
		this.value = this.hiddenSelect?.value || '';

		if (this.trigger && this.dropdown) {
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
			if (!e.target.closest('.avance-select-wrapper')) {
				this.closeDropdown();
			}
		});

		// Keyboard navigation
		this.trigger.addEventListener('keydown', (e) => this.handleKeyboard(e));

		// Resetear cuando el formulario se resetea
		const form = this.hiddenSelect.closest('form');
		if (form) {
			form.addEventListener('reset', () => this.reset());
		}
	}

	toggleDropdown() {
		if (this.dropdown.classList.contains('is-open')) {
			this.closeDropdown();
		} else {
			this.openDropdown();
		}
	}

	openDropdown() {
		this.dropdown.classList.add('is-open');
		this.trigger.classList.add('is-open');
	}

	closeDropdown() {
		this.dropdown.classList.remove('is-open');
		this.trigger.classList.remove('is-open');
	}

	selectOption(option) {
		const value = option.dataset.value;
		const text = option.textContent;

		// Update hidden select
		this.hiddenSelect.value = value;
		this.value = value;

		// Update trigger display
		const valueSpan = this.trigger.querySelector('.avance-select-value');
		if (value === '') {
			valueSpan.textContent = text;
			this.trigger.classList.remove('has-value');
		} else {
			valueSpan.textContent = text;
			this.trigger.classList.add('has-value');
		}

		// Update selected state
		this.options.forEach(opt => {
			opt.classList.remove('is-selected');
			if (opt.dataset.value === value) {
				opt.classList.add('is-selected');
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
		} else if (e.key === 'ArrowDown' && this.dropdown.classList.contains('is-open')) {
			e.preventDefault();
			this.focusNextOption();
		} else if (e.key === 'ArrowUp' && this.dropdown.classList.contains('is-open')) {
			e.preventDefault();
			this.focusPrevOption();
		}
	}

	focusNextOption() {
		const selected = this.dropdown.querySelector('.avance-select-option.is-selected');
		const index = Array.from(this.options).indexOf(selected || this.options[0]);
		const next = this.options[index + 1];
		if (next) {
			this.selectOption(next);
		}
	}

	focusPrevOption() {
		const selected = this.dropdown.querySelector('.avance-select-option.is-selected');
		const index = Array.from(this.options).indexOf(selected);
		const prev = this.options[index - 1];
		if (prev) {
			this.selectOption(prev);
		}
	}

	reset() {
		const valueSpan = this.trigger.querySelector('.avance-select-value');
		if (valueSpan) {
			valueSpan.textContent = valueSpan.dataset.placeholder || 'Selecciona una opción';
		}
		this.trigger.classList.remove('has-value');
		this.hiddenSelect.value = '';
		this.value = '';
		this.options.forEach(opt => opt.classList.remove('is-selected'));
	}

	// Alias methods para compatibilidad con nombres viejos
	static FormSectionSelect(triggerId, dropdownId, hiddenSelectId) {
		return new AvanceSelect(triggerId, dropdownId, hiddenSelectId);
	}

	static SchedulingSelect(triggerId, dropdownId, hiddenSelectId) {
		return new AvanceSelect(triggerId, dropdownId, hiddenSelectId);
	}

	static ProposalSelect(triggerId, dropdownId, hiddenSelectId) {
		return new AvanceSelect(triggerId, dropdownId, hiddenSelectId);
	}
}

// Initialize selects when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
	// Scheduling select (scheduling-section.php)
	if (document.getElementById('scheduling-select-trigger')) {
		new AvanceSelect('scheduling-select-trigger', 'scheduling-select-dropdown', 'contacto-agenda-topic');
	}

	// Form section select (form-section.php)
	if (document.getElementById('form-section-select-trigger')) {
		new AvanceSelect('form-section-select-trigger', 'form-section-select-dropdown', 'contacto_wsp_asunto');
	}

	// Proposal select (page-servicio-empresa.php)
	if (document.getElementById('proposal-select-trigger')) {
		new AvanceSelect('proposal-select-trigger', 'proposal-select-dropdown', 'proposal-servicio');
	}
});
