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
            @php
                $flujogramaUrl = $flujograma ? asset('storage/' . $flujograma) : null;
                $ext = $flujograma ? strtolower(pathinfo($flujograma, PATHINFO_EXTENSION)) : null;
                $esImagen = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            @endphp

            @if ($flujograma)
                <!-- Flujograma cargado: visualización lista para ver e imprimir -->
                <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 1.75rem;">
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: #1e293b; margin: 0;">Flujograma de actuación ante crisis en salud mental</h2>
                        <a href="{{ $flujogramaUrl }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #ef4444; color: #ffffff; font-weight: 600; font-size: 0.9rem; padding: 0.6rem 1.1rem; border-radius: 0.5rem; text-decoration: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.15rem; height: 1.15rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                            </svg>
                            Ver / Imprimir
                        </a>
                    </div>

                    @if ($esImagen)
                        <img src="{{ $flujogramaUrl }}" alt="Flujograma de actuación ante crisis" style="width: 100%; height: auto; border: 1px solid #f1f5f9; border-radius: 0.5rem;" />
                    @else
                        <iframe src="{{ $flujogramaUrl }}" style="width: 100%; height: 80vh; border: 1px solid #f1f5f9; border-radius: 0.5rem;" title="Flujograma de actuación ante crisis"></iframe>
                    @endif
                </div>
            @else
                <!-- Mensaje provisional: aún no se ha cargado el flujograma -->
                <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 3rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; border-left: 4px solid #ef4444;">
                    <div style="height: 4rem; width: 4rem; background-color: #fef2f2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="height: 2rem; width: 2rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <p style="color: #475569; font-size: 1.125rem; line-height: 1.6; max-width: 34rem; margin: 0;">
                        Aquí encontrarás el flujograma de actuación ante crisis en salud mental.
                    </p>
                </div>
            @endif

            <!-- Materiales de Apoyo -->
            <div style="background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 1rem; padding: 1.75rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="background-color: #fef2f2; color: #ef4444; padding: 0.5rem; border-radius: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v3.75m-9.75-3h.008v.008H12V6.75z" />
                        </svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: #1e293b; margin: 0;">Materiales de Apoyo</h3>
                </div>
                <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin: 0;">
                    Descarga recursos para la atención de crisis de salud mental:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                    @forelse($materiales as $material)
                        @php
                            $url = $material->tipo === 'enlace' || $material->tipo === 'video'
                                ? $material->enlace_url
                                : asset('storage/' . $material->archivo_path);

                            $icon = match($material->tipo) {
                                'pdf' => '📋',
                                'imagen' => '🖼️',
                                'video' => '🎥',
                                'enlace' => '🌐',
                                default => '📄',
                            };

                            $btnLabel = match($material->tipo) {
                                'pdf' => 'Descargar PDF',
                                'imagen' => 'Ver Imagen',
                                'video' => 'Ver Video',
                                'enlace' => 'Abrir Enlace',
                                default => 'Abrir',
                            };
                        @endphp

                        <a href="{{ $url }}" target="_blank" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; text-decoration: none; color: #475569; font-weight: 500; font-size: 0.9rem; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='#f8fafc'">
                            <span>{{ $icon }} {{ $material->titulo }}</span>
                            <span style="color: #ef4444;">{{ $btnLabel }}</span>
                        </a>
                    @empty
                        <div style="text-align: center; padding: 1.5rem; background-color: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 0.5rem; color: #94a3b8; font-size: 0.9rem;">
                            No hay materiales de apoyo disponibles por el momento.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
