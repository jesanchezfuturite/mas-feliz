@php
    // Todo va con estilos en línea, como la Ruta Crítica: en este panel las
    // utilidades de Tailwind de las vistas no llegan al navegador.
    $colorEstatus = match ($estatus) {
        'Aprobado' => ['texto' => '#15803d', 'fondo' => '#dcfce7', 'borde' => '#bbf7d0'],
        'No aprobado' => ['texto' => '#b91c1c', 'fondo' => '#fee2e2', 'borde' => '#fecaca'],
        'En revisión' => ['texto' => '#b45309', 'fondo' => '#fef3c7', 'borde' => '#fde68a'],
        default => ['texto' => '#4b5563', 'fondo' => '#f3f4f6', 'borde' => '#e5e7eb'],
    };

    $colorNivel = match ($nivel) {
        'Intermedio' => ['texto' => '#0369a1', 'fondo' => '#e0f2fe', 'borde' => '#bae6fd'],
        'Avanzado' => ['texto' => '#4338ca', 'fondo' => '#eef2ff', 'borde' => '#c7d2fe'],
        'Excelencia' => ['texto' => '#15803d', 'fondo' => '#dcfce7', 'borde' => '#bbf7d0'],
        default => ['texto' => '#4b5563', 'fondo' => '#f3f4f6', 'borde' => '#e5e7eb'],
    };

    $tarjeta = 'flex: 1 1 200px; min-width: 200px; max-width: 320px; background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;';
    $etiqueta = 'font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; line-height: 1;';
    $pastilla = 'align-self: flex-start; border-radius: 9999px; padding: 0.25rem 0.875rem; font-size: 0.875rem; font-weight: 800; line-height: 1.4;';
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
            <div style="padding: 0.5rem; background-color: #f3f4f6; border-radius: 0.5rem; display: flex;">
                <x-heroicon-s-shield-check style="width: 20px; height: 20px; color: #4b5563;" />
            </div>
            <h3 style="font-size: 0.875rem; font-weight: 800; color: #1f2937; text-transform: uppercase; letter-spacing: 0.1em; line-height: 1; margin: 0;">
                Resultado de la Dictaminación
            </h3>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
            {{-- El resultado --}}
            <div style="{{ $tarjeta }}">
                <span style="{{ $etiqueta }}">Estatus del Distintivo</span>
                <span style="{{ $pastilla }} color: {{ $colorEstatus['texto'] }}; background-color: {{ $colorEstatus['fondo'] }}; border: 1px solid {{ $colorEstatus['borde'] }};">
                    {{ $estatus ?? 'Sin dictaminar' }}
                </span>
            </div>

            {{-- El nivel alcanzado, que es lo que se les pidió consultar --}}
            @if($nivel)
                <div style="{{ $tarjeta }}">
                    <span style="{{ $etiqueta }}">Nivel de Madurez</span>
                    <span style="{{ $pastilla }} color: {{ $colorNivel['texto'] }}; background-color: {{ $colorNivel['fondo'] }}; border: 1px solid {{ $colorNivel['borde'] }};">
                        {{ $nivel }}
                    </span>
                </div>
            @endif

            {{-- Cuándo se dictaminó --}}
            @if($fecha)
                <div style="{{ $tarjeta }}">
                    <span style="{{ $etiqueta }}">Fecha de Dictamen</span>
                    <span style="font-size: 0.9375rem; font-weight: 700; color: #1f2937;">
                        {{ $fecha->format('d/m/Y') }}
                    </span>
                </div>
            @endif

            {{-- El distintivo, cuando ya se generó --}}
            @if($rutaPdf)
                <div style="{{ $tarjeta }}">
                    <span style="{{ $etiqueta }}">Distintivo</span>
                    <a href="/storage/{{ $rutaPdf }}" target="_blank"
                       style="align-self: flex-start; display: inline-flex; align-items: center; gap: 0.5rem; background-color: #556ee6; color: #ffffff; border-radius: 0.5rem; padding: 0.375rem 0.875rem; font-size: 0.8125rem; font-weight: 700; text-decoration: none;">
                        <x-heroicon-s-arrow-down-tray style="width: 16px; height: 16px;" />
                        Descargar
                    </a>
                </div>
            @endif
        </div>

        {{-- El comentario del Comité: Angélica pidió que se llamara así, a secas. --}}
        @if($comentario)
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f3f4f6; display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="{{ $etiqueta }}">Comentario</span>
                <p style="font-size: 0.9375rem; color: #374151; line-height: 1.6; white-space: pre-line; margin: 0;">{{ $comentario }}</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
