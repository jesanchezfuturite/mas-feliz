<div style="width: 100%; background-color: #ffffff; border: 1px solid #f1f5f9; border-radius: 1rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.06); padding: 1.5rem;">

    <div style="margin-bottom: 1.25rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Resultados del tamizaje</h3>
        <p style="font-size: 0.8rem; color: #64748b; margin: 0.15rem 0 0;">Distribución de niveles por instrumento y por datos de identificación.</p>
    </div>

    @if ($total === 0)
        <div style="text-align: center; padding: 2rem 1rem; background-color: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 0.75rem; color: #94a3b8; font-size: 0.9rem;">
            Aún no hay tamizajes respondidos con resultado de riesgo para mostrar estadísticas.
        </div>
    @else
        <style>
            @media (min-width: 900px) {
                .estad-grid-2 { grid-template-columns: repeat(2, 1fr) !important; }
                .estad-grid-3 { grid-template-columns: repeat(3, 1fr) !important; }
            }
        </style>

        {{-- Sección 1: Resultados por instrumento --}}
        <h4 style="font-size: 0.8rem; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1rem; padding-bottom: 0.4rem; border-bottom: 1px solid #f1f5f9;">Resultados por instrumento</h4>

        <div class="estad-grid-3" style="display: grid; grid-template-columns: 1fr; gap: 1.25rem; margin-bottom: 2rem;">
            @foreach ($instrumentos as $inst)
                <div style="border: 1px solid #f1f5f9; border-radius: 0.75rem; padding: 1rem 1.1rem; background-color: #fcfdfe;">
                    <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 0.85rem;">
                        <span style="font-size: 0.92rem; font-weight: 700; color: #1e293b;">{{ $inst['titulo'] }}</span>
                        <span style="font-size: 0.72rem; color: #94a3b8;">{{ $inst['total'] }} evaluados</span>
                    </div>

                    @php $ti = max($inst['total'], 1); @endphp
                    <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                        @foreach ($inst['niveles'] as $nivel)
                            <div>
                                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 0.2rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; color: #334155;">
                                        <span style="height: 0.65rem; width: 0.65rem; border-radius: 2px; background-color: {{ $nivel['color'] }}; display: inline-block;"></span>
                                        {{ $nivel['label'] }}
                                    </span>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #1e293b;">{{ $nivel['count'] }}</span>
                                </div>
                                <div style="height: 0.45rem; width: 100%; border-radius: 9999px; background-color: #f1f5f9; overflow: hidden;">
                                    <div style="height: 100%; width: {{ $nivel['count'] / $ti * 100 }}%; background-color: {{ $nivel['color'] }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sección 2: Riesgo general por perfil --}}
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 0.4rem; border-bottom: 1px solid #f1f5f9;">
            <h4 style="font-size: 0.8rem; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Nivel de riesgo general por perfil</h4>
            <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.72rem; color: #475569;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="height:0.65rem;width:0.65rem;border-radius:2px;background:#10b981;display:inline-block;"></span>Leve</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="height:0.65rem;width:0.65rem;border-radius:2px;background:#f59e0b;display:inline-block;"></span>Moderado</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="height:0.65rem;width:0.65rem;border-radius:2px;background:#ef4444;display:inline-block;"></span>Urgente</span>
            </div>
        </div>

        <div class="estad-grid-2" style="display: grid; grid-template-columns: 1fr; gap: 1.75rem;">
            @foreach ($dimensiones as $dim)
                <div>
                    <h5 style="font-size: 0.78rem; font-weight: 700; color: #475569; margin: 0 0 0.85rem;">{{ $dim['titulo'] }}</h5>

                    @forelse ($dim['datos'] as $categoria => $c)
                        @php $t = max($c['total'], 1); @endphp
                        <div style="margin-bottom: 0.9rem;">
                            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 0.3rem;">
                                <span style="font-size: 0.85rem; color: #1e293b; font-weight: 500;">{{ $categoria }}</span>
                                <span style="font-size: 0.78rem; color: #64748b;">
                                    <span style="color:#10b981; font-weight:600;">{{ $c['Leve'] }}</span> ·
                                    <span style="color:#d97706; font-weight:600;">{{ $c['Moderado'] }}</span> ·
                                    <span style="color:#ef4444; font-weight:600;">{{ $c['Urgente'] }}</span>
                                    <span style="color:#94a3b8;">&nbsp;(Total: {{ $c['total'] }})</span>
                                </span>
                            </div>
                            <div style="display: flex; height: 0.6rem; width: 100%; border-radius: 9999px; overflow: hidden; background-color: #f1f5f9;">
                                <div style="width: {{ $c['Leve'] / $t * 100 }}%; background-color: #10b981;"></div>
                                <div style="width: {{ $c['Moderado'] / $t * 100 }}%; background-color: #f59e0b;"></div>
                                <div style="width: {{ $c['Urgente'] / $t * 100 }}%; background-color: #ef4444;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 0.82rem; color: #94a3b8; margin: 0;">Sin datos para esta categoría.</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    @endif

</div>
