<?php

/**
 * Template Name: Plantilla Mentorías
 * Template Post Type: page
 *
 * Página de mentorías personalizadas
 *
 * @package Avance_Template
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="mentoria-main">
    <!-- Section 1: Mentoría 1:1 -->
    <section class="mentoria-intro" aria-label="Mentoría 1:1">
        <div class="mentoria-intro__container">
            <div class="mentoria-intro__features">
                <div class="mentoria-intro__mentoria">
                    <h2 class="mentoria-intro__title">Mentoría 1:1</h2>
                    <h3 class="mentoria-intro__subtitle">Acelera tu crecimiento con acompañamiento real</h3>
                    <p class="mentoria-intro__description">Trabajo contigo de forma personalizada para desbloquear los obstáculos que frenan tu desarrollo profesional y los resultados de tu empresa. Sin teoría: sesiones prácticas, acción y seguimiento.</p>
                    <ul class="mentoria-intro__list">
                        <li class="mentoria-intro__list-item">Sesiones 1:1 enfocadas en tus desafíos reales</li>
                        <li class="mentoria-intro__list-item">Plan de acción concreto y medible</li>
                        <li class="mentoria-intro__list-item">Acceso directo por WhatsApp entre sesiones</li>
                        <li class="mentoria-intro__list-item">Revisión de métricas y ajuste continuo</li>
                    </ul>
                </div>
                <div class="mentoria-intro__img"></div>
            </div>
        </div>
    </section>

    <!-- Section 2: ¿Para quién es? -->
    <section class="mentoria-target" aria-label="Público objetivo">
        <div class="mentoria-target__container">
            <header class="mentoria-target__header animate-on-scroll" data-animate>
                <h2 class="mentoria-target__title">¿Para quién es?</h2>
            </header>
            <div class="mentoria-target__list">
                <div class="mentoria-target__item animate-on-scroll" data-animate>
                    <h3 class="mentoria-target__item-title">Fundadores y CEOs</h3>
                    <p class="mentoria-target__item-text">Que quieren escalar sin perder el foco en lo que importa.</p>
                </div>
                <div class="mentoria-target__item animate-on-scroll" data-animate>
                    <h3 class="mentoria-target__item-title">Gerentes comerciales</h3>
                    <p class="mentoria-target__item-text">En transición o buscando llevar a su equipo al siguiente nivel.</p>
                </div>
                <div class="mentoria-target__item animate-on-scroll" data-animate>
                    <h3 class="mentoria-target__item-title">Ejecutivos senior</h3>
                    <p class="mentoria-target__item-text">Que quieren acelerar su carrera con apoyo estratégico.</p>
                </div>
                <div class="mentoria-target__item animate-on-scroll" data-animate>
                    <h3 class="mentoria-target__item-title">Emprendedores con tracción</h3>
                    <p class="mentoria-target__item-text">Que ya tienen mercado pero necesitan sistema y estructura.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: ¿Qué hacemos juntos? -->
    <section class="mentoria-together" aria-label="Pilares de trabajo">
        <div class="mentoria-together__container">
            <div class="mentoria-together__features">
                <div class="mentoria-together__img"></div>
                <div class="mentoria-together__content">
                    <header class="mentoria-together__header animate-on-scroll" data-animate>
                        <h2 class="mentoria-together__title">Qué trabajamos juntos</h2>
                    </header>
                    <ol class="mentoria-together__list">
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Claridad de visión y objetivos comerciales</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Construcción de propuesta de valor personal</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Gestión del equipo y delegación efectiva</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Toma de decisiones bajo incertidumbre</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Desarrollo de habilidades de negociación</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Gestión del tiempo y foco estratégico</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Marca personal y posicionamiento profesional</li>
                        <li class="mentoria-together__list-item animate-on-scroll" data-animate>Revisión y ajuste del modelo de negocio</li> 
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Preguntas Frecuentes -->
    <section class="mentoria-faq" aria-label="Preguntas frecuentes">
        <div class="mentoria-faq__container">
            <header class="mentoria-faq__header animate-on-scroll" data-animate>
                <h2 class="mentoria-faq__title">Preguntas Frecuentes</h2>
            </header>
            <div class="mentoria-faq__list">
                <div class="mentoria-faq__item animate-on-scroll" data-animate>
                    <button class="mentoria-faq__question">
                        <span class="mentoria-faq__text">¿Puedo cancelar en cualquier momento?</span>
                        <svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <p class="mentoria-faq__answer">Sí, con 30 días de anticipación puedes cancelar tu mentoría</p>
                </div>
                <div class="mentoria-faq__item animate-on-scroll" data-animate>
                    <button class="mentoria-faq__question">
                        <span class="mentoria-faq__text">¿En qué horarios están disponibles las sesiones?</span>
                        <svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <p class="mentoria-faq__answer">Flexibles. Adaptamos los horarios a tu disponibilidad</p>
                </div>
                <div class="mentoria-faq__item animate-on-scroll" data-animate>
                    <button class="mentoria-faq__question">
                        <span class="mentoria-faq__text">¿Las sesiones son presenciales u online?</span>
                        <svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <p class="mentoria-faq__answer">Ambas opciones disponibles según tu preferencia</p>
                </div>
                <div class="mentoria-faq__item animate-on-scroll" data-animate>
                    <button class="mentoria-faq__question">
                        <span class="mentoria-faq__text">¿Qué pasa si no puedo asistir a una sesión?</span>
                        <svg class="mentoria-faq__toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <p class="mentoria-faq__answer">Podemos reprogramar con 48 horas de anticipación</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Reserva tu Mentoría -->
    <section class="mentoria-reservations" id="mentoria-reservations" aria-label="Reserva tu mentoría">
        <?php include get_template_directory() . '/template-parts/reservations/reservation-widget.php'; ?>
    </section>
</main>

<?php
get_footer();
