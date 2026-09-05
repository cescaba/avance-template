const DIAGNOSTICO_QUESTIONS = [
	{
		text: "¿Cuál es tu mayor desafío comercial ahora mismo?",
		options: [
			"No genero suficientes leads",
			"Bajo porcentaje de cierre",
			"No tengo proceso comercial definido",
			"Equipo de ventas poco efectivo"
		]
	},
	{
		text: "¿Cuántos vendedores tiene tu equipo actualmente?",
		options: [
			"Solo yo",
			"2 - 5 personas",
			"6 - 15 personas",
			"Más de 15 personas"
		]
	},
	{
		text: "¿Tienes definida tu propuesta de valor?",
		options: [
			"Sí , muy claro",
			"Más o menos",
			"No , es difusa",
			"No sé qué es eso"
		]
	},
	{
		text: "¿Cuánto inviertes en formación comercial?",
		options: [
			"Nada",
			"Menos de S/ 500/mes",
			"S/ 500 – 2.000/mes",
			"Más de S/ 2.000/mes"
		]
	},
	{
		text: "Tu diagnóstico está listo. ¿A dónde lo enviamos?",
		isForm: true,
		formFields: [
			{ name: "nombreCompleto", label: "Nombre Completo*", type: "text", placeholder: "Ej. Juan García Rodríguez" },
			{ name: "email", label: "Email*", type: "email", placeholder: "Ej. juan@empresa.com" },
			{ name: "whatsapp", label: "WhatsApp*", type: "tel", placeholder: "Ej. +51 987 654 321" }
		]
	}
];

const DIAGNOSTICO_STORAGE_KEY = 'diagnostico_quiz_state';

let diagnosticoCurrentIndex = 0;
let diagnosticoAnswers = [];

let diagnosticoProgressFill;
let diagnosticoQuestionTag;
let diagnosticoQuestionText;
let diagnosticoOptionsList;
let diagnosticoQuizView;

function saveDiagnosticoState() {
	const state = {
		currentIndex: diagnosticoCurrentIndex,
		answers: diagnosticoAnswers
	};
	localStorage.setItem(DIAGNOSTICO_STORAGE_KEY, JSON.stringify(state));
}

function clearDiagnosticoState() {
	localStorage.removeItem(DIAGNOSTICO_STORAGE_KEY);
	diagnosticoCurrentIndex = 0;
	diagnosticoAnswers = [];
}

function initDiagnosticoElements() {
	diagnosticoProgressFill = document.getElementById('diagnosticoProgressFill');
	diagnosticoQuestionTag = document.getElementById('diagnosticoQuestionTag');
	diagnosticoQuestionText = document.getElementById('diagnosticoQuestionText');
	diagnosticoOptionsList = document.getElementById('diagnosticoOptionsList');
	diagnosticoQuizView = document.getElementById('diagnosticoQuizView');
}

function renderDiagnosticoQuestion() {
	const q = DIAGNOSTICO_QUESTIONS[diagnosticoCurrentIndex];
	diagnosticoQuestionTag.textContent = `PREGUNTA ${diagnosticoCurrentIndex + 1} DE ${DIAGNOSTICO_QUESTIONS.length}`;
	diagnosticoQuestionText.textContent = q.text;
	diagnosticoOptionsList.innerHTML = '';

	if (q.isForm) {
		renderDiagnosticoForm(q);
	} else {
		q.options.forEach((label) => {
			const btn = document.createElement('button');
			btn.className = 'diagnostico-quiz__option-btn animate-on-scroll is-visible';
			btn.setAttribute('data-animate', '');
			btn.setAttribute('data-answer', label);
			btn.textContent = label;
			btn.addEventListener('click', () => selectDiagnosticoOption(label));
			diagnosticoOptionsList.appendChild(btn);
		});
	}

	updateDiagnosticoProgress(diagnosticoCurrentIndex + 1);
}

