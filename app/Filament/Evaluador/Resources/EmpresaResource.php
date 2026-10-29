<?php

namespace App\Filament\Evaluador\Resources;

use App\Filament\Evaluador\Resources\EmpresaResource\Pages;
use App\Filament\Resources\Empresas\Schemas\DictamenForm;
use App\Mail\VisitaAgendadaMail;
use App\Models\Empresa;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Auditoría';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('evaluadores', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Los mismos cuatro campos que llena Gobierno, tal cual: el
                // evaluador dictamina con ese vocabulario, no con uno propio.
                Section::make('Dictamen y Auditoría')
                    ->description('Gestiona el estatus del distintivo y nivel de madurez para esta empresa.')
                    ->schema(DictamenForm::getSchema())
                    ->columns(2),

                Section::make('Información de la Empresa (Lectura)')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('nombre_empresa')
                            ->disabled(),
                        Forms\Components\TextInput::make('rfc')
                            ->disabled(),
                        Forms\Components\TextInput::make('municipio')
                            ->disabled(),
                        Forms\Components\TextInput::make('numero_trabajadores')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre_empresa')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('municipio')
                    ->label('Municipio')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estatus_distintivo')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Validado' => 'success',
                        'En revisión' => 'warning',
                        'Rechazado', 'No aprobado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estatus_distintivo')
                    ->label('Estatus')
                    // Los tres vigentes más 'Validado', que es el que pone la
                    // validación de la autoevaluación y sigue en la base.
                    ->options(Empresa::opcionesEstatusDistintivo('Validado')),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                // El evaluador dictamina desde el listado, igual que el admin.
                DictamenForm::accion()
                    ->iconButton()
                    ->tooltip('Emitir o corregir el dictamen'),
                // El acompañante es quien sigue de cerca el avance de la empresa,
                // así que puede mover la Ruta Crítica sin depender del administrador.
                Action::make('certificarFase')
                    ->label('Actualizar Fase')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Actualizar fase de la Ruta Crítica')
                    ->modalHeading('Ruta Crítica de Avance')
                    ->modalDescription(fn (Empresa $record) => 'La empresa verá esta fase en su escritorio: '.$record->nombre_empresa)
                    ->modalSubmitActionLabel('Guardar fase')
                    ->fillForm(fn (Empresa $record) => ['paso_certificacion' => $record->paso_certificacion])
                    ->form([
                        Forms\Components\Select::make('paso_certificacion')
                            ->label('Fase actual de certificación')
                            ->options(Empresa::opcionesPasoCertificacion())
                            ->required(),
                    ])
                    ->action(function (Empresa $record, array $data): void {
                        $record->update(['paso_certificacion' => $data['paso_certificacion']]);

                        Notification::make()
                            ->title('Fase actualizada')
                            ->body('La empresa ya ve la fase «'.(Empresa::PASOS_CERTIFICACION[$data['paso_certificacion']] ?? '').'» en su escritorio.')
                            ->success()
                            ->send();
                    }),
                Action::make('agendarVisita')
                    ->label('Agendar Visita')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->form([
                        Forms\Components\DateTimePicker::make('fecha_visita_presencial')
                            ->label('Fecha y Hora de la Visita')
                            ->required()
                            ->seconds(false)
                            ->displayFormat('d/m/Y h:i A')
                            ->default(fn ($record) => $record->fecha_visita_presencial),
                    ])
                    ->action(function (Empresa $record, array $data): void {
                        $record->update([
                            'fecha_visita_presencial' => $data['fecha_visita_presencial'],
                        ]);

                        try {
                            Mail::to($record->correo)
                                ->send(new VisitaAgendadaMail($record));

                            Notification::make()
                                ->title('Visita agendada y correo enviado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Visita agendada pero el correo falló')
                                ->warning()
                                ->body('Error: '.$e->getMessage())
                                ->send();
                        }
                    })
                    ->iconButton()
                    ->tooltip('Agendar Visita Presencial'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
            'view' => Pages\ViewEmpresa::route('/{record}'),
        ];
    }
}
