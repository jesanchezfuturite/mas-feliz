<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center py-2">
            {{-- Columna 1: Nombre de la Empresa --}}
            <div class="flex items-center gap-4">
                <div style="padding: 0.75rem; background-color: #f9fafb; border-radius: 1rem; border: 1px solid #f3f4f6;" class="dark:bg-gray-900/50 dark:border-gray-800">
                    <x-heroicon-s-building-office style="width: 32px; height: 32px;" class="text-gray-700 dark:text-gray-300" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Nombre de la Empresa</span>
                    <span class="text-lg font-black text-gray-900 dark:text-white leading-tight">
                        {{ $empresa->nombre_empresa }}
                    </span>
                </div>
            </div>

            {{-- Columna 2: RFC --}}
            <div class="flex flex-col border-l border-gray-100 dark:border-gray-800 pl-6">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 leading-none">Registro Federal (RFC)</span>
                <span class="text-base font-bold text-gray-800 dark:text-gray-200">
                    {{ $empresa->rfc ?? 'No disponible' }}
                </span>
            </div>

            {{-- Columna 3: Folio --}}
            <div class="flex flex-col border-l border-gray-100 dark:border-gray-800 pl-6">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 leading-none">Folio de Registro</span>
                <span class="text-base font-bold text-gray-800 dark:text-gray-200">
                    {{ $empresa->folio ?? 'N/A' }}
                </span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
