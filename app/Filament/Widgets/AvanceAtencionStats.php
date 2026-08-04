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

    protected int | string | array $columnSpan = 'full';

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

        return [
            Stat::make('Tamizajes aplicados', $aplicados)
                ->description($metricas->porcentajeParticipacion() . '% participó · ' . $metricas->noParticiparon() . ' declinaron')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Personas detectadas en riesgo', $metricas->detectadosEnRiesgo())
                ->description('Tamizajes con riesgo moderado o urgente')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Casos en seguimiento', $metricas->casosAbiertos())
                ->description($metricas->casosCerrados() . ' cerrados satisfactoriamente')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),

            Stat::make('Casos derivados', $canalizados)
                ->description($referencias . ' con formato de referencia enviado')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color($canalizados > 0 ? 'danger' : 'gray'),

            Stat::make('Citas asignadas por Salud', $metricas->referenciasConCita())
                ->description($sinCita > 0 ? $sinCita . ' referencias esperan cita' : 'Sin referencias pendientes')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($sinCita > 0 ? 'warning' : 'success'),
        ];
    }
}
