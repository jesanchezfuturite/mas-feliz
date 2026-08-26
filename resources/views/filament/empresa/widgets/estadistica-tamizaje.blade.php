{{-- El envoltorio de Filament es el que carga las clases de columna del
     grid de widgets: sin él, el `columnSpan = 'full'` del widget no se
     aplica y el grid lo encajona en una sola columna. --}}
<x-filament-widgets::widget>
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
        {{-- Las columnas se deciden por el ancho DISPONIBLE, no por el del
             viewport: una media query metía dos columnas aunque el widget
             estuviera encajonado en 440px y las barras se salían de la
             tarjeta. minmax(0, 1fr) permite a cada columna encogerse. --}}
        <style>
            .estad-grid-2 { display: grid; gap: 1.75rem; grid-template-columns: repeat(auto-fit, minmax(min(24rem, 100%), 1fr)); }
            .estad-grid-3 { display: grid; gap: 1.25rem; grid-template-columns: repeat(auto-fit, minmax(min(15rem, 100%), 1fr)); }
            .estad-grid-2 > div, .estad-grid-3 > div { min-width: 0; }
        </style>

        {{-- Sección 1: Resultados por instrumento --}}
        <h4 style="font-size: 0.8rem; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1rem; padding-bottom: 0.4rem; border-bottom: 1px solid #f1f5f9;">Resultados por instrumento</h4>

        <div class="estad-grid-3" style="margin-bottom: 2rem;">
            @foreach ($instrumentos as $inst)
                <div style="border: 1px solid #f1f5f9; border-radius: 0.75rem; padding: 1rem 1.1rem; background-color: #fcfdfe;">
                    {{-- Título y total en bloque: lado a lado se estrujaban y el
                         título se partía palabra por palabra. --}}
                    <div style="margin-bottom: 0.85rem;">
                        <span style="display: block; font-size: 0.92rem; font-weight: 700; color: #1e293b; line-height: 1.3;">{{ $inst['titulo'] }}</span>
                        <span style="font-size: 0.72rem; color: #94a3b8;">{{ $inst['total'] }} personas evaluadas</span>
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

        {{-- Sección 2: sintomatología (o prioridad) por perfil, con selector de instrumento --}}
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 0.4rem; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                <h4 style="font-size: 0.8rem; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">{{ $tituloPerfil }} por perfil</h4>
                <select wire:model.live="instrumento" style="font-size: 0.78rem; color: #334155; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.25rem 1.75rem 0.25rem 0.6rem; background-color: #ffffff;">
                    @foreach ($opcionesInstrumento as $valor => $titulo)
                        <option value="{{ $valor }}">{{ $titulo }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.85rem; font-size: 0.72rem; color: #475569;">
                @foreach ($escala as $nivel)
                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="height:0.65rem;width:0.65rem;border-radius:2px;background:{{ $nivel['color'] }};display:inline-block;"></span>{{ $nivel['label'] }}</span>
                @endforeach
            </div>
        </div>

        @if ($nota)
            <p style="font-size: 0.72rem; color: #94a3b8; margin: -0.5rem 0 1rem; line-height: 1.4;"><strong>Nota:</strong> {{ $nota }}</p>
        @endif

        <div class="estad-grid-2">
            @foreach ($dimensiones as $dim)
                <div>
                    <h5 style="font-size: 0.78rem; font-weight: 700; color: #475569; margin: 0 0 0.85rem;">{{ $dim['titulo'] }}</h5>

                    @forelse ($dim['datos'] as $categoria => $c)
                        @php $t = max($c['total'], 1); @endphp
                        <div style="margin-bottom: 0.9rem;">
                            {{-- Línea 1: categoría y total. Los conteos van
                                 DEBAJO de la barra, donde pueden envolver: en
                                 la misma línea empujaban la fila fuera de la
                                 tarjeta en pantallas medianas. --}}
                            <div style="display: flex; align-items: baseline; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.3rem;">
                                <span style="font-size: 0.85rem; color: #1e293b; font-weight: 500; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $categoria }}">{{ $categoria }}</span>
                                <span style="font-size: 0.78rem; color: #64748b; white-space: nowrap; flex-shrink: 0;">{{ $c['total'] }} {{ $c['total'] === 1 ? 'persona' : 'personas' }}</span>
                            </div>
                            <div style="display: flex; height: 0.6rem; width: 100%; border-radius: 9999px; overflow: hidden; background-color: #f1f5f9;">
                                @foreach ($escala as $nivel)
                                    @if ($c[$nivel['label']] > 0)
                                        <div style="width: {{ $c[$nivel['label']] / $t * 100 }}%; background-color: {{ $nivel['color'] }};" title="{{ $nivel['label'] }}: {{ $c[$nivel['label']] }}"></div>
                                    @endif
                                @endforeach
                            </div>
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.3rem 0.75rem; margin-top: 0.3rem; font-size: 0.75rem; color: #475569;">
                                {{-- Solo los niveles con personas: los ceros son ruido. --}}
                                @foreach ($escala as $nivel)
                                    @if ($c[$nivel['label']] > 0)
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem;" title="{{ $nivel['label'] }}: {{ $c[$nivel['label']] }} ({{ round($c[$nivel['label']] / $t * 100) }}%)">
                                            <span style="height:0.5rem;width:0.5rem;border-radius:2px;background:{{ $nivel['color'] }};display:inline-block;"></span>{{ $c[$nivel['label']] }} <span style="color:#94a3b8;">({{ round($c[$nivel['label']] / $t * 100) }}%)</span>
                                        </span>
                                    @endif
                                @endforeach
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
</x-filament-widgets::widget>
