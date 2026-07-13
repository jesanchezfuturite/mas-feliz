<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-center py-2">
            {{-- Columna 1: Nombre de la Empresa --}}
            <div class="flex items-center gap-4">
                <div style="padding: 0.75rem; background-color: #f9fafb; border-radius: 1rem; border: 1px solid #f3f4f6;" class="dark:bg-gray-900/50 dark:border-gray-800">
                    <x-heroicon-s-building-office style="width: 32px; height: 32px;" class="text-gray-700 dark:text-gray-300" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Organización</span>
                    <span class="text-base font-black text-gray-900 dark:text-white leading-tight">
                        {{ $empresa->nombre_empresa }}
                    </span>
                </div>
            </div>

            {{-- Columna 2: RFC y Folio (Compactado para dar espacio) --}}
            <div class="flex flex-col border-l border-gray-100 dark:border-gray-800 pl-6">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 leading-none">Identificación</span>
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200">
                        RFC: <span class="font-medium uppercase">{{ $empresa->rfc ?? 'N/A' }}</span>
                    </span>
                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200">
                        Folio: <span class="font-medium text-primary-600">{{ $empresa->folio ?? 'N/A' }}</span>
                    </span>
                </div>
            </div>

            {{-- Columna 3: Registro --}}
            <div class="flex flex-col border-l border-gray-100 dark:border-gray-800 pl-6">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 leading-none">Fecha de Alta</span>
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                    {{ $empresa->created_at?->format('d/m/Y') ?? 'N/A' }}
                </span>
            </div>

            {{-- Columna 4: Visita Presencial (NUEVO) --}}
            <div class="flex flex-col border-l border-gray-100 dark:border-gray-800 pl-6">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 leading-none text-primary-600">Visita Presencial</span>
                @if($empresa->fecha_visita_presencial)
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-calendar-days class="w-4 h-4 text-primary-500" />
                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase">
                            {{ $empresa->fecha_visita_presencial->format('d/m/Y') }}
                            <span class="text-[10px] font-medium text-gray-500 ml-1">at {{ $empresa->fecha_visita_presencial->format('h:i A') }}</span>
                        </span>
                    </div>
                @else
                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs font-bold text-gray-500 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                        Por definir
                    </span>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
