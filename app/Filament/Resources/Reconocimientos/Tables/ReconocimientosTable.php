<?php

namespace App\Filament\Resources\Reconocimientos\Tables;

use App\Filament\Resources\Empresas\Schemas\ReconocimientoForm;
use App\Models\Empresa;
use App\Support\ExportacionReconocimientos;
use App\Support\RegionesCoahuila;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class ReconocimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Organizaciones que reciben reconocimiento')
            ->description('Solo aparecen las organizaciones aprobadas. La exportación y la descarga de logotipos respetan los filtros puestos.')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('evaluadores'))
            ->columns([
                TextColumn::make('logo_reconocimiento')
                    ->label('Logo')
                    ->state(fn (Empresa $record) => self::miniatura($record))
                    ->html()
                    ->alignCenter(),

                TextColumn::make('nombre_reconocimiento')
                    ->label('Organización')
                    ->state(fn (Empresa $record) => $record->nombre_para_reconocimiento)
                    // Si la empresa cambió su nombre, se deja a la vista el
                    // de registro: es la pista para ubicarla en Empresas.
                    ->description(fn (Empresa $record) => $record->nombre_para_reconocimiento !== $record->nombre_empresa
                        ? $record->folio.' · Registrada como: '.$record->nombre_empresa
                        : $record->folio)
                    ->searchable(['nombre_reconocimiento', 'nombre_empresa', 'folio'])
                    ->sortable(['nombre_empresa'])
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('municipio')
                    ->label('Municipio')
                    ->description(fn (Empresa $record) => $record->region ?? RegionesCoahuila::SIN_REGION)
                    ->sortable(),

                TextColumn::make('nivel_madurez_asignado')
                    ->label('Nivel')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Intermedio' => 'info',
                        'Avanzado' => 'primary',
                        'Excelencia' => 'success',
                        default => 'gray',
                    })
                    ->placeholder('Sin nivel')
                    ->sortable(),

                TextColumn::make('nombre_director')
                    ->label('Director/Gerente')
                    ->description(fn (Empresa $record) => self::contacto($record->cargo_director, $record->celular_director))
                    ->wrap(),

                TextColumn::make('nombre_responsable')
                    ->label('Enlace')
                    ->description(fn (Empresa $record) => self::contacto($record->cargo_enlace, $record->celular_enlace))
                    ->wrap(),

                TextColumn::make('acompanante')
                    ->label('Acompañante')
                    ->state(fn (Empresa $record) => ExportacionReconocimientos::acompanantes($record))
                    ->placeholder('Sin asignar')
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('reconocimiento_confirmado_at')
                    ->label('Datos')
                    ->badge()
                    ->state(fn (Empresa $record) => $record->reconocimiento_confirmado_at
                        ? 'Confirmados '.$record->reconocimiento_confirmado_at->format('d/m/Y')
                        : 'Pendientes')
                    ->color(fn (string $state): string => $state === 'Pendientes' ? 'warning' : 'success')
                    ->sortable(),
            ])
            ->defaultSort('municipio')
            ->filters([
                SelectFilter::make('region')
                    ->label('Región')
                    ->options(RegionesCoahuila::opciones())
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereIn('municipio', RegionesCoahuila::municipiosDe($data['value']))
                        : $query),

                SelectFilter::make('municipio')
                    ->label('Municipio')
                    ->options(fn () => Empresa::query()->aprobadas()->distinct()->orderBy('municipio')->pluck('municipio', 'municipio')->filter()->toArray()),

                SelectFilter::make('nivel_madurez_asignado')
                    ->label('Nivel de Madurez')
                    ->options(Empresa::opcionesNivelMadurez()),

                TernaryFilter::make('reconocimiento_confirmado_at')
                    ->label('Datos del reconocimiento')
                    ->placeholder('Todas')
                    ->trueLabel('Confirmados')
                    ->falseLabel('Pendientes')
                    ->nullable(),
            ])
            ->recordActions([
                Action::make('descargar_logo')
                    ->label('Descargar logotipo')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->color('gray')
                    ->tooltip('Descargar logotipo en alta resolución')
                    ->visible(fn (Empresa $record) => filled($record->logo_reconocimiento))
                    ->url(fn (Empresa $record) => '/storage/'.$record->logo_reconocimiento)
                    ->openUrlInNewTab(),

                ReconocimientoForm::accion()
                    ->iconButton()
                    ->tooltip('Capturar o corregir los datos del reconocimiento'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Cuando el nombre o el logotipo llegaron mal: la empresa
                    // vuelve a ver el candado y lo que ya había capturado.
                    BulkAction::make('pedir_de_nuevo')
                        ->label('Volver a pedir confirmación')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->modalDescription('Las organizaciones seleccionadas volverán a ver el candado en su escritorio hasta que confirmen sus datos. Lo que ya capturaron no se borra.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $records->each->update(['reconocimiento_confirmado_at' => null]);

                            Notification::make()->title('Se les pedirá confirmar de nuevo')->success()->send();
                        }),

                    // Para las que ya se entregaron con los datos recabados
                    // por fuera (Laguna): no tiene caso detenerles el resultado.
                    BulkAction::make('quitar_candado')
                        ->label('Quitar candado sin pedir datos')
                        ->icon('heroicon-o-lock-open')
                        ->requiresConfirmation()
                        ->modalDescription('Las organizaciones seleccionadas verán su resultado sin llenar el formulario. Úsalo para las que ya recibieron su reconocimiento con datos recabados por otro medio.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $records
                                ->filter(fn (Empresa $empresa) => blank($empresa->reconocimiento_confirmado_at))
                                ->each->update(['reconocimiento_confirmado_at' => now()]);

                            Notification::make()->title('Candado retirado')->success()->send();
                        }),
                ]),
            ]);
    }

    private static function contacto(?string $cargo, ?string $celular): ?string
    {
        return collect([$cargo, $celular])->filter()->implode(' · ') ?: null;
    }

    /**
     * La miniatura del logotipo. Va con estilos en línea porque las clases
     * de Tailwind de las vistas no llegan a los paneles; un PDF no se puede
     * previsualizar, así que se anuncia como tal.
     */
    private static function miniatura(Empresa $empresa): HtmlString
    {
        $logo = $empresa->logo_reconocimiento;

        if (blank($logo)) {
            return new HtmlString('<span style="font-size: 0.75rem; color: #9ca3af;">Pendiente</span>');
        }

        if (str_ends_with(strtolower($logo), '.pdf')) {
            return new HtmlString('<span style="font-size: 0.75rem; font-weight: 700; color: #b91c1c;">PDF</span>');
        }

        return new HtmlString(
            '<img src="/storage/'.e($logo).'" alt="Logotipo de '.e($empresa->nombre_para_reconocimiento).'" '
            .'style="max-width: 96px; max-height: 48px; object-fit: contain; display: inline-block;">'
        );
    }
}
