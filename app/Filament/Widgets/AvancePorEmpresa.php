<?php

namespace App\Filament\Widgets;

use App\Models\Empresa;
use App\Support\PrioridadAtencion;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Avance por organización, para dar seguimiento sin abrir empresa por empresa.
 *
 * Solo cifras: cuántos tamizajes llevan, cuántas personas salieron en riesgo,
 * cuántos casos tienen abiertos y cuántos derivaron. Ningún dato del
 * colaborador aparece aquí.
 */
class AvancePorEmpresa extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    /**
     * Empresas que entran en el listado. null = todas.
     *
     * @return array<int>|null
     */
    protected function empresaIds(): ?array
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Avance por organización')
            ->description('Cifras de seguimiento. No incluye datos personales de los colaboradores.')
            ->query(
                Empresa::query()
                    ->when($this->empresaIds() !== null, fn (Builder $q) => $q->whereIn('id', $this->empresaIds()))
                    ->withCount([
                        'tamizajes',
                        'tamizajes as participaron_count' => fn (Builder $q) => $q->where('nivel_riesgo_general', '!=', PrioridadAtencion::NO_PARTICIPO),
                        'tamizajes as declinaron_count' => fn (Builder $q) => $q->where('nivel_riesgo_general', PrioridadAtencion::NO_PARTICIPO),
                        'tamizajes as en_riesgo_count' => fn (Builder $q) => $q->whereIn('nivel_riesgo_general', PrioridadAtencion::REQUIEREN_ATENCION),
                        'casosSeguimiento as casos_abiertos_count' => fn (Builder $q) => $q->where('estatus_atencion', 'En seguimiento'),
                        'casosSeguimiento as casos_canalizados_count' => fn (Builder $q) => $q->where('estatus_atencion', 'Canalizado'),
                        'solicitudesReferencia as referencias_count',
                        'solicitudesReferencia as citas_count' => fn (Builder $q) => $q->whereNotNull('fecha_cita'),
                    ])
            )
            ->columns([
                TextColumn::make('nombre_empresa')
                    ->label('Organización')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn ($record) => $record->municipio),

                TextColumn::make('numero_trabajadores')
                    ->label('Colaboradores')
                    ->alignCenter()
                    ->sortable()
                    ->placeholder('Sin declarar')
                    ->description('Universo registrado'),

                // El porcentaje se calcula sobre el universo que la organización
                // declaró al registrarse, contando también a quien declinó: es lo
                // que pidió Angélica y es la lectura que se compara con la meta
                // del 90%. Antes se dividía entre los propios tamizajes, con lo
                // que medía consentimiento y no avance.
                TextColumn::make('tamizajes_count')
                    ->label('Tamizajes')
                    ->alignCenter()
                    ->sortable()
                    ->description(function ($record) {
                        $universo = (int) $record->numero_trabajadores;

                        if ($universo <= 0) {
                            return 'Sin universo declarado';
                        }

                        return round($record->tamizajes_count * 100 / $universo).'% participó';
                    }),

                TextColumn::make('participaron_count')
                    ->label('Consintieron')
                    ->alignCenter()
                    ->sortable()
                    ->description(fn ($record) => $record->declinaron_count.' declinaron'),

                TextColumn::make('en_riesgo_count')
                    ->label('En riesgo')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),

                TextColumn::make('casos_abiertos_count')
                    ->label('Casos en seguimiento')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('casos_canalizados_count')
                    ->label('Derivados')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('citas_count')
                    ->label('Citas asignadas')
                    ->alignCenter()
                    ->sortable()
                    ->description(fn ($record) => $record->referencias_count.' referencias'),

                TextColumn::make('estatus_distintivo')
                    ->label('Distintivo')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Validado', 'Aprobado' => 'success',
                        'En revisión' => 'warning',
                        'Rechazado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('en_riesgo_count', 'desc')
            ->paginated([10, 25, 50]);
    }
}
