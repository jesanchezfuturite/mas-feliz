<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Tables;

use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\HtmlString;

/**
 * Columnas del listado de personas en riesgo.
 *
 * Siguen la jerarquía del documento "ATENCIÓN EMPRESAS +FELIZ": identificación,
 * resultados, consentimiento, estatus de atención, servicio y solicitud de
 * referencia. La captura es en línea —Angélica pidió expresamente que no fuera
 * en ventanas modales—, salvo el formato de referencia, que sí es modal.
 *
 * Los datos que vienen del tamizaje no se pueden editar: son las respuestas
 * anónimas de la persona y la empresa no debe alterarlas. En los casos
 * capturados a mano sí quedan abiertos.
 */
class Columnas
{
    public const RANGOS_EDAD = [
        'Menor de 18 años' => 'Menor de 18 años',
        '18 a 24 años' => '18 a 24 años',
        '25 a 34 años' => '25 a 34 años',
        '35 a 44 años' => '35 a 44 años',
        '45 a 54 años' => '45 a 54 años',
        '55 años o más' => '55 años o más',
    ];

    public const SEXO = [
        'Hombre' => 'Hombre',
        'Mujer' => 'Mujer',
    ];

    public const FUNCIONES = [
        'Operativas' => 'Operativas',
        'Administrativas' => 'Administrativas',
        'Técnicas' => 'Técnicas',
        'Profesionales especializadas' => 'Profesionales especializadas',
        'Supervisión o coordinación' => 'Supervisión o coordinación',
        'Dirección o gerencia' => 'Dirección o gerencia',
        'Atención directa al público, usuarios o clientes' => 'Atención directa al público, usuarios o clientes',
        'Otra' => 'Otra',
    ];

    public const TIEMPO_TRABAJANDO = [
        'Menos de 6 meses' => 'Menos de 6 meses',
        'De 6 meses a 1 año' => 'De 6 meses a 1 año',
        'Más de 1 año a 3 años' => 'Más de 1 año a 3 años',
        'Más de 3 años a 5 años' => 'Más de 3 años a 5 años',
        'Más de 5 años' => 'Más de 5 años',
    ];

    public const NIVELES_RIESGO = [
        'Leve' => 'Leve',
        'Moderado' => 'Moderado',
        'Urgente' => 'Urgente',
    ];

