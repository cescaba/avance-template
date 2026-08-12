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
		text: "¿Cuántos leads recibes al mes aproximadamente?",
		options: [
			"Menos de 20",
			"Entre 20 y 100",
			"Entre 100 y 500",
			"Más de 500"
		]
	},
	{
		text: "¿Tienes un proceso de ventas documentado?",
		options: [
			"Sí, totalmente definido",
			"Parcialmente definido",
			"No, cada vendedor hace lo suyo",
			"No tengo equipo de ventas"
		]
	},
	{
		text: "¿Qué herramienta usas para gestionar tus clientes (CRM)?",
		options: [
			"Ninguna, uso hojas de cálculo",
			"Un CRM básico",
			"Un CRM avanzado (HubSpot, Salesforce, etc.)",
			"No lo sé / no estoy seguro"
		]
	},
	{
		text: "¿Cuál es tu objetivo principal en los próximos 3 meses?",
		options: [
			"Aumentar leads",
			"Mejorar el cierre de ventas",
			"Optimizar procesos internos",
			"Escalar el equipo de ventas"
		]
	}
];

let diagnosticoCurrentIndex = 0;
const diagnosticoAnswers = [];

const diagnosticoProgressFill = document.getElementById('diagnosticoProgressFill');
const diagnosticoQuestionTag = document.getElementById('diagnosticoQuestionTag');
const diagnosticoQuestionText = document.getElementById('diagnosticoQuestionText');
const diagnosticoOptionsList = document.getElementById('diagnosticoOptionsList');
const diagnosticoQuizView = document.getElementById('diagnosticoQuizView');
const diagnosticoResultView = document.getElementById('diagnosticoResultView');

function renderDiagnosticoQuestion() {
	const q = DIAGNOSTICO_QUESTIONS[diagnosticoCurrentIndex];
	diagnosticoQuestionTag.textContent = `PREGUNTA ${diagnosticoCurrentIndex + 1} DE ${DIAGNOSTICO_QUESTIONS.length}`;
	diagnosticoQuestionText.textContent = q.text;
	diagnosticoOptionsList.innerHTML = '';

	q.options.forEach((label, i) => {
		const btn = document.createElement('button');
		btn.className = 'diagnostico-quiz__option-btn animate-on-scroll';
		btn.setAttribute('data-animate', '');
		btn.textContent = label;
		btn.addEventListener('click', () => selectDiagnosticoOption(i));
		diagnosticoOptionsList.appendChild(btn);
	});

	updateDiagnosticoProgress(diagnosticoCurrentIndex);
}

function updateDiagnosticoProgress(answeredCount) {
	const pct = (answeredCount / DIAGNOSTICO_QUESTIONS.length) * 100;
	diagnosticoProgressFill.style.width = pct + '%';
}

function selectDiagnosticoOption(optionIndex) {
	diagnosticoAnswers.push(optionIndex);
	if (diagnosticoCurrentIndex >= DIAGNOSTICO_QUESTIONS.length - 1) {
		updateDiagnosticoProgress(DIAGNOSTICO_QUESTIONS.length);
		showDiagnosticoResult();
	} else {
		diagnosticoCurrentIndex++;
		renderDiagnosticoQuestion();
	}
}

function showDiagnosticoResult() {
	diagnosticoQuizView.classList.add('hidden');
	diagnosticoResultView.classList.remove('hidden');
}

function restartDiagnosticoQuiz() {
	diagnosticoCurrentIndex = 0;
	diagnosticoAnswers.length = 0;
	diagnosticoResultView.classList.add('hidden');
	diagnosticoQuizView.classList.remove('hidden');
	renderDiagnosticoQuestion();
}

document.getElementById('diagnosticoRestart').addEventListener('click', restartDiagnosticoQuiz);
document.getElementById('diagnosticoCta').addEventListener('click', restartDiagnosticoQuiz);

renderDiagnosticoQuestion();
