<?php

namespace App\Filament\Resources\Autoevaluacions\Pages;

use App\Filament\Resources\AutoevaluacionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewAutoevaluacion extends ViewRecord
{
    protected static string $resource = AutoevaluacionResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $sum = 0;
        $respuestas = $this->record->respuestas ?? [];
        foreach ($respuestas as $criterio => $elementos) {
            if (is_array($elementos)) {
                foreach ($elementos as $elemento => $data) {
                    if (isset($data['score']) && is_numeric($data['score'])) {
                        $sum += (int) $data['score'];
                    }
                }
            }
        }

        return 'Ver Autoevaluación (Puntaje Total: ' . $sum . ')';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Empresa\Resources\Autoevaluacions\Widgets\AutoevaluacionStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('validar')
                ->label('Validar Autoevaluación')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Validar Autoevaluación')
                ->modalDescription('¿Estás seguro de que deseas validar esta autoevaluación?')
                ->modalSubmitActionLabel('Sí, validar')
                ->form([
                    \Filament\Forms\Components\Textarea::make('dictamen_final')
                        ->label('Dictamen Final')
                        ->placeholder('Escriba el dictamen o conclusiones de la evaluación...')
                        ->rows(4)
                        ->required(),
                ])
                ->visible(function ($record): bool {
                    if ($record->estatus !== 'En revisión') {
                        return false;
                    }

                    $indispensableIds = [1, 4, 9, 15, 16];
                    $respuestas = $record->respuestas ?? [];

                    foreach ($indispensableIds as $id) {
                        $status = $respuestas["criterio_{$id}"]['status'] ?? null;
                        if ($status !== 'Aprobado') {
                            return false;
                        }
                    }

                    return true;
                })
                ->action(function ($record, array $data) {
                    $dictamenFinal = $data['dictamen_final'];
                    
                    // 1. Calculate maturity level
                    $nivelMadurez = $this->calcularNivelMadurez($record);

                    // 2. Generate PDF Distinctive
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.distintivo', [
                        'autoevaluacion' => $record,
                        'nivelMadurez' => $nivelMadurez,
                        'dictamenFinal' => $dictamenFinal,
                    ])->setPaper('letter', 'landscape');

                    $empresaId = $record->empresa_id;
                    $folio = $record->empresa->folio ?? 'S-F';
                    $pdfFilename = "distintivos/empresa_{$empresaId}_folio_{$folio}.pdf";

                    // Save to public storage disk
                    \Illuminate\Support\Facades\Storage::disk('public')->put($pdfFilename, $pdf->output());

                    // 3. Update Autoevaluacion model
                    $respuestas = $record->respuestas ?? [];
                    $respuestas['dictamen_final'] = $dictamenFinal;
                    $respuestas['pdf_distintivo'] = $pdfFilename;
                    
                    $record->update([
                        'estatus' => 'Validado',
                        'respuestas' => $respuestas,
                    ]);

                    // 4. Update Empresa model
                    $empresa = $record->empresa;
                    if ($empresa) {
                        $empresa->update([
                            'estatus_distintivo' => 'Validado',
                            'nivel_madurez_asignado' => $nivelMadurez,
                            'retroalimentacion_gobierno' => $dictamenFinal,
                            'fecha_dictamen' => now(),
                        ]);
                    }

                    // 5. Send Email to the company
                    $emailDestino = $record->empresa->correo ?? null;
                    if ($emailDestino) {
                        \Illuminate\Support\Facades\Mail::to($emailDestino)->send(
                            new \App\Mail\DistintivoAprobadoMail($record, $nivelMadurez, $dictamenFinal, $pdfFilename)
                        );
                    }

                    // 6. Filament Success Notification
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Dictamen emitido y distintivo generado')
                        ->send();
                }),
            Action::make('devolver')
                ->label('Devolver para Corrección')
                ->color('danger')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->requiresConfirmation()
                ->modalHeading('Devolver para Corrección')
                ->modalDescription('¿Estás seguro de que deseas devolver esta autoevaluación a Borrador para que la empresa realice correcciones?')
                ->modalSubmitActionLabel('Sí, devolver')
                ->visible(fn ($record) => $record->estatus === 'En revisión')
                ->action(function ($record) {
                    $record->update(['estatus' => 'Borrador']);

                    $emailDestino = $record->empresa->correo ?? null;
                    if ($emailDestino) {
                        \Illuminate\Support\Facades\Mail::to($emailDestino)->send(new \App\Mail\AutoevaluacionDevueltaMail($record));
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Autoevaluación devuelta a borrador para correcciones')
                        ->danger()
                        ->send();
                }),
            Action::make('descargar_distintivo')
                ->label('Ver Distintivo')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn ($record) => !empty($record->respuestas['pdf_distintivo'] ?? null) ? '/storage/' . $record->respuestas['pdf_distintivo'] : null)
                ->openUrlInNewTab()
                ->visible(fn ($record) => $record->estatus === 'Validado' && !empty($record->respuestas['pdf_distintivo'] ?? null)),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            \App\Filament\Resources\Empresas\EmpresaResource::getUrl('index') => 'Empresas',
            '#' => $this->record->empresa->nombre_empresa ?? 'Empresa',
            '' => 'Autoevaluación',
        ];
    }

    private function calcularNivelMadurez($record): string
    {
        $respuestas = $record->respuestas ?? [];
        $sum = 0;
        if (is_array($respuestas)) {
            foreach ($respuestas as $criterioKey => $criterio) {
                if (is_array($criterio)) {
                    foreach ($criterio as $key => $elemento) {
                        if ($key === 'status' || $key === 'feedback') {
                            continue;
                        }
                        if (is_array($elemento) && isset($elemento['score']) && is_numeric($elemento['score'])) {
                            $sum += (int) $elemento['score'];
                        }
                    }
                }
            }
        }

        $indispensableIds = [1, 4, 9, 15, 16];
        $criterioElementsCount = [
            1 => 7,
            4 => 7,
            9 => 7,
            15 => 6,
            16 => 5,
        ];

        $cumpleIndispensables = true;
        foreach ($indispensableIds as $id) {
            $numElements = $criterioElementsCount[$id];
            for ($e = 1; $e <= $numElements; $e++) {
                $score = $respuestas["criterio_{$id}"]["elemento_{$e}"]['score'] ?? null;
                if ($score !== '10' && $score !== 'NA') {
                    $cumpleIndispensables = false;
                    break 2;
                }
            }
        }

        if ($cumpleIndispensables) {
            if ($sum >= 180) {
                return 'Excelencia';
            } elseif ($sum >= 100) {
                return 'Avanzado';
            }
        }

        return 'Inicial';
    }
}
