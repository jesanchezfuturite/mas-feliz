@php
    $empresa = auth()->user();
    $autoevaluacion = $empresa->autoevaluaciones()->first();
    
    $autoevaluacionUrl = $autoevaluacion 
        ? \App\Filament\Empresa\Resources\Autoevaluacions\AutoevaluacionResource::getUrl('edit', ['record' => $autoevaluacion->id])
        : \App\Filament\Empresa\Resources\Autoevaluacions\AutoevaluacionResource::getUrl('create');
        
    $prevencionUrl = \App\Filament\Empresa\Pages\PrevencionPromocion::getUrl();
    $tamizajeUrl = \App\Filament\Empresa\Resources\Tamizajes\TamizajeResource::getUrl('index');
    $crisisUrl = \App\Filament\Empresa\Pages\Crisis::getUrl();
    $capacitacionUrl = \App\Filament\Empresa\Pages\Capacitacion::getUrl();
@endphp

<div class="fi-section rounded-xl bg-white p-6 shadow-sm border border-gray-100" style="width: 100%;">
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Ruta Metodológica de tu Organización</h2>
        <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0 0;">Sigue los pasos secuenciales recomendados para obtener el distintivo de Salud Mental.</p>
    </div>

    <!-- Pipeline Container -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%; position: relative;">
        <!-- Conexión de línea visual en Desktop -->
        <div style="display: none; position: absolute; top: 1.75rem; left: 10%; right: 10%; height: 2px; background-color: #e2e8f0; z-index: 1;"></div>
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem; z-index: 2; width: 100%;">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 w-full">
                
                <!-- Paso 1: Registro -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; position: relative;">
                    <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                        ✓
                    </div>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Paso 1: Registro</h4>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Datos generales completados.</p>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #10b981; background-color: #d1fae5; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-top: auto;">Listo</span>
                </div>

                <!-- Paso 2: Autoevaluación -->
                <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; position: relative; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)'" onmouseout="this.style.boxShadow='none'">
                    @if($estadoAutoevaluacion === 'validado')
                        <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                            ✓
                        </div>
                    @elseif($estadoAutoevaluacion === 'revision')
                        <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                            ⌛
                        </div>
                    @else
                        <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                            2
                        </div>
                    @endif

                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Paso 2: Autoevaluación</h4>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Cédula oficial de 20 criterios.</p>
                    </div>

                    @if($estadoAutoevaluacion === 'validado')
                        <span style="font-size: 0.75rem; font-weight: 700; color: #10b981; background-color: #d1fae5; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-top: auto;">Validada</span>
                    @elseif($estadoAutoevaluacion === 'revision')
                        <span style="font-size: 0.75rem; font-weight: 700; color: #d97706; background-color: #fef3c7; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-top: auto;">En Revisión</span>
                    @elseif($estadoAutoevaluacion === 'borrador')
                        <a href="{{ $autoevaluacionUrl }}" style="font-size: 0.75rem; font-weight: 700; color: #ffffff; background-color: #f59e0b; padding: 0.35rem 0.75rem; border-radius: 0.375rem; text-decoration: none; margin-top: auto; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#d97706'" onmouseout="this.style.backgroundColor='#f59e0b'">Editar Borrador</a>
                    @else
                        <a href="{{ $autoevaluacionUrl }}" style="font-size: 0.75rem; font-weight: 700; color: #ffffff; background-color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 0.375rem; text-decoration: none; margin-top: auto; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#3b82f6'">Iniciar Cédula</a>
                    @endif
                </div>

                <!-- Paso 3: Prevención / Promoción -->
                <div style="background-color: {{ $autoevaluacionCompletada ? '#ffffff' : '#f8fafc' }}; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; opacity: {{ $autoevaluacionCompletada ? '1' : '0.6' }};">
                    <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: {{ $autoevaluacionCompletada ? '#eff6ff' : '#f1f5f9' }}; color: {{ $autoevaluacionCompletada ? '#3b82f6' : '#94a3b8' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                        3
                    </div>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Paso 3: Prevención</h4>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Programa de promoción (Criterio 9).</p>
                    </div>

                    @if($autoevaluacionCompletada)
                        <a href="{{ $prevencionUrl }}" style="font-size: 0.75rem; font-weight: 700; color: #ffffff; background-color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 0.375rem; text-decoration: none; margin-top: auto; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#3b82f6'">Ir a Prevención</a>
                    @else
                        <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; background-color: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-top: auto; display: flex; align-items: center; gap: 0.25rem;">
                            🔒 Bloqueado
                        </span>
                    @endif
                </div>

                <!-- Paso 4: Diagnóstico y Tamizaje -->
                <div style="background-color: {{ $autoevaluacionCompletada ? '#ffffff' : '#f8fafc' }}; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; opacity: {{ $autoevaluacionCompletada ? '1' : '0.6' }};">
                    <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: {{ $autoevaluacionCompletada ? '#eff6ff' : '#f1f5f9' }}; color: {{ $autoevaluacionCompletada ? '#3b82f6' : '#94a3b8' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                        4
                    </div>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Paso 4: Diagnóstico</h4>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Tamizaje de colaboradores (Criterio 4).</p>
                    </div>

                    @if($autoevaluacionCompletada)
                        <a href="{{ $tamizajeUrl }}" style="font-size: 0.75rem; font-weight: 700; color: #ffffff; background-color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 0.375rem; text-decoration: none; margin-top: auto; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#3b82f6'">Ver Diagnósticos</a>
                    @else
                        <span style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; background-color: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-top: auto; display: flex; align-items: center; gap: 0.25rem;">
                            🔒 Bloqueado
                        </span>
                    @endif
                </div>

                <!-- Paso 5: Crisis / Capacitación -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; opacity: 0.6;">
                    <div style="height: 2.5rem; width: 2.5rem; border-radius: 50%; background-color: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                        5
                    </div>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">Paso 5: Crisis y Cursos</h4>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">Criterios 10 y 16 (10 de julio).</p>
                    </div>

                    <span style="font-size: 0.75rem; font-weight: 700; color: #475569; background-color: #cbd5e1; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-top: auto;">Próximamente</span>
                </div>

            </div>
        </div>
    </div>
</div>
