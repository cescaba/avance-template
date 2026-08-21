<?php

/**
 * Template Name: Plantilla Servicios Empresa
 * Template Post Type: page
 *
 * Página de servicios empresariales
 *
 * @package Avance_Template
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="servicio-main">
    <!-- Section 1: Intro Servicios -->
    <section class="servicio-intro" aria-label="Introducción de servicios">
        <div class="servicio-intro__container">
            <div class="servicio-intro__features">
                <div class="servicio-intro__img"></div>
                <div class="servicio-intro__content">
                    <h2 class="servicio-intro__title">Servicios para tu Empresa</h2>
                    <h3 class="servicio-intro__subtitle">Capacitación y consultoría para equipos comerciales</h3>
                    <p class="servicio-intro__description">Programas de formación ejecutiva, seminarios Top Class, consultoría comercial y educación especializada. Modalidad presencial, online o blended — diseñado a medida para tu empresa.</p>
                    <div class="servicio-intro__actions">
                        <button class="servicio-intro__btn servicio-intro__btn--primary">Solicitar propuesta</button>
                        <a href="https://wa.me" class="servicio-intro__btn servicio-intro__btn--whatsapp">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/wsp.svg" alt="WhatsApp" width="16" height="16">
                            Hablar por WhatsApp
                        </a>
                    </div>
                    <div class="servicio-intro__stats">
                        <div class="servicio-intro__stat">
                            <span class="servicio-intro__stat-number home-features__stat">+200</span>
                            <p class="servicio-intro__stat-label">Empresas</p>
                        </div>
                        <div class="servicio-intro__stat">
                            <span class="servicio-intro__stat-number home-features__stat">+15</span>
                            <p class="servicio-intro__stat-label">Años</p>
                        </div>
                        <div class="servicio-intro__stat">
                            <span class="servicio-intro__stat-number home-features__stat">100%</span>
                            <p class="servicio-intro__stat-label">Personalizado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Nuestros Servicios -->
    <section class="servicio-offers" aria-label="Oferta de servicios">
        <div class="servicio-offers__container">
            <header class="servicio-offers__header animate-on-scroll" data-animate>
                <p class="servicio-offers__label">SERVICIOS TOP CLASS</p>
                <h2 class="servicio-offers__title">Programas intensivos de alto impacto</h2>
                <p class="servicio-offers__description">Formato compacto (4–6 horas), metodología experiencial y contenido aplicable al día siguiente.</p>
            </header>
            <div class="servicio-offers__grid">
                <div class="servicio-offers__item animate-on-scroll" data-animate>
                    <div class="servicio-offers__item-header">
                        <span class="servicio-offers__item-duration">6 Horas</span>
                        <span class="servicio-offers__item-level">TOP CLASS</span>
                    </div>
                    <h3 class="servicio-offers__item-title">Coaching en Ventas</h3>
                    <p class="servicio-offers__item-text">Programa intensivo que entrena al equipo comercial en técnicas de venta consultiva, manejo de objeciones y cierre de negocios en entornos B2B. Metodología experiencial con casos reales del sector.</p>
                    <div class="servicio-offers__tags">
                        <span class="servicio-offers__tag">Equipos de ventas</span>
                        <span class="servicio-offers__tag">B2B</span>
                        <span class="servicio-offers__tag">Cierre</span>
                    </div>
                </div>
                <div class="servicio-offers__item animate-on-scroll" data-animate>
                    <div class="servicio-offers__item-header">
                        <span class="servicio-offers__item-duration">4 Horas</span>
                        <span class="servicio-offers__item-level">TOP CLASS</span>
                    </div>
                    <h3 class="servicio-offers__item-title">Story Selling</h3>
                    <p class="servicio-offers__item-text">Aprende a vender con historias: cómo estructurar narrativas comerciales que conecten emocionalmente con el cliente, generen confianza y aceleren la decisión de compra.</p>
                    <div class="servicio-offers__tags">
                        <span class="servicio-offers__tag">Narrativa comercial</span>
                        <span class="servicio-offers__tag">Persuasión</span>
                        <span class="servicio-offers__tag">Presentaciones</span>
                    </div>
                </div>
                <div class="servicio-offers__item animate-on-scroll" data-animate>
                    <div class="servicio-offers__item-header">
                        <span class="servicio-offers__item-duration">6 Horas</span>
                        <span class="servicio-offers__item-level">TOP CLASS</span>
                    </div>
                    <h3 class="servicio-offers__item-title">Gestión Estratégica de Equipos Online</h3>
                    <p class="servicio-offers__item-text">Herramientas y metodologías para liderar equipos distribuidos con alto rendimiento. Foco en comunicación asertiva, KPIs de gestión remota y cultura de resultados.</p>
                    <div class="servicio-offers__tags">
                        <span class="servicio-offers__tag">Liderazgo remoto</span>
                        <span class="servicio-offers__tag">KPIs</span>
                        <span class="servicio-offers__tag">Equipos</span>
                    </div>
                </div>
                <div class="servicio-offers__item animate-on-scroll" data-animate>
                    <div class="servicio-offers__item-header">
                        <span class="servicio-offers__item-duration">6 Horas</span>
                        <span class="servicio-offers__item-level">TOP CLASS</span>
                    </div>
                    <h3 class="servicio-offers__item-title">Técnicas para Presentaciones de Ventas</h3>
                    <p class="servicio-offers__item-text">Desarrolla habilidades para presentar propuestas comerciales de alto impacto en entornos virtuales. Diseño de decks, manejo de la cámara, lenguaje corporal digital y cierre efectivo.</p>
                    <div class="servicio-offers__tags">
                        <span class="servicio-offers__tag">Presentaciones</span>
                        <span class="servicio-offers__tag">Virtual</span>
                        <span class="servicio-offers__tag">Propuestas</span>
                    </div>
                </div>
            </div>
            <div class="servicio-offers__cta animate-on-scroll" data-animate>
                <div class="servicio-offers__cta-content">
                    <h3 class="servicio-offers__cta-title">¿Quieres un seminario in-company para tu equipo?</h3>
                    <p class="servicio-offers__cta-description">Adaptamos el contenido a tu sector, reto y nivel del equipo. Modalidad presencial, online o blended.</p>
                </div>
                <div class="servicio-offers__cta-actions">
                    <button class="servicio-offers__cta-btn servicio-offers__cta-btn--primary">Solicitar cotización</button>
                    <a href="https://wa.me" class="servicio-offers__cta-btn servicio-offers__cta-btn--whatsapp">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/wsp.svg" alt="WhatsApp" width="16" height="16">
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Proceso de Trabajo -->
    <section class="servicio-process" aria-label="Proceso de trabajo">
        <div class="servicio-process__container">
            <h2 class="servicio-process__title animate-on-scroll" data-animate>Cómo Trabajamos</h2>
            <div class="servicio-process__steps">
                <div class="servicio-process__step animate-on-scroll" data-animate>
                    <span class="servicio-process__step-number">01</span>
                    <h3 class="servicio-process__step-title">Diagnóstico inicial</h3>
                    <p class="servicio-process__step-description">Reunión sin costo para entender tu situación y definir el alcance.</p>
                </div>
                <div class="servicio-process__step animate-on-scroll" data-animate>
                    <span class="servicio-process__step-number">02</span>
                    <h3 class="servicio-process__step-title">Propuesta a medida</h3>
                    <p class="servicio-process__step-description">Diseñamos un programa específico con objetivos, plazos y métricas.</p>
                </div>
                <div class="servicio-process__step animate-on-scroll" data-animate>
                    <span class="servicio-process__step-number">03</span>
                    <h3 class="servicio-process__step-title">Implementación</h3>
                    <p class="servicio-process__step-description">Ejecutamos el programa con seguimiento semanal y ajustes en tiempo real.</p>
                </div>
                <div class="servicio-process__step animate-on-scroll" data-animate>
                    <span class="servicio-process__step-number">04</span>
                    <h3 class="servicio-process__step-title">Medición</h3>
                    <p class="servicio-process__step-description">Entregamos informe de resultados y recomendaciones de continuidad.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Solicitar Propuesta -->
    <section class="servicio-contact" aria-label="Solicitar propuesta personalizada">
        <div class="servicio-contact__container">
            <header class="servicio-contact__header animate-on-scroll" data-animate>
                <p class="servicio-contact__label">SOLICITAR PROPUESTA</p>
                <h2 class="servicio-contact__title">Cuéntanos sobre tu empresa</h2>
                <p class="servicio-contact__description">Completa el formulario y te enviamos una propuesta personalizada en menos de 48h. Sin compromiso.</p>
            </header>
            <div class="servicio-contact__content">
                <form id="proposal-form" class="servicio-contact__form">
                    <?php wp_nonce_field('avance_proposal_form', 'nonce'); ?>

                    <div class="servicio-contact__form-row animate-on-scroll" data-animate>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">Nombre *</label>
                            <input type="text" name="nombre" class="servicio-contact__input" placeholder="Tu nombre" required>
                        </div>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">Cargo</label>
                            <input type="text" name="cargo" class="servicio-contact__input" placeholder="Gerente / CEO / Director">
                        </div>
                    </div>
                    <div class="servicio-contact__form-row animate-on-scroll" data-animate>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">Empresa *</label>
                            <input type="text" name="empresa" class="servicio-contact__input" placeholder="Nombre de tu empresa" required>
                        </div>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">Tamaño del equipo</label>
                            <input type="text" name="tamaño_equipo" class="servicio-contact__input" placeholder="Número de colaboradores">
                        </div>
                    </div>
                    <div class="servicio-contact__form-row animate-on-scroll" data-animate>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">Email *</label>
                            <input type="email" name="email" class="servicio-contact__input" placeholder="tu@empresa.com" required>
                        </div>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">WhatsApp</label>
                            <input type="tel" name="whatsapp" class="servicio-contact__input" placeholder="+51 999 000 000">
                        </div>
                    </div>
                    <div class="servicio-contact__form-row servicio-contact__form-row--full animate-on-scroll" data-animate>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">Servicio de interés *</label>
                            <input type="text" name="servicio_interes" class="servicio-contact__input" placeholder="Selecciona el servicio" required>
                        </div>
                    </div>
                    <div class="servicio-contact__form-row servicio-contact__form-row--full animate-on-scroll" data-animate>
                        <div class="servicio-contact__form-group">
                            <label class="servicio-contact__label-text">¿Cuál es tu principal desafío comercial? *</label>
                            <textarea name="desafio_comercial" class="servicio-contact__textarea" placeholder="Describe brevemente la situación de tu empresa..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="servicio-contact__btn">Solicitar propuesta personalizada</button>
                    <p class="servicio-contact__footnote">* Propuesta enviada en menos de 48h. Sin compromiso. informacion@avance-empresarial.com</p>
                </form>
                <div class="servicio-contact__benefits">
                    <div class="servicio-contact__benefits-list">
                        <h3 class="servicio-contact__benefits-title">¿POR QUÉ ELEGIRNOS?</h3>
                        <div class="servicio-contact__benefit animate-on-scroll" data-animate>
                            <span class="servicio-contact__benefit-number">01</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">Experiencia real</h4>
                                <p class="servicio-contact__benefit-text">+15 años trabajando con empresas peruanas de distintos sectores.</p>
                            </div>
                        </div>
                        <div class="servicio-contact__benefit animate-on-scroll" data-animate>
                            <span class="servicio-contact__benefit-number">02</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">No solo teoría</h4>
                                <p class="servicio-contact__benefit-text">Nos involucramos en la implementación y medimos los resultados contigo.</p>
                            </div>
                        </div>
                        <div class="servicio-contact__benefit animate-on-scroll" data-animate>
                            <span class="servicio-contact__benefit-number">03</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">Metodología probada</h4>
                                <p class="servicio-contact__benefit-text">Framework B2B Management aplicado en +200 empresas.</p>
                            </div>
                        </div>
                        <div class="servicio-contact__benefit animate-on-scroll" data-animate>
                            <span class="servicio-contact__benefit-number">04</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">Flexibilidad total</h4>
                                <p class="servicio-contact__benefit-text">100% online, blended o presencial. Nos adaptamos a tu equipo y agenda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="servicio-contact__direct animate-on-scroll" data-animate>
                        <div class="servicio-contact__direct-info">
                            <p class="servicio-contact__direct-title">Contacto directo</p>
                            <p class="servicio-contact__direct-text">+51 991 908 301 · informacion@avance-empresarial.com</p>
                        </div>
                        <a href="https://wa.me" class="servicio-contact__direct-btn">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/wsp.svg" alt="WhatsApp" width="16" height="16">
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
