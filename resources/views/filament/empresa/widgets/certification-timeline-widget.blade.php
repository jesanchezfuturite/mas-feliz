@php
    $colorMap = [
        0 => '#84cc16', // Registro: lime-500
        1 => '#10b981', // Diagnóstico: emerald-500
        2 => '#06b6d4', // Retroalimentación: cyan-500
        3 => '#f97316', // Plan de acción: orange-500
        4 => '#db2777', // Evaluación: pink-600
        5 => '#22c55e', // Reconocimiento: green-500
    ];
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-3 mb-12">
            <div style="padding: 0.5rem; background-color: #f3f4f6; border-radius: 0.5rem;">
                <x-heroicon-s-map style="width: 20px; height: 20px;" class="text-gray-600 dark:text-gray-400" />
            </div>
            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-widest leading-none">
                Ruta Crítica de Avance
            </h3>
        </div>

        <div style="position: relative; padding: 0 1rem; margin-bottom: 2rem;">
            {{-- Connector Line Background --}}
            <div style="position: absolute; top: 28px; left: 2rem; right: 2rem; height: 6px; background-color: #f3f4f6; border-radius: 99px; z-index: 0;" class="dark:bg-gray-800"></div>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; position: relative; z-index: 10;">
                @foreach($steps as $index => $step)
                    @php
                        $isPending = $step['status'] === 'pending';
                        $hexColor = $isPending ? '#e5e7eb' : ($colorMap[$index] ?? '#556ee6');
                        $iconColorStyle = $isPending ? 'color: #9ca3af;' : 'color: #ffffff;';
                    @endphp

                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; width: 140px; position: relative;">
                        {{-- Perfect Circle Container (w-14 h-14) --}}
                        <div class="shadow-xl" 
                             style="width: 56px; height: 56px; min-width: 56px; border-radius: 50% !important; background-color: {{ $hexColor }}; display: flex; align-items: center; justify-content: center; position: relative; margin-bottom: 1rem; z-index: 20; border: 4px solid rgba(255,255,255,0.4); overflow: hidden;">
                            
                            {{-- Specific icon requested --}}
                            <x-dynamic-component :component="$step['icon']" style="width: 28px; height: 28px; {{ $iconColorStyle }}" />

                            {{-- Small checkmark badge if completed --}}
                            @if($step['status'] === 'completed')
                                <div style="position: absolute; bottom: 0; right: 0; background-color: #ffffff; border-radius: 50%; padding: 2px; display: flex;">
                                    <x-heroicon-s-check-circle style="width: 14px; height: 14px; color: #10b981;" />
                                </div>
                            @endif

                            {{-- Active Indicator Overlay --}}
                            @if($step['status'] === 'active')
                                <span style="position: absolute; top: -2px; right: -2px; display: flex; height: 18px; width: 18px; z-index: 30;">
                                    <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #556ee6; opacity: 0.75;" class="animate-ping"></span>
                                    <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 14px; width: 14px; background-color: #556ee6; border: 2px solid #ffffff;"></span>
                                </span>
                            @endif
                        </div>

                        {{-- Label Container --}}
                        <div style="min-height: 50px;" class="flex flex-col items-center">
                            <span @class([
                                'uppercase tracking-tight block leading-tight',
                                'text-gray-900 dark:text-white font-bold' => ! $isPending,
                                'text-gray-400 dark:text-gray-600 font-semibold' => $isPending,
                            ]) class="text-[10px] max-w-[110px]">
                                {{ $step['label'] }}
                            </span>
                            
                            @if($step['status'] === 'active')
                                <span class="mt-2 text-[8px] font-black text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full dark:bg-primary-900/40 tracking-tighter uppercase">
                                    Estás Aquí
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