function renderDiagnosticoForm(q) {
	const form = document.createElement('form');
	form.className = 'diagnostico-quiz__form';

	const nonceInput = document.createElement('input');
	nonceInput.type = 'hidden';
	nonceInput.name = 'nonce';
	nonceInput.value = typeof avanceDiagnosticoConfig !== 'undefined' ? avanceDiagnosticoConfig.nonce : '';
	form.appendChild(nonceInput);

	q.formFields.forEach(field => {
		const fieldDiv = document.createElement('div');
		fieldDiv.className = 'diagnostico-quiz__form-field';

		const label = document.createElement('label');
		label.textContent = field.label;

		const input = document.createElement('input');
		input.type = field.type;
		input.name = field.name;
		input.placeholder = field.placeholder;
		input.className = 'diagnostico-quiz__form-input';
		input.required = true;

		fieldDiv.appendChild(label);
		fieldDiv.appendChild(input);
		form.appendChild(fieldDiv);
	});

	const submitBtn = document.createElement('button');
	submitBtn.type = 'submit';
	submitBtn.className = 'diagnostico-quiz__form-submit animate-on-scroll is-visible';
	submitBtn.setAttribute('data-animate', '');
	submitBtn.textContent = 'Recibir diagnostico';

	form.appendChild(submitBtn);

	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const formData = new FormData(form);
		const data = Object.fromEntries(formData);

		// Validar campos
		if (!data.nombreCompleto || data.nombreCompleto.trim().length < 3) {
			showDiagnosticoNotification('El nombre debe tener mínimo 3 caracteres', 'error');
			return;
		}

		if (!data.email || !isValidEmail(data.email)) {
			showDiagnosticoNotification('Email inválido', 'error');
			return;
		}

		if (!data.whatsapp || data.whatsapp.trim().length < 9) {
			showDiagnosticoNotification('WhatsApp debe tener mínimo 9 dígitos', 'error');
			return;
		}

		submitBtn.disabled = true;
		submitBtn.style.opacity = '0.7';
		submitBtn.style.cursor = 'default';

		const submissionData = {
			nombreCompleto: data.nombreCompleto,
			email: data.email,
			whatsapp: data.whatsapp,
			respuestas: diagnosticoAnswers.slice(0, 4),
		};

		if (typeof avanceDiagnosticoSubmit === 'function') {
			avanceDiagnosticoSubmit(submissionData, submitBtn);
		}

		setTimeout(() => {
			resetDiagnosticoOnSuccess();
		}, 5000);
	});

	diagnosticoOptionsList.appendChild(form);
}

function updateDiagnosticoProgress(answeredCount) {
	const pct = (answeredCount / DIAGNOSTICO_QUESTIONS.length) * 100;
	diagnosticoProgressFill.style.width = pct + '%';
}

function selectDiagnosticoOption(answerText) {
	diagnosticoAnswers.push(answerText); // Guardar el texto de la respuesta
	saveDiagnosticoState();

	if (diagnosticoCurrentIndex >= DIAGNOSTICO_QUESTIONS.length - 1) {
		updateDiagnosticoProgress(DIAGNOSTICO_QUESTIONS.length);
	} else {
		diagnosticoCurrentIndex++;
		renderDiagnosticoQuestion();
	}
}

function resetDiagnosticoOnSuccess() {
	clearDiagnosticoState();
	renderDiagnosticoQuestion();
}

function isValidEmail(email) {
	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return emailRegex.test(email);
}

function showDiagnosticoNotification(message, type = 'error') {
	const quizTitle = document.getElementById('diagnosticoQuestionText');
	if (!quizTitle || typeof NotificationManager === 'undefined') {
		alert(message);
		return;
	}

	if (type === 'error') {
		NotificationManager.error(message, quizTitle);
	} else {
		NotificationManager.success(message, quizTitle);
	}
}

document.addEventListener('DOMContentLoaded', () => {
	initDiagnosticoElements();
	renderDiagnosticoQuestion();
	if (diagnosticoOptionsList) {
		const buttons = diagnosticoOptionsList.querySelectorAll('[data-answer]');
		buttons.forEach(btn => {
			const newBtn = btn.cloneNode(true);
			btn.parentNode.replaceChild(newBtn, btn);
			const answerText = newBtn.getAttribute('data-answer');
			newBtn.addEventListener('click', () => selectDiagnosticoOption(answerText));
		});
	}
});
