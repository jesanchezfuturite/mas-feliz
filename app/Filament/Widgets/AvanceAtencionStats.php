<?php

namespace App\Filament\Widgets;

use App\Support\MetricasAtencion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Avance de la atención en salud mental para administración: cifras agregadas
 * de todas las organizaciones, sin datos de los colaboradores.
 */
class AvanceAtencionStats extends BaseWidget
{
    protected ?string $heading = 'Avance de la atención';

    protected ?string $description = 'Cifras agregadas del programa. No incluye datos personales de los colaboradores.';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    // Es el primer dato que se busca al entrar: se carga junto con la página,
    // no en diferido. Son consultas de conteo, no pesan.
    protected static bool $isLazy = false;

    /**
     * Empresas que entran en el cálculo. null = todas.
     *
     * @return array<int>|null
     */
    protected function empresaIds(): ?array
    {
        return null;
    }

    protected function getStats(): array
    {
        $metricas = MetricasAtencion::paraEmpresas($this->empresaIds());

        $aplicados = $metricas->tamizajesAplicados();
        $canalizados = $metricas->casosCanalizados();
        $referencias = $metricas->referenciasEnviadas();
        $sinCita = $metricas->referenciasSinCita();
        $universo = $metricas->colaboradoresRegistrados();
        $participacion = $metricas->porcentajeParticipacion();
        $consentimiento = $metricas->porcentajeConsentimiento();
        $porEstatus = $metricas->casosPorCadaEstatus();

        return [
            // El porcentaje va sobre el universo convocado, no sobre los
            // cuestionarios respondidos: es la lectura contra la que se mide la
            // meta indispensable del 90%.
            Stat::make('Participación', $participacion === null ? 'Sin dato' : $participacion.'%')
                ->description($universo > 0
                    ? $aplicados.' de '.$universo.' colaboradores registrados · meta 90%'
                    : 'Las organizaciones no han declarado su total de colaboradores')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color(match (true) {
                    $participacion === null => 'gray',
                    $participacion >= 90 => 'success',
                    default => 'warning',
                }),

            Stat::make('Consentimiento', $consentimiento === null ? 'Sin dato' : $consentimiento.'%')
                ->description($metricas->participaron().' otorgaron · '.$metricas->noParticiparon().' declinaron')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('info'),

            // La cifra suma los niveles de REQUIEREN_ATENCION; la descripción
            // los enumera para que no se lea como dato inflado (Angélica,
            // 31/08/2026: "me siguen saliendo muchos en riesgo").
            Stat::make('Personas detectadas en riesgo', $metricas->detectadosEnRiesgo())
                ->description('Prioridad Moderada, Alta o Urgente, o agudeza pendiente de confirmar')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Casos en seguimiento', $porEstatus['En seguimiento'])
                ->description($porEstatus['Abandonó'].' abandonaron el seguimiento')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),

            Stat::make('Casos cerrados', $porEstatus['Cerrado satisfactorio'])
                ->description($porEstatus['Cerrado no atendido'].' cerrados sin atender')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($porEstatus['Cerrado no atendido'] > 0 ? 'warning' : 'success'),

            Stat::make('Casos derivados', $canalizados)
                ->description($referencias.' con formato de referencia enviado')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color($canalizados > 0 ? 'danger' : 'gray'),

            Stat::make('Citas asignadas por Salud', $metricas->referenciasConCita())
                ->description($sinCita > 0 ? $sinCita.' referencias esperan cita' : 'Sin referencias pendientes')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($sinCita > 0 ? 'warning' : 'success'),
        ];
    }
}
