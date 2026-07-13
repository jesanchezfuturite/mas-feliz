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
                            ])
                            ->required()
                            ->live(),
                        
                        Forms\Components\Select::make('seccion')
                            ->label('Sección del Tablero')
                            ->options([
                                'prevencion_promocion' => 'Prevención y Promoción',
                            ])
                            ->default('prevencion_promocion')
                            ->required(),

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
                        default => 'gray',
                    }),
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
