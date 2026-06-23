<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Autoevaluacion;
use Illuminate\Database\Eloquent\Model;

class AutoevaluacionStatsWidget extends StatsOverviewWidget
{
    public ?Model $record = null;
    public ?array $respuestasState = null;

    protected ?string $pollingInterval = null;

    protected $listeners = ['formUpdated'];

    public function mount(): void
    {
        if ($this->record) {
            $this->respuestasState = $this->record->respuestas ?? [];
        }
    }

    public function formUpdated(array $respuestas): void
    {
        $this->respuestasState = $respuestas;
    }

    protected function getStats(): array
    {
        $respuestas = $this->respuestasState ?? ($this->record ? ($this->record->respuestas ?? []) : []);
        $estatus = $this->record ? ($this->record->estatus ?? 'Borrador') : 'Borrador';

        // 1. Calculate sum
        $sum = 0;
        if (is_array($respuestas)) {
            foreach ($respuestas as $criterioKey => $criterio) {
                if (is_array($criterio)) {
                    foreach ($criterio as $key => $elemento) {
                        if ($key === 'status' || $key === 'feedback') {
                            continue;
                        }
                        if (is_array($elemento) && isset($elemento['score']) && is_numeric($elemento['score'])) {
                            $sum += (int) $elemento['score'];
                        }
                    }
                }
            }
        }

        // 2. Check Indispensable criteria
        $indispensableIds = [4, 9, 10, 15, 16];
        $indispensablesCount = 0;
        $criterioElementsCount = [
            4 => 3,
            9 => 5,
            10 => 5,
            15 => 3,
            16 => 5,
        ];

        foreach ($indispensableIds as $id) {
            $met = true;
            $numElements = $criterioElementsCount[$id];
            for ($e = 1; $e <= $numElements; $e++) {
                $score = $respuestas["criterio_{$id}"]["elemento_{$e}"]['score'] ?? null;
                if ($score !== '10' && $score !== 'NA') {
                    $met = false;
                    break;
                }
            }
            if ($met) {
                $indispensablesCount++;
            }
        }

        // 3. Compute predictive maturity level
        $cumpleIndispensables = ($indispensablesCount === 5);
        $madurez = 'Inicial';
        $madurezDesc = 'Menos de 100 puntos';
        $color = 'danger';

        if ($cumpleIndispensables) {
            if ($sum >= 180) {
                $madurez = 'Excelencia';
                $madurezDesc = '180 puntos o más + Indispensables';
                $color = 'success';
            } elseif ($sum >= 100) {
                $madurez = 'Avanzado';
                $madurezDesc = '100 a 179 puntos + Indispensables';
                $color = 'warning';
            }
        } else {
            $madurezDesc = 'Requiere cumplir todos los indispensables';
        }

        $isAdmin = filament()->getCurrentPanel()?->getId() === 'admin';

        if (! $isAdmin) {
            return [
                Stat::make('Criterios Indispensables', "{$indispensablesCount} de 5")
                    ->description($cumpleIndispensables ? '¡Todos los obligatorios cumplidos!' : 'Faltan indispensables por cumplir')
                    ->descriptionIcon($cumpleIndispensables ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-circle')
                    ->color($cumpleIndispensables ? 'success' : 'danger')
                    ->view('filament.widgets.custom-stat'),
            ];
        }

        return [
            Stat::make('Puntaje Total', "{$sum} pts")
                ->description('Puntos acumulados en autoevaluación')
                ->descriptionIcon('heroicon-m-numbered-list')
                ->color($sum >= 180 ? 'success' : ($sum >= 100 ? 'warning' : 'danger'))
                ->view('filament.widgets.custom-stat'),

            Stat::make('Madurez Predictiva', $madurez)
                ->description($madurezDesc)
                ->descriptionIcon($cumpleIndispensables ? 'heroicon-m-academic-cap' : 'heroicon-m-shield-exclamation')
                ->color($color)
                ->view('filament.widgets.custom-stat'),

            Stat::make('Criterios Indispensables', "{$indispensablesCount} de 5")
                ->description($cumpleIndispensables ? '¡Todos los obligatorios cumplidos!' : 'Faltan indispensables por cumplir')
                ->descriptionIcon($cumpleIndispensables ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-circle')
                ->color($cumpleIndispensables ? 'success' : 'danger')
                ->view('filament.widgets.custom-stat'),
        ];
    }
}
