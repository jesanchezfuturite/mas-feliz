<?php

namespace App\Filament\Evaluador\Resources;

use App\Filament\Evaluador\Resources\EmpresaResource\Pages;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                \Filament\Schemas\Components\Section::make('Dictamen y Auditoría')
                    ->description('Gestiona el estatus del distintivo y nivel de madurez para esta empresa.')
                    ->schema([
                        Forms\Components\Select::make('estatus_distintivo')
                            ->label('Estatus del Distintivo')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'En revisión' => 'En revisión',
                                'Validado' => 'Validado',
                                'Rechazado' => 'Rechazado',
                            ])
                            ->required(),
                        Forms\Components\Select::make('nivel_madurez_asignado')
                            ->label('Nivel de Madurez')
                            ->options([
                                'Inicial' => 'Nivel 1: Inicial',
                                'Intermedio' => 'Nivel 2: En proceso',
                                'Excelencia' => 'Nivel 3: Sobresaliente',
                            ]),
                        Forms\Components\Textarea::make('retroalimentacion_gobierno')
                            ->label('Retroalimentación')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Información de la Empresa (Lectura)')
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
                        'Rechazado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estatus_distintivo')
                    ->label('Estatus')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En revisión' => 'En revisión',
                        'Validado' => 'Validado',
                        'Rechazado' => 'Rechazado',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\Action::make('agendarVisita')
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
                            \Illuminate\Support\Facades\Mail::to($record->correo)
                                ->send(new \App\Mail\VisitaAgendadaMail($record));

                            \Filament\Notifications\Notification::make()
                                ->title('Visita agendada y correo enviado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Visita agendada pero el correo falló')
                                ->warning()
                                ->body('Error: ' . $e->getMessage())
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
