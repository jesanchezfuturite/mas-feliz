<?php

namespace App\Filament\Resources\Empresas;

use App\Filament\Resources\Empresas\Pages\CreateEmpresa;
use App\Filament\Resources\Empresas\Pages\EditEmpresa;
use App\Filament\Resources\Empresas\Pages\ListEmpresas;
use App\Filament\Resources\Empresas\Pages\ViewEmpresa;
use App\Filament\Resources\Empresas\Schemas\EmpresaForm;
use App\Filament\Resources\Empresas\Tables\EmpresasTable;
use App\Models\Empresa;
use BackedEnum;
use App\Filament\Resources\Empresas\RelationManagers;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    protected static ?string $navigationLabel = 'Empresas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-m-building-office';

    public static function form(Schema $schema): Schema
    {
        return EmpresaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Datos Generales')
                            ->headerActions([
                                \Filament\Actions\Action::make('icon')
                                    ->icon('heroicon-m-document-text')
                                    ->link()
                                    ->extraAttributes(['style' => 'pointer-events: none; margin-left: auto; color: #556ee6;'])
                                    ->label('')
                            ])
                            ->schema([
                                TextEntry::make('folio')
                                    ->label('Folio'),
                                TextEntry::make('nombre_empresa')
                                    ->label('Nombre de la Empresa'),
                                TextEntry::make('rubro')
                                    ->label('Rubro'),
                                TextEntry::make('numero_trabajadores')
                                    ->label('Número de Trabajadores'),
                            ])
                            ->columns(2),

                        \Filament\Schemas\Components\Section::make('Dictamen de Gobierno')
                            ->headerActions([
                                \Filament\Actions\Action::make('icon')
                                    ->icon('heroicon-m-shield-check')
                                    ->link()
                                    ->extraAttributes(['style' => 'pointer-events: none; margin-left: auto; color: #556ee6;'])
                                    ->label('')
                            ])
                            ->schema([
                                TextEntry::make('estatus_distintivo')
                                    ->label('Estatus del Distintivo')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'Aprobado' => 'success',
                                        'Rechazado' => 'danger',
                                        'En revisión' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('nivel_madurez_asignado')
                                    ->label('Nivel de Madurez Asignado')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'Inicial' => 'gray',
                                        'Intermedio' => 'info',
                                        'Avanzado' => 'primary',
                                        'Excelencia' => 'success',
                                        default => 'gray',
                                    })
                                    ->placeholder('Ninguno'),
                                TextEntry::make('fecha_dictamen')
                                    ->label('Fecha de Dictamen')
                                    ->dateTime()
                                    ->placeholder('No dictaminado'),
                                TextEntry::make('retroalimentacion_gobierno')
                                    ->label('Retroalimentación del Gobierno')
                                    ->placeholder('Sin comentarios'),
                            ])
                            ->columns(2),

                        \Filament\Schemas\Components\Section::make('Progreso de Tamizaje')
                            ->extraAttributes(['class' => 'equal-height-section'])
                            ->headerActions([
                                \Filament\Actions\Action::make('icon')
                                    ->icon('heroicon-m-chart-bar')
                                    ->link()
                                    ->extraAttributes(['style' => 'pointer-events: none; margin-left: auto; color: #556ee6;'])
                                    ->label('')
                            ])
                            ->schema([
                                TextEntry::make('total_tamizajes')
                                    ->label('Total de Tamizajes Realizados')
                                    ->state(function ($record) {
                                        $enLinea = $record->tamizajes()->count();
                                        $manuales = $record->casosSeguimiento()
                                            ->whereNotIn('identificador_empleado', function ($query) use ($record) {
                                                $query->select('nombre_completo')
                                                    ->from('tamizajes')
                                                    ->where('empresa_id', $record->id);
                                            })->count();
                                        return $enLinea + $manuales;
                                    })
                                    ->tooltip(function ($record) {
                                        $enLinea = $record->tamizajes()->count();
                                        $manuales = $record->casosSeguimiento()
                                            ->whereNotIn('identificador_empleado', function ($query) use ($record) {
                                                $query->select('nombre_completo')
                                                    ->from('tamizajes')
                                                    ->where('empresa_id', $record->id);
                                            })->count();
                                        return "En línea: {$enLinea} | Manuales: {$manuales}";
                                    }),
                                TextEntry::make('porcentaje_participacion')
                                    ->label('Porcentaje de Participación')
                                    ->state(function ($record) {
                                        $enLinea = $record->tamizajes()->count();
                                        $manuales = $record->casosSeguimiento()
                                            ->whereNotIn('identificador_empleado', function ($query) use ($record) {
                                                $query->select('nombre_completo')
                                                    ->from('tamizajes')
                                                    ->where('empresa_id', $record->id);
                                            })->count();
                                        $evaluados = $enLinea + $manuales;
                                        $trabajadores = $record->numero_trabajadores ?: 1;
                                        return round(($evaluados / $trabajadores) * 100, 1) . '%';
                                    })
                                    ->badge()
                                    ->color(function ($state) {
                                        $percent = (float) rtrim($state, '%');
                                        return $percent >= 90 ? 'success' : 'warning';
                                    }),
                                TextEntry::make('riesgos_urgentes')
                                    ->label('Riesgos Urgentes')
                                    ->state(function ($record) {
                                        $enLinea = $record->tamizajes()->where('nivel_riesgo_general', 'Urgente')->count();
                                        $manuales = $record->casosSeguimiento()
                                            ->where('nivel_riesgo_detectado', 'Urgente')
                                            ->whereNotIn('identificador_empleado', function ($query) use ($record) {
                                                $query->select('nombre_completo')
                                                    ->from('tamizajes')
                                                    ->where('empresa_id', $record->id);
                                            })->count();
                                        return $enLinea + $manuales;
                                    })
                                    ->badge()
                                    ->color('danger'),
                                TextEntry::make('distribucion_riesgos')
                                    ->label('Distribución General de Riesgos')
                                    ->state(function ($record) {
                                        $counts = $record->tamizajes()
                                            ->selectRaw('nivel_riesgo_general, count(*) as total')
                                            ->groupBy('nivel_riesgo_general')
                                            ->pluck('total', 'nivel_riesgo_general')
                                            ->toArray();

                                        $manualCounts = $record->casosSeguimiento()
                                            ->whereNotIn('identificador_empleado', function ($query) use ($record) {
                                                $query->select('nombre_completo')
                                                    ->from('tamizajes')
                                                    ->where('empresa_id', $record->id);
                                            })
                                            ->selectRaw('nivel_riesgo_detectado, count(*) as total')
                                            ->groupBy('nivel_riesgo_detectado')
                                            ->pluck('total', 'nivel_riesgo_detectado')
                                            ->toArray();

                                        $leve = ($counts['Leve'] ?? 0) + ($manualCounts['Leve'] ?? 0);
                                        $moderado = ($counts['Moderado'] ?? 0) + ($manualCounts['Moderado'] ?? 0);
                                        $urgente = ($counts['Urgente'] ?? 0) + ($manualCounts['Urgente'] ?? 0);
                                        return "Leve: {$leve} | Moderado: {$moderado} | Urgente: {$urgente}";
                                    }),
                            ])
                            ->columns(2),

                        \Filament\Schemas\Components\Section::make('Casos Clínicos')
                            ->extraAttributes(['class' => 'equal-height-section'])
                            ->headerActions([
                                \Filament\Actions\Action::make('icon')
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->link()
                                    ->extraAttributes(['style' => 'pointer-events: none; margin-left: auto; color: #556ee6;'])
                                    ->label('')
                            ])
                            ->schema([
                                TextEntry::make('total_casos')
                                    ->label('Total de Casos Clínicos en Bitácora')
                                    ->state(fn ($record) => $record->casosSeguimiento()->count()),
                                TextEntry::make('casos_canalizados')
                                    ->label('Casos Canalizados a Instituciones')
                                    ->state(fn ($record) => $record->casosSeguimiento()->where('estatus_atencion', 'Canalizado')->count()),
                            ])
                            ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return EmpresasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TamizajesRelationManager::class,
            RelationManagers\CasosSeguimientoRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\EmpresaStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmpresas::route('/'),
            'create' => CreateEmpresa::route('/create'),
            'view' => ViewEmpresa::route('/{record}'),
            'edit' => EditEmpresa::route('/{record}/edit'),
        ];
    }
}
