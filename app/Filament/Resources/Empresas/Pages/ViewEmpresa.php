<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Resources\Empresas\EmpresaResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;

class ViewEmpresa extends ViewRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('emitir_dictamen')
                ->label('Emitir Dictamen')
                ->color('primary')
                ->form([
                    Select::make('estatus_distintivo')
                        ->label('Estatus del Distintivo')
                        ->options([
                            'Aprobado' => 'Aprobado',
                            'Rechazado' => 'Rechazado',
                        ])
                        ->required()
                        ->live(),

                    Select::make('nivel_madurez_asignado')
                        ->label('Nivel de Madurez Asignado')
                        ->options([
                            'Inicial' => 'Inicial',
                            'Intermedio' => 'Intermedio',
                            'Avanzado' => 'Avanzado',
                            'Excelencia' => 'Excelencia',
                        ])
                        ->visible(fn (Get $get) => $get('estatus_distintivo') === 'Aprobado')
                        ->required(fn (Get $get) => $get('estatus_distintivo') === 'Aprobado'),

                    Textarea::make('retroalimentacion_gobierno')
                        ->label('Retroalimentación del Gobierno')
                        ->placeholder('Justificación o comentarios. Si se rechaza, detalla qué mejorar para una futura revisión.')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'estatus_distintivo' => $data['estatus_distintivo'],
                        'nivel_madurez_asignado' => $data['estatus_distintivo'] === 'Aprobado' ? $data['nivel_madurez_asignado'] : null,
                        'retroalimentacion_gobierno' => $data['retroalimentacion_gobierno'],
                        'fecha_dictamen' => now(),
                    ]);

                    $this->refreshFormData([
                        'estatus_distintivo',
                        'nivel_madurez_asignado',
                        'retroalimentacion_gobierno',
                        'fecha_dictamen',
                    ]);
                }),
        ];
    }
}
