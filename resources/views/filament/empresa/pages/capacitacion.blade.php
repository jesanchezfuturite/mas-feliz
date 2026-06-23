<x-filament-panels::page>
    @if (!$isHabilitado)
        <!-- Vista Próximamente -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 50vh; padding: 2rem 1rem;">
            <div style="width: 100%; max-width: 42rem; background-color: #ffffff; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border-radius: 1rem; border: 1px solid #f1f5f9; padding: 3rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
                
                <!-- Icono de alerta / reloj -->
                <div style="height: 5rem; width: 5rem; background-color: #dbeafe; color: #1e40af; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 2.5rem; width: 2.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; letter-spacing: -0.025em; margin: 0;">Sección en Desarrollo</h3>
                    <p style="color: #475569; font-size: 1.125rem; line-height: 1.75; max-width: 32rem; margin: 0 auto;">
                        El acceso a las <strong>fechas oficiales y recursos de capacitación (Apoyo al criterio indispensable 10)</strong> se habilitará el <strong>10 de julio</strong>.
                    </p>
                </div>

                <div style="font-size: 0.875rem; color: #94a3b8; font-weight: 500;">
                    Oficina Inspira Coahuila &bull; Gobierno del Estado
                </div>
            </div>
        </div>
    @else
        <!-- Vista Activa de la Página de Capacitación -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 2rem; border-left: 4px solid #10b981;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Capacitación en Salud Mental para Líderes</h2>
                <p style="color: #475569; font-size: 1rem; line-height: 1.6; margin: 0;">
                    Este recurso apoya al <strong>Criterio 10 (Necesario)</strong> para capacitar a directivos, líderes y gerentes en señales de alerta de salud mental y liderazgo positivo.
                </p>
            </div>

            <!-- Listado de Cursos Disponibles -->
            <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">Talleres y Webinars Programados</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="padding: 1rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        <span style="background-color: #eff6ff; color: #3b82f6; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem; align-self: flex-start; text-transform: uppercase;">Módulo 1</span>
                        <h4 style="font-size: 1.05rem; font-weight: 600; color: #334155; margin: 0;">Liderazgo Positivo y Sensibilización en Salud Mental</h4>
                        <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Duración: 2 horas. Temas: Reducción del estigma, factores de riesgo y comunicación empática.</p>
                    </div>

                    <div style="padding: 1rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        <span style="background-color: #eff6ff; color: #3b82f6; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.25rem; align-self: flex-start; text-transform: uppercase;">Módulo 2</span>
                        <h4 style="font-size: 1.05rem; font-weight: 600; color: #334155; margin: 0;">Detección Oportuna de Señales de Alerta</h4>
                        <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Duración: 3 horas. Temas: Identificación de cambios de comportamiento, ideación suicida y ruta de canalización.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
