<x-filament-panels::page>
    {{-- Estilos en línea: en este panel las utilidades de Tailwind de las
         vistas no llegan al navegador. --}}
    @if($pendiente)
        <div style="display: flex; gap: 0.875rem; align-items: flex-start; background-color: #eef2ff; border: 1px solid #c7d2fe; border-radius: 0.75rem; padding: 1rem 1.25rem;">
            <x-heroicon-s-lock-closed style="width: 22px; height: 22px; color: #4338ca; flex-shrink: 0; margin-top: 2px;" />
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.9375rem; font-weight: 700; color: #312e81;">
                    Un paso antes de ver tu resultado
                </span>
                <span style="font-size: 0.875rem; color: #3730a3; line-height: 1.6;">
                    Tu organización obtuvo el Distintivo +Feliz. Confirma cómo debe aparecer en el reconocimiento, sube tu logotipo y dinos a quién dirigir la invitación al evento de entrega. Al confirmar se mostrará tu nivel de madurez.
                </span>
            </div>
        </div>
    @else
        <div style="display: flex; gap: 0.875rem; align-items: flex-start; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; padding: 1rem 1.25rem;">
            <x-heroicon-s-check-circle style="width: 22px; height: 22px; color: #15803d; flex-shrink: 0; margin-top: 2px;" />
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.9375rem; font-weight: 700; color: #14532d;">
                    Datos confirmados{{ $confirmado ? ' el '.$confirmado->format('d/m/Y') : '' }}
                </span>
                <span style="font-size: 0.875rem; color: #166534; line-height: 1.6;">
                    Con estos datos se elabora tu reconocimiento y se envía la invitación. Si algo cambió, corrígelo aquí y avisa a tu acompañante: una vez que se manda a imprimir ya no es posible modificarlo.
                </span>
            </div>
        </div>
    @endif

    <form wire:submit="save">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
