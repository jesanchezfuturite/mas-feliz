<x-filament-panels::page>
    @if (!$isHabilitado)
        <!-- Vista Próximamente -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 50vh; padding: 2rem 1rem;">
            <div style="width: 100%; max-width: 42rem; background-color: #ffffff; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border-radius: 1rem; border: 1px solid #f1f5f9; padding: 3rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
                
                <!-- Icono de alerta / reloj -->
                <div style="height: 5rem; width: 5rem; background-color: #fef3c7; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 2.5rem; width: 2.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; letter-spacing: -0.025em; margin: 0;">Sección en Desarrollo</h3>
                    <p style="color: #475569; font-size: 1.125rem; line-height: 1.75; max-width: 32rem; margin: 0 auto;">
                        El acceso al <strong>flujograma de atención a crisis y registro de casos (Apoyo al criterio indispensable 16)</strong> estará disponible a partir del <strong>10 de julio</strong>.
                    </p>
                </div>

                <div style="font-size: 0.875rem; color: #94a3b8; font-weight: 500;">
                    Oficina Inspira Coahuila &bull; Gobierno del Estado
                </div>
            </div>
        </div>
    @else
        <!-- Vista Activa del Flujograma de Atención a Crisis -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 2rem; border-left: 4px solid #ef4444;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Protocolo de Actuación ante Crisis de Salud Mental</h2>
                <p style="color: #475569; font-size: 1rem; line-height: 1.6; margin: 0;">
                    Este recurso apoya al <strong>Criterio 16 (Indispensable)</strong> y proporciona las herramientas para identificar personal en riesgo agudo, dar contención inicial y canalizar de forma segura y ética.
                </p>
            </div>

            <!-- Flujograma de 3 pasos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">

                <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 1.5rem; border-top: 4px solid #f59e0b;">
                    <div style="font-size: 0.875rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem; text-transform: uppercase;">Paso 1</div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 0.75rem;">Detección Inicial</h3>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin: 0;">
                        Identificar señales de alerta en el colaborador (cambios abruptos de conducta, aislamiento, ideación, crisis de ansiedad aguda).
                    </p>
                </div>

                <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 1.5rem; border-top: 4px solid #ef4444;">
                    <div style="font-size: 0.875rem; font-weight: 700; color: #ef4444; margin-bottom: 0.5rem; text-transform: uppercase;">Paso 2</div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 0.75rem;">Primeros Auxilios Psicológicos</h3>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin: 0;">
                        Intervención inmediata por parte de un "Gatekeeper" capacitado. Brindar escucha activa, empatía y estabilización emocional.
                    </p>
                </div>

                <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 1.5rem; border-top: 4px solid #10b981;">
                    <div style="font-size: 0.875rem; font-weight: 700; color: #10b981; margin-bottom: 0.5rem; text-transform: uppercase;">Paso 3</div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 0.75rem;">Canalización Directa</h3>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin: 0;">
                        Llamar a la Línea de Vida del Estado de Coahuila y canalizar al colaborador con el servicio de apoyo interno/externo asignado.
                    </p>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
