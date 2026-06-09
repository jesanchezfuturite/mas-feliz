<?php

namespace App\Filament\Empresa\Resources\Autoevaluacions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AutoevaluacionForm
{
    public static function configure(Schema $schema): Schema
    {
        $options = [
            '10' => 'Sí (10)',
            '5' => 'En proceso (5)',
            '0' => 'No (0)',
            'NA' => 'No aplica',
        ];

        // Helper function to build criteria Select fields programmatically
        $makeSelect = fn ($num) => Select::make("criterio_{$num}")
            ->label("Criterio {$num}")
            ->options($options)
            ->required()
            ->selectablePlaceholder(false);

        return $schema
            ->components([
                Section::make('Enfoque 1. Liderazgo, Política y Prevención de Riesgos (+Prevención)')
                    ->schema([
                        $makeSelect(1),
                        $makeSelect(2),
                        $makeSelect(3),
                        $makeSelect(4),
                        $makeSelect(5),
                        $makeSelect(6),
                    ])
                    ->columns(2),

                Section::make('Enfoque 2. Cuidado, Salud Emocional y Promoción del Bienestar (+Salud)')
                    ->schema([
                        $makeSelect(7),
                        $makeSelect(8),
                        $makeSelect(9),
                        $makeSelect(10),
                        $makeSelect(11),
                        $makeSelect(12),
                    ])
                    ->columns(2),

                Section::make('Enfoque 3. Desarrollo Humano, Formación y Comunicación (+Desarrollo)')
                    ->schema([
                        $makeSelect(13),
                        $makeSelect(14),
                        $makeSelect(15),
                        $makeSelect(16),
                        $makeSelect(17),
                        $makeSelect(18),
                    ])
                    ->columns(2),

                Section::make('Enfoque 4. Condiciones de trabajo, bienestar y entorno psicosocial (+Bienestar)')
                    ->schema([
                        $makeSelect(19),
                        $makeSelect(20),
                        $makeSelect(21),
                        $makeSelect(22),
                        $makeSelect(23),
                        $makeSelect(24),
                        $makeSelect(25),
                    ])
                    ->columns(2),
            ]);
    }
}
