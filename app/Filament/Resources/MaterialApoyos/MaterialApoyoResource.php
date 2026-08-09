<?php

namespace App\Filament\Resources\MaterialApoyos;

use App\Filament\Resources\MaterialApoyos\Pages;
use App\Models\MaterialApoyo;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class MaterialApoyoResource extends Resource
{
    protected static ?string $model = MaterialApoyo::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Contenidos';
    protected static ?string $navigationLabel = 'Materiales de Apoyo';
    protected static ?string $modelLabel = 'Material de Apoyo';
    protected static ?string $pluralModelLabel = 'Materiales de Apoyo';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Recurso')
                    ->columnSpanFull() // Forzar que la sección ocupe todo el ancho del modal
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título del Recurso')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Recurso')
                            ->options([
                                'pdf' => 'Documento PDF',
                                'imagen' => 'Imagen / Flyer',
                                'video' => 'Video (YouTube/Vimeo)',
                                'enlace' => 'Enlace Externo',
                                'aviso' => 'Aviso / Fecha de capacitación',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('seccion')
                            ->label('Sección del Tablero')
                            ->options([
                                'prevencion_promocion' => 'Prevención y Promoción',
                                'crisis' => 'Crisis',
                                'capacitacion' => 'Capacitación',
                            ])
                            ->default('prevencion_promocion')
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->helperText('Opcional. En los avisos se usa para el detalle: duración, temario, sede o instrucciones.')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        // Un aviso no lleva archivo ni enlace: comunica una fecha.
                        Forms\Components\DateTimePicker::make('fecha_evento')
                            ->label('Fecha y hora de la capacitación')
                            ->displayFormat('d/m/Y h:i A')
                            ->seconds(false)
                            ->required(fn (Get $get) => $get('tipo') === 'aviso')
                            ->visible(fn (Get $get) => $get('tipo') === 'aviso')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('enlace_url')
                            ->label('URL del Enlace / Video')
                            ->url()
                            ->required()
                            ->visible(fn (Get $get) => in_array($get('tipo'), ['video', 'enlace']))
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('archivo_path')
                            ->label('Cargar Archivo')
                            ->disk('public')
                            ->directory('material-apoyo')
                            ->required()
                            ->maxSize(51200)
                            ->visible(fn (Get $get) => in_array($get('tipo'), ['pdf', 'imagen']))
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Recurso Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pdf' => 'danger',
                        'imagen' => 'success',
                        'video' => 'warning',
                        'enlace' => 'info',
                        'aviso' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('seccion')
                    ->label('Sección')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'prevencion_promocion' => 'Prevención y Promoción',
                        'crisis' => 'Crisis',
                        'capacitacion' => 'Capacitación',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_evento')
                    ->label('Fecha del evento')
                    ->dateTime('d/m/Y h:i A')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'pdf' => 'PDF',
                        'imagen' => 'Imagen',
                        'video' => 'Video',
                        'enlace' => 'Enlace',
                        'aviso' => 'Aviso',
                    ]),
                Tables\Filters\SelectFilter::make('seccion')
                    ->label('Sección')
                    ->options([
                        'prevencion_promocion' => 'Prevención y Promoción',
                        'crisis' => 'Crisis',
                        'capacitacion' => 'Capacitación',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMaterialApoyos::route('/'),
        ];
    }
}
