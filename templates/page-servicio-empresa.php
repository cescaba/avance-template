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
                        <a href="#servicio-contact" class="servicio-intro__btn servicio-intro__btn--primary">Solicitar propuesta</a>
                        <a href="<?php echo esc_url(AVANCE_WHATSAPP_URL); ?>" class="servicio-intro__btn servicio-intro__btn--whatsapp" <?php echo AVANCE_WHATSAPP_ATTRS; ?>>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/wsp.svg" alt="Icono de WhatsApp" width="16" height="16">
                            Hablar por WhatsApp
                        </a>
                    </div>
                    <div class="servicio-intro__stats">
                        <div class="servicio-intro__stat">
                            <span class="servicio-intro__stat-number">+200</span>
                            <p class="servicio-intro__stat-label">Empresas</p>
                        </div>
                        <div class="servicio-intro__stat">
                            <span class="servicio-intro__stat-number">+15</span>
                            <p class="servicio-intro__stat-label">Años</p>
                        </div>
                        <div class="servicio-intro__stat">
                            <span class="servicio-intro__stat-number">100%</span>
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
            <header class="servicio-offers__header">
                <p class="servicio-offers__label">SERVICIOS TOP CLASS</p>
                <h2 class="servicio-offers__title">Programas intensivos de alto impacto</h2>
                <p class="servicio-offers__description">Seminarios diseñados para equipos que necesitan resultados rápidos. Formato compacto (4–6 horas), metodología experiencial y contenido directamente aplicable al día siguiente.</p>
            </header>
            <div class="servicio-offers__grid">
                <article class="servicio-offers__item">
                    <div class="servicio-offers__item-header">
                         <h2 class="servicio-offers__header-title">Ventas Consultivas 4.0: De Vendedor a Socio Estratégico del Cliente</h2>
                        <span class="servicio-offers__item-duration">TOP CLASS</span>
                    </div>
                    <div class="servicio-offers__item-description">
                        <h3 class="servicio-offers__item-title">JUSTIFICACIÓN</h3>
                        <p class="servicio-offers__item-text">En un entorno donde el cliente está más informado y exige valor real, la venta transaccional pierde vigencia. Este curso permite evolucionar hacia un modelo consultivo basado en insights, empatía y co-creación de soluciones.</p>
                    </div>
                    <div class="servicio-offers__tags">
                        <h3 class="servicio-offers__item-title">TEMARIO</h3>
                        <div class="servicio-offers__justification-list">
                            <div class="servicio-offers__justification-item">De vender productos a diseñar soluciones de negocio</div>
                            <div class="servicio-offers__justification-item">El arte de preguntar: cómo descubrir necesidades ocultas del cliente</div>
                            <div class="servicio-offers__justification-item">Neuroventas y toma de decisiones del cliente B2B</div>
                            <div class="servicio-offers__justification-item">Cierre estratégico: influir sin presionar</div>
                        </div>
                    </div>
                </article>
                <article class="servicio-offers__item">
                    <div class="servicio-offers__item-header">
                         <h2 class="servicio-offers__header-title">Ventas Consultivas 4.0: De Vendedor a Socio Estratégico del Cliente</h2>
                        <span class="servicio-offers__item-duration">TOP CLASS</span>
                    </div>
                    <div class="servicio-offers__item-description">
                        <h3 class="servicio-offers__item-title">JUSTIFICACIÓN</h3>
                        <p class="servicio-offers__item-text">En un entorno donde el cliente está más informado y exige valor real, la venta transaccional pierde vigencia. Este curso permite evolucionar hacia un modelo consultivo basado en insights, empatía y co-creación de soluciones.</p>
                    </div>
                    <div class="servicio-offers__tags">
                        <h3 class="servicio-offers__item-title">TEMARIO</h3>
                        <div class="servicio-offers__justification-list">
                            <div class="servicio-offers__justification-item">De vender productos a diseñar soluciones de negocio</div>
                            <div class="servicio-offers__justification-item">El arte de preguntar: cómo descubrir necesidades ocultas del cliente</div>
                            <div class="servicio-offers__justification-item">Neuroventas y toma de decisiones del cliente B2B</div>
                            <div class="servicio-offers__justification-item">Cierre estratégico: influir sin presionar</div>
                        </div>
                    </div>
                </article>
                <article class="servicio-offers__item">
                    <div class="servicio-offers__item-header">
                         <h2 class="servicio-offers__header-title">Ventas Consultivas 4.0: De Vendedor a Socio Estratégico del Cliente</h2>
                        <span class="servicio-offers__item-duration">TOP CLASS</span>
                    </div>
                    <div class="servicio-offers__item-description">
                        <h3 class="servicio-offers__item-title">JUSTIFICACIÓN</h3>
                        <p class="servicio-offers__item-text">En un entorno donde el cliente está más informado y exige valor real, la venta transaccional pierde vigencia. Este curso permite evolucionar hacia un modelo consultivo basado en insights, empatía y co-creación de soluciones.</p>
                    </div>
                    <div class="servicio-offers__tags">
                        <h3 class="servicio-offers__item-title">TEMARIO</h3>
                        <div class="servicio-offers__justification-list">
                            <div class="servicio-offers__justification-item">De vender productos a diseñar soluciones de negocio</div>
                            <div class="servicio-offers__justification-item">El arte de preguntar: cómo descubrir necesidades ocultas del cliente</div>
                            <div class="servicio-offers__justification-item">Neuroventas y toma de decisiones del cliente B2B</div>
                            <div class="servicio-offers__justification-item">Cierre estratégico: influir sin presionar</div>
                        </div>
                    </div>
                </article>
                <article class="servicio-offers__item">
                    <div class="servicio-offers__item-header">
                         <h2 class="servicio-offers__header-title">Ventas Consultivas 4.0: De Vendedor a Socio Estratégico del Cliente</h2>
                        <span class="servicio-offers__item-duration">TOP CLASS</span>
                    </div>
                    <div class="servicio-offers__item-description">
                        <h3 class="servicio-offers__item-title">JUSTIFICACIÓN</h3>
                        <p class="servicio-offers__item-text">En un entorno donde el cliente está más informado y exige valor real, la venta transaccional pierde vigencia. Este curso permite evolucionar hacia un modelo consultivo basado en insights, empatía y co-creación de soluciones.</p>
                    </div>
                    <div class="servicio-offers__tags">
                        <h3 class="servicio-offers__item-title">TEMARIO</h3>
                        <div class="servicio-offers__justification-list">
                            <div class="servicio-offers__justification-item">De vender productos a diseñar soluciones de negocio</div>
                            <div class="servicio-offers__justification-item">El arte de preguntar: cómo descubrir necesidades ocultas del cliente</div>
                            <div class="servicio-offers__justification-item">Neuroventas y toma de decisiones del cliente B2B</div>
                            <div class="servicio-offers__justification-item">Cierre estratégico: influir sin presionar</div>
                        </div>
                    </div>
                </article>
            </div>
            <section class="servicio-offers__cta" aria-label="Solicitar seminario in-company">
                <div class="servicio-offers__cta-content">
                    <h3 class="servicio-offers__cta-title">¿Quieres un seminario in-company para tu equipo?</h3>
                    <p class="servicio-offers__cta-description">Adaptamos el contenido a tu sector y nivel.</p>
                </div>
                <div class="servicio-offers__cta-actions">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('contacto')) . '#home-form'); ?>" class="servicio-offers__cta-btn--primary">Solicitar cotización</a>
                </div>
            </section>
        </div>
    </section>

    <!-- Section 3: Proceso de Trabajo -->
    <section class="servicio-process" aria-label="Proceso de trabajo">
        <div class="servicio-process__container">
            <h2 class="servicio-process__title">Cómo Trabajamos</h2>
            <div class="servicio-process__steps">
                <div class="servicio-process__step">
                    <span class="servicio-process__step-number">01</span>
                    <h3 class="servicio-process__step-title">Diagnóstico inicial</h3>
                    <p class="servicio-process__step-description">Reunión sin costo para entender tu situación y definir el alcance.</p>
                </div>
                <div class="servicio-process__step">
                    <span class="servicio-process__step-number">02</span>
                    <h3 class="servicio-process__step-title">Propuesta a medida</h3>
                    <p class="servicio-process__step-description">Diseñamos un programa específico con objetivos, plazos y métricas.</p>
                </div>
                <div class="servicio-process__step">
                    <span class="servicio-process__step-number">03</span>
                    <h3 class="servicio-process__step-title">Implementación</h3>
                    <p class="servicio-process__step-description">Ejecutamos el programa con seguimiento semanal y ajustes en tiempo real.</p>
                </div>
                <div class="servicio-process__step">
                    <span class="servicio-process__step-number">04</span>
                    <h3 class="servicio-process__step-title">Medición</h3>
                    <p class="servicio-process__step-description">Entregamos informe de resultados y recomendaciones de continuidad.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Solicitar Propuesta -->
    <section id="servicio-contact" class="servicio-contact" aria-label="Solicitar propuesta personalizada">
        <div class="servicio-contact__container">
            <header class="servicio-contact__header">
                <p class="servicio-contact__label">SOLICITAR PROPUESTA</p>
                <h2 class="servicio-contact__title">Cuéntanos sobre tu empresa</h2>
                <p class="servicio-contact__description">Completa el formulario y te enviamos una propuesta personalizada en menos de 48h. Sin compromiso.</p>
            </header>
            <div class="servicio-contact__content">
                <form id="proposal-form" class="servicio-contact__form">
                    <?php wp_nonce_field('form_servicio_empresa', 'nonce', false); ?>

                    <div class="servicio-contact__form-row">
                        <div class="servicio-contact__form-group">
                            <label for="proposal-nombre" class="servicio-contact__label-text">Nombre *</label>
                            <input type="text" id="proposal-nombre" name="nombre" class="servicio-contact__input" placeholder="Ej. Juan García Rodríguez" required>
                        </div>
                        <div class="servicio-contact__form-group">
                            <label for="proposal-cargo" class="servicio-contact__label-text">Cargo</label>
                            <input type="text" id="proposal-cargo" name="cargo" class="servicio-contact__input" placeholder="Ej. Director Comercial">
                        </div>
                    </div>
                    <div class="servicio-contact__form-row">
                        <div class="servicio-contact__form-group">
                            <label for="proposal-empresa" class="servicio-contact__label-text">Empresa *</label>
                            <input type="text" id="proposal-empresa" name="empresa" class="servicio-contact__input" placeholder="Ej. Tech Solutions S.A." required>
                        </div>
                        <div class="servicio-contact__form-group">
                            <label for="proposal-tamaño" class="servicio-contact__label-text">Tamaño del equipo</label>
                            <input type="text" id="proposal-tamaño" name="tamaño_equipo" class="servicio-contact__input" placeholder="Ej. 25 colaboradores">
                        </div>
                    </div>
                    <div class="servicio-contact__form-row">
                        <div class="servicio-contact__form-group">
                            <label for="proposal-email" class="servicio-contact__label-text">Email *</label>
                            <input type="email" id="proposal-email" name="email" class="servicio-contact__input" placeholder="Ej. juan@empresa.com" required>
                        </div>
                        <div class="servicio-contact__form-group">
                            <label for="proposal-whatsapp" class="servicio-contact__label-text">WhatsApp</label>
                            <input type="tel" id="proposal-whatsapp" name="whatsapp" class="servicio-contact__input" placeholder="Ej. +51 987 654 321">
                        </div>
                    </div>
                    <div class="servicio-contact__form-row servicio-contact__form-row--full">
                        <div class="servicio-contact__form-group">
                            <label for="proposal-servicio" class="servicio-contact__label-text">Servicio de interés *</label>
                            <div class="avance-select-wrapper">
                                <div class="avance-select-trigger" id="proposal-select-trigger" data-select="proposal-servicio">
                                    <span class="avance-select-value"><?php esc_html_e('Selecciona un servicio', 'avance-template'); ?></span>
                                    <svg class="avance-select-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
                                </div>
                                <div class="avance-select-dropdown" id="proposal-select-dropdown">
                                    <div class="avance-select-option" data-value=""><?php esc_html_e('Selecciona un servicio', 'avance-template'); ?></div>
                                    <div class="avance-select-option" data-value="Mentoría"><?php esc_html_e('Mentoría', 'avance-template'); ?></div>
                                    <div class="avance-select-option" data-value="Servicio Empresa"><?php esc_html_e('Servicio Empresa', 'avance-template'); ?></div>
                                    <div class="avance-select-option" data-value="Diagnóstico"><?php esc_html_e('Diagnóstico', 'avance-template'); ?></div>
                                    <div class="avance-select-option" data-value="Capacitación"><?php esc_html_e('Capacitación', 'avance-template'); ?></div>
                                    <div class="avance-select-option" data-value="Consultoría"><?php esc_html_e('Consultoría', 'avance-template'); ?></div>
                                </div>
                            </div>
                            <select id="proposal-servicio" name="servicio_interes" class="avance-select-hidden" required aria-required="true" style="display: none;">
                                <option value=""><?php esc_html_e('Selecciona un servicio', 'avance-template'); ?></option>
                                <option value="Mentoría"><?php esc_html_e('Mentoría', 'avance-template'); ?></option>
                                <option value="Servicio Empresa"><?php esc_html_e('Servicio Empresa', 'avance-template'); ?></option>
                                <option value="Diagnóstico"><?php esc_html_e('Diagnóstico', 'avance-template'); ?></option>
                                <option value="Capacitación"><?php esc_html_e('Capacitación', 'avance-template'); ?></option>
                                <option value="Consultoría"><?php esc_html_e('Consultoría', 'avance-template'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="servicio-contact__form-row servicio-contact__form-row--full">
                        <div class="servicio-contact__form-group">
                            <label for="proposal-desafio" class="servicio-contact__label-text">¿Cuál es tu principal desafío comercial? *</label>
                            <textarea id="proposal-desafio" name="desafio_comercial" class="servicio-contact__textarea" placeholder="Ej. Necesitamos mejorar nuestra estrategia de ventas y reorganizar el equipo..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="servicio-contact__btn">Solicitar propuesta personalizada</button>
                    <p class="servicio-contact__footnote">* Propuesta enviada en menos de 48h. Sin compromiso. <?php echo antispambot('informacion@avance-empresarial.com'); ?></p>
                </form>
                <div class="servicio-contact__benefits">
                    <div class="servicio-contact__benefits-list">
                        <h3 class="servicio-contact__benefits-title">¿POR QUÉ ELEGIRNOS?</h3>
                        <div class="servicio-contact__benefit">
                            <span class="servicio-contact__benefit-number">01</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">Experiencia real</h4>
                                <p class="servicio-contact__benefit-text">+15 años trabajando con empresas peruanas de distintos sectores.</p>
                            </div>
                        </div>
                        <div class="servicio-contact__benefit">
                            <span class="servicio-contact__benefit-number">02</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">No solo teoría</h4>
                                <p class="servicio-contact__benefit-text">Nos involucramos en la implementación y medimos los resultados contigo.</p>
                            </div>
                        </div>
                        <div class="servicio-contact__benefit">
                            <span class="servicio-contact__benefit-number">03</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">Metodología probada</h4>
                                <p class="servicio-contact__benefit-text">Framework B2B Management aplicado en +200 empresas.</p>
                            </div>
                        </div>
                        <div class="servicio-contact__benefit">
                            <span class="servicio-contact__benefit-number">04</span>
                            <div class="servicio-contact__benefit-content">
                                <h4 class="servicio-contact__benefit-title">Flexibilidad total</h4>
                                <p class="servicio-contact__benefit-text">100% online, blended o presencial. Nos adaptamos a tu equipo y agenda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="servicio-contact__direct">
                        <div class="servicio-contact__direct-info">
                            <p class="servicio-contact__direct-title">Contacto directo</p>
                            <p class="servicio-contact__direct-text">+51 991 908 301 · <?php echo antispambot('informacion@avance-empresarial.com'); ?></p>
                        </div>
                        <a href="<?php echo esc_url(AVANCE_WHATSAPP_URL); ?>" class="servicio-contact__direct-btn" <?php echo AVANCE_WHATSAPP_ATTRS; ?>>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/wsp.svg" alt="Icono de WhatsApp" width="16" height="16">
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
wp_enqueue_script('proposal-select', get_template_directory_uri() . '/assets/js/proposal-select.js', [], '1.0.0', true);
get_footer();
