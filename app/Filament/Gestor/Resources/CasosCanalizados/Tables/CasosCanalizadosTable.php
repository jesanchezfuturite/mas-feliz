<?php

namespace App\Filament\Gestor\Resources\CasosCanalizados\Tables;

use App\Filament\Empresa\Resources\CasoSeguimientos\Schemas\DetalleCasoForm;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Respuesta a la pregunta de Salud: "¿cómo veremos los casos que ponen
 * Canalizar?". Concentra en una sola vista los casos canalizados de todas las
 * empresas, con el estado de su referencia. La comparten Gestor y admin.
 */
class CasosCanalizadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('identificador_empleado')
                    ->label('Persona')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => trim(collect([$record->genero, $record->edad])->filter()->implode(' · ')) ?: null),

                TextColumn::make('empresa.nombre_empresa')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('nivel_riesgo_detectado')
                    ->label(PrioridadAtencion::ETIQUETA)
                    ->badge()
                    ->color(fn (string $state): string => ColorNivel::badge($state))
                    ->sortable(),

                TextColumn::make('servicios')
                    ->label('Servicio')
                    ->getStateUsing(fn ($record) => $record->servicios_texto)
                    ->wrap(),

                TextColumn::make('institucion_canalizacion')
                    ->label('Institución')
                    ->placeholder('N/A')
                    ->wrap(),

                TextColumn::make('consentimiento')
                    ->label('Consentimiento')
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->consentimiento) {
                        true => 'Sí',
                        false => 'No',
                        default => 'Sin registrar',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Sí' => 'success',
                        'No' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('referencia')
                    ->label('Referencia a Salud')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $solicitud = $record->solicitudReferencia;
                        if (! $solicitud) {
                            return $record->referencia_secretaria_salud ? 'Solicitada, sin formato' : 'No requiere';
                        }

                        return $solicitud->esta_agendada
                            ? 'Cita: '.$solicitud->fecha_cita->format('d/m/Y H:i')
                            : 'Formato enviado ('.$solicitud->folio.')';
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'Cita:') => 'success',
                        str_starts_with($state, 'Formato enviado') => 'info',
                        $state === 'Solicitada, sin formato' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                // Angélica, 11/09/2026: "que el perfil de gestor pueda ver
                // todos los datos de los usuarios que le mandan (los que salen
                // en el apartado de Atención)". Es el mismo detalle que ve la
                // empresa, más la organización, porque aquí se cruzan varias.
                Action::make('verDetalle')
                    ->label('Ver detalle')
                    ->icon('heroicon-m-eye')
                    ->iconButton()
                    ->tooltip('Ver todos los datos de la persona')
                    ->color('gray')
                    ->modalHeading('Detalle de la persona canalizada')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->form(DetalleCasoForm::componentes(conEmpresa: true)),
            ])
            ->filters([
                SelectFilter::make('nivel_riesgo_detectado')
                    ->label(PrioridadAtencion::ETIQUETA)
                    ->options(PrioridadAtencion::opciones()),

                SelectFilter::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre_empresa')
                    ->searchable()
                    ->preload(),

                Filter::make('sin_referencia')
                    ->label('Marcados para referencia, sin formato')
                    ->query(fn ($query) => $query
                        ->where('referencia_secretaria_salud', true)
                        ->whereDoesntHave('solicitudReferencia')),
            ]);
    }
}
