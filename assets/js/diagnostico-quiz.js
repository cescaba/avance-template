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
			{ name: "nombreCompleto", label: "Nombre Completo*", type: "text", placeholder: "Tu nombre completo" },
			{ name: "email", label: "Email*", type: "email", placeholder: "tu@email.com" },
			{ name: "whatsapp", label: "WhatsApp*", type: "tel", placeholder: "+51 XXX XXX XXX" }
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

function loadDiagnosticoState() {
	// Siempre comenzar desde la primera pregunta
	clearDiagnosticoState();
	return false;
}

function clearDiagnosticoState() {
	localStorage.removeItem(DIAGNOSTICO_STORAGE_KEY);
	localStorage.removeItem(DIAGNOSTICO_STORAGE_KEY); // Doble limpieza para asegurar
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
		q.options.forEach((label, i) => {
			const btn = document.createElement('button');
			btn.className = 'diagnostico-quiz__option-btn animate-on-scroll is-visible';
			btn.setAttribute('data-animate', '');
			btn.textContent = label;
			btn.addEventListener('click', () => selectDiagnosticoOption(label)); // Guardar texto, no índice
			diagnosticoOptionsList.appendChild(btn);
		});
	}

	updateDiagnosticoProgress(diagnosticoCurrentIndex + 1);
}

function renderDiagnosticoForm(q) {
	const form = document.createElement('form');
	form.className = 'diagnostico-quiz__form';
	form.style.cssText = 'display: flex; flex-direction: column; gap: 12px;';

	q.formFields.forEach(field => {
		const fieldDiv = document.createElement('div');
		fieldDiv.className = 'diagnostico-quiz__form-field';
		fieldDiv.style.cssText = 'display: flex; flex-direction: column; gap: 6px;';

		const label = document.createElement('label');
		label.textContent = field.label;
		label.style.cssText = 'font-size: 13px; font-weight: 500; color: #364153;';

		const input = document.createElement('input');
		input.type = field.type;
		input.name = field.name;
		input.placeholder = field.placeholder;
		input.className = 'diagnostico-quiz__form-input';
		input.style.cssText = 'padding: 10px 12px; font-size: 13px; border: 1px solid #E5E7EB; border-radius: 6px; font-family: inherit;';
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
	submitBtn.style.cssText = 'margin-top: 12px;';

	form.appendChild(submitBtn);

	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const formData = new FormData(form);
		const data = Object.fromEntries(formData);

		submitBtn.disabled = true;
		submitBtn.style.opacity = '0.7';
		submitBtn.style.cursor = 'default';

		// Preparar datos para envío
		const submissionData = {
			nombreCompleto: data.nombreCompleto,
			email: data.email,
			whatsapp: data.whatsapp,
			respuestas: diagnosticoAnswers.slice(0, 4), // Solo las 4 primeras preguntas
		};

		// Llamar función global si existe
		if (typeof avanceDiagnosticoSubmit === 'function') {
			avanceDiagnosticoSubmit(submissionData, submitBtn);
		}

		// Reiniciar después de que se complete todo (solo si fue exitoso)
		// El estado se limpiará desde diagnostico-submit.js cuando sea exitoso
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

function restartDiagnosticoQuiz(clearHistory = true) {
	if (clearHistory) {
		clearDiagnosticoState();
	}
	renderDiagnosticoQuestion();
}

function resetDiagnosticoOnSuccess() {
	clearDiagnosticoState();
	renderDiagnosticoQuestion();
}

document.addEventListener('DOMContentLoaded', () => {
	initDiagnosticoElements();
	if (diagnosticoOptionsList) {
		// Restaurar estado guardado si existe
		const hasState = loadDiagnosticoState();
		renderDiagnosticoQuestion();
	}
});
