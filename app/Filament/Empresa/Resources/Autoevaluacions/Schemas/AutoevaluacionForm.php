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

        $criteriosLabels = [
            1 => 'Existencia de una política/declaración interna para promover la Salud Mental aprobada por dirección, con objetivos, alcance y responsabilidades.',
            2 => 'Planes y programas de salud mental que incluyen acciones de prevención, cuidado y fortalecimiento de la salud mental vinculados a la política, con metas y seguimiento.',
            3 => 'Comité de Salud Mental multidisciplinario o responsable/enlace institucional.',
            4 => 'Medición anual del clima laboral y cultura organizacional con informe y plan de mejora.',
            5 => 'Participación activa de trabajadores operativos en el comité de salud mental , elaboración/revisión y seguimiento de planes .',
            6 => 'Elaboración y difusión de lineamientos de cohesión, inclusión, no discriminación y mecanismos seguros de denuncia por violencia laboral.',
            7 => 'La organización cuenta con un protocolo documentado de manejo, intervención y postvención en crisis psicológicas, que incluye rutas de atención, responsables, tiempos de respuesta y mecanismos de seguimiento.',
            8 => 'La organización cuenta con un buzón confidencial (físico o digital) para recibir inquietudes, solicitudes de apoyo o reportes relacionados con bienestar emocional y violencia laboral.',
            9 => 'Evaluación e identificación de riesgos psicosociales mediante herramientas validadas (liderazgo negativo, relaciones, violencia).',
            10 => 'Acciones de promoción y prevención según riesgos identificados, integradas a la política de salud mental.',
            11 => 'Exámenes médicos periódicos con evaluación psicológica en personal con riesgos, signos de alarma o quejas.',
            12 => 'Programa de prevención de adicciones incluido en el plan de salud mental.',
            13 => 'Directorio actualizado de recursos de apoyo para la atención 24/7 de pacientes en riesgo accesible a todo el personal.',
            14 => 'La organización cuenta con un programa de capacitación al personal clave (mandos medios), en Primeros Auxilios Psicológicos (PAP), incluyendo identificación de signos de alarma, contención inicial y canalización segura.',
            15 => 'La organización garantiza acceso a servicios de atención psicológica interna o externa, mediante convenios, prestación directa o contratación de especialistas, asegurando confidencialidad y oportunidad.',
            16 => 'Difusión de alfabetización en salud mental, autocuidado, estrés, estigma, violencia laboral y bienestar emocional.',
            17 => 'Difusión de política de cero tolerancia a maltrato, hostigamiento, discriminación y acoso.',
            18 => 'Promoción de relaciones de respeto y cordialidad entre compañeros y mandos.',
            19 => 'Programa de reconocimiento del esfuerzo del personal al menos de manera simbólica.',
            20 => 'La organización instala y mantiene señalética de promoción de salud mental, prevención del estrés, canales de ayuda y fomento del autocuidado, visible en áreas comunes y operativas.',
            21 => 'Evaluación periódica de condiciones básicas del ambiente físico: seguridad, limpieza, iluminación, ventilación.',
            22 => 'Evaluación de la organización del trabajo acorde a las funciones asignadas, respeto a horarios laborales, pausas y tiempos de descanso',
            23 => 'Realización de actividades de bienestar, convivencia y recreación.',
            24 => 'La organización dispone de espacios físicos destinados al autocuidado, descanso activo o regulación emocional, accesibles para los trabajadores.',
            25 => 'La organización implementa un programa de pausas activas orientado al bienestar físico y mental y manejo del estrés.',
        ];

        // Helper function to build criteria Select fields programmatically
        $makeSelect = fn ($num) => \Filament\Schemas\Components\Grid::make(10)
            ->schema([
                \Filament\Forms\Components\Placeholder::make("label_{$num}")
                    ->content(new \Illuminate\Support\HtmlString(
                        '<div style="font-weight: 500; font-size: 0.875rem; color: #374151; padding-top: 0.3rem;">' . 
                        (isset($criteriosLabels[$num]) ? "{$num}. " . $criteriosLabels[$num] : "Criterio {$num}") . 
                        '</div>'
                    ))
                    ->hiddenLabel()
                    ->columnSpan(8),
                Select::make("criterio_{$num}")
                    ->options($options)
                    ->required()
                    ->default('10')
                    ->selectablePlaceholder(false)
                    ->hiddenLabel()
                    ->columnSpan(2),
            ])
            ->extraAttributes([
                'style' => 'align-items: flex-start; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: background-color 0.2s; background-color: ' . ($num % 2 === 0 ? '#f1f5f9' : 'transparent') . ';',
            ]);

        return $schema
            ->columns(1)
            ->components([
                Section::make(new \Illuminate\Support\HtmlString('Enfoque 1. Liderazgo, Política y Prevención de Riesgos <span style="float: right; color: #556ee6; font-weight: bold; font-size: 1.1rem;">(+Prevención)</span>'))
                    ->schema([
                        $makeSelect(1),
                        $makeSelect(2),
                        $makeSelect(3),
                        $makeSelect(4),
                        $makeSelect(5),
                        $makeSelect(6),
                    ])
                    ->columns(1),

                Section::make(new \Illuminate\Support\HtmlString('Enfoque 2. Cuidado, Salud Emocional y Promoción del Bienestar <span style="float: right; color: #556ee6; font-weight: bold; font-size: 1.1rem;">(+Salud)</span>'))
                    ->schema([
                        $makeSelect(7),
                        $makeSelect(8),
                        $makeSelect(9),
                        $makeSelect(10),
                        $makeSelect(11),
                        $makeSelect(12),
                    ])
                    ->columns(1),

                Section::make(new \Illuminate\Support\HtmlString('Enfoque 3. Desarrollo Humano, Formación y Comunicación <span style="float: right; color: #556ee6; font-weight: bold; font-size: 1.1rem;">(+Desarrollo)</span>'))
                    ->schema([
                        $makeSelect(13),
                        $makeSelect(14),
                        $makeSelect(15),
                        $makeSelect(16),
                        $makeSelect(17),
                        $makeSelect(18),
                    ])
                    ->columns(1),

                Section::make(new \Illuminate\Support\HtmlString('Enfoque 4. Condiciones de trabajo, bienestar y entorno psicosocial <span style="float: right; color: #556ee6; font-weight: bold; font-size: 1.1rem;">(+Bienestar)</span>'))
                    ->schema([
                        $makeSelect(19),
                        $makeSelect(20),
                        $makeSelect(21),
                        $makeSelect(22),
                        $makeSelect(23),
                        $makeSelect(24),
                        $makeSelect(25),
                    ])
                    ->columns(1),

                \Filament\Forms\Components\Placeholder::make('custom-css')
                    ->content(new \Illuminate\Support\HtmlString('<div style="display: none;"><style>
                        .fi-resource-create-record-page form, .fi-resource-edit-record-page form {
                            background-color: transparent !important;
                            padding: 0 !important;
                            box-shadow: none !important;
                        }
                        /* Reduce the massive gap between questions */
                        .fi-resource-create-record-page form .fi-section-content > .grid, 
                        .fi-resource-edit-record-page form .fi-section-content > .grid {
                            gap: 0 !important;
                        }
                    </style></div>'))
                    ->hiddenLabel()
                    ->columnSpan('full'),
            ]);
    }
}