    public static function definicion(): array
    {
        return [
            // --- DATOS DE IDENTIFICACIÓN ---
            self::nombre(),

            self::selectDeTamizaje('edad', 'Rango de edad', self::RANGOS_EDAD),
            self::selectDeTamizaje('genero', 'Sexo', self::SEXO),
            self::selectDeTamizaje('actividad_trabajo', 'Funciones', self::FUNCIONES),

            TextInputColumn::make('actividad_trabajo_otra')
                ->label('¿Cuál función?')
                ->disabled(fn ($record) => $record->es_de_tamizaje)
                ->toggleable(),

            self::selectDeTamizaje('tiempo_trabajando', 'Tiempo trabajando en la empresa', self::TIEMPO_TRABAJANDO),

            TextInputColumn::make('correo')
                ->label('Correo')
                ->rules(['nullable', 'email', 'max:255']),

            TextInputColumn::make('celular')
                ->label('Celular')
                ->rules(['nullable', 'max:20']),

            // --- RESULTADOS (provienen del tamizaje, solo lectura) ---
            self::resultado('ansiedad', 'Ansiedad', fn ($t) => $t?->nivel_ansiedad),
            self::resultado('depresion', 'Depresión', fn ($t) => $t?->nivel_depresion),
            self::resultado('suicidio', 'Ideación y riesgo suicida', fn ($t) => $t?->nivel_suicidio),

            SelectColumn::make('nivel_riesgo_detectado')
                ->label('Nivel de riesgo')
                ->options(self::NIVELES_RIESGO)
                ->selectablePlaceholder(false),

            // --- CONSENTIMIENTO ---
            SelectColumn::make('consentimiento')
                ->label('Consentimiento')
                ->options([
                    1 => 'Sí',
                    0 => 'No',
                ]),

            // --- ESTATUS DE ATENCIÓN ---
            SelectColumn::make('estatus_atencion')
                ->label('Estatus de atención')
                ->options(\App\Models\CasoSeguimiento::ESTATUS_ATENCION)
                ->selectablePlaceholder(false),

            // --- SERVICIO ---
            CheckboxColumn::make('servicio_medicina')->label('Medicina'),
            CheckboxColumn::make('servicio_psicologia')->label('Psicología'),
            CheckboxColumn::make('servicio_psiquiatria')->label('Psiquiatría'),
            CheckboxColumn::make('servicio_otro_activo')->label('Otro'),

            TextInputColumn::make('servicio_otro')
                ->label('¿Cuál servicio?')
                ->toggleable(),

            // --- SOLICITUD DE REFERENCIA COMPLEMENTARIA ---
            CheckboxColumn::make('referencia_secretaria_salud')
                ->label('Secretaría de Salud'),

            // No está en el documento de Salud, pero existe desde antes y hay
            // casos con el dato capturado: se conserva oculta y se puede
            // mostrar desde el selector de columnas.
            TextInputColumn::make('institucion_canalizacion')
                ->label('Institución de canalización')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * Nombre del colaborador. Es la columna que queda inmovilizada al hacer
     * scroll horizontal y la que avisa cuando Salud ya asignó la cita.
     */
    private static function nombre(): TextColumn
    {
        return TextColumn::make('identificador_empleado')
            ->label('Nombre')
            ->searchable()
            ->sortable()
            ->weight('medium')
            ->extraHeaderAttributes(['class' => 'col-nombre-fija'])
            ->extraCellAttributes(['class' => 'col-nombre-fija'])
            // Angélica pidió que la cita se vea directamente en el nombre, sin
            // tener que volver a abrir el formato de referencia.
            ->description(function ($record) {
                $solicitud = $record->solicitudReferencia;

                if (! $solicitud?->fecha_cita) {
                    return null;
                }

                $unidad = $solicitud->unidad_atencion_completa;

                return new HtmlString(
                    '<span style="display: inline-flex; align-items: center; gap: 0.25rem; background-color: #dcfce7; color: #15803d; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; white-space: nowrap;">'
                    . '📅 Cita ' . $solicitud->fecha_cita->format('d/m/Y H:i')
                    . ($unidad ? ' · ' . e($unidad) : '')
                    . '</span>'
                );
            });
    }

    /**
     * Dato de identificación del colaborador. La regla de cuál valor manda vive
     * en el modelo (CasoSeguimiento::datoIdentificacion); aquí solo se muestra
     * y se bloquea cuando proviene del cuestionario.
     */
    private static function selectDeTamizaje(string $campo, string $etiqueta, array $opciones): SelectColumn
    {
        return SelectColumn::make($campo)
            ->label($etiqueta)
            ->options($opciones)
            ->getStateUsing(fn ($record) => $record->datoIdentificacion($campo))
            ->disabled(fn ($record) => $record->es_de_tamizaje);
    }

    private static function resultado(string $nombre, string $etiqueta, callable $valor): TextColumn
    {
        return TextColumn::make($nombre)
            ->label($etiqueta)
            ->badge()
            ->getStateUsing(fn ($record) => $valor($record->tamizaje) ?? 'N/A')
            ->color(fn (string $state): string => match ($state) {
                'Grave', 'Moderadamente grave', 'Riesgo Agudo' => 'danger',
                'Moderada', 'Evaluación Adicional' => 'warning',
                'Leve', 'Mínima o sin ansiedad', 'Mínima o ausente', 'Negativo' => 'success',
                default => 'gray',
            });
    }
}
