<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Tables;

use App\Models\CasoSeguimiento;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColumnGroup;
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

    /**
     * Escala de prioridad de atención. Vive en PrioridadAtencion porque la
     * comparten el tamizaje, el caso y el formato de referencia; aquí solo se
     * expone como opciones del selector.
     */
    public static function nivelesRiesgo(): array
    {
        return PrioridadAtencion::opciones();
    }

    /**
     * Las columnas van agrupadas bajo los títulos de sección de la fila 1 de
     * la hoja de Drive "ATENCIÓN EMPRESAS +FELIZ" (Angélica, 27/08/2026):
     * Datos de identificación, Resultados, Consentimiento, Estatus de
     * atención, Servicio y Solicitud de referencia complementaria.
     */
    public static function definicion(): array
    {
        return [
            ColumnGroup::make('Datos de identificación', [
                self::nombre(),

                self::selectDeTamizaje('edad', 'Rango de edad', self::RANGOS_EDAD),
                self::selectDeTamizaje('genero', 'Sexo', self::SEXO),
                self::selectDeTamizaje('actividad_trabajo', 'Funciones', self::FUNCIONES),

                // Angélica reportó el 21/08/2026 que estas tres columnas salían
                // vacías en los casos que vienen del tamizaje: se mostraban solo
                // desde la copia del caso, que nunca se llena. Van por el tamizaje
                // igual que edad, sexo y tiempo, así se arrastra lo que contestó
                // la persona.
                self::textoDeTamizaje('actividad_trabajo_otra', '¿Cuál función?')
                    ->toggleable(),

                self::selectDeTamizaje('tiempo_trabajando', 'Tiempo trabajando en la empresa', self::TIEMPO_TRABAJANDO),

                self::textoDeTamizaje('correo', 'Correo')
                    ->rules(['nullable', 'email', 'max:255']),

                // El tamizaje lo captura como `telefono`; el caso lo llama
                // `celular`, así que el origen se indica aparte.
                self::textoDeTamizaje('celular', 'Celular', 'telefono')
                    ->rules(['nullable', 'max:20']),
            ]),

            // Provienen del tamizaje, solo lectura.
            ColumnGroup::make('Resultados', [
                self::resultado('ansiedad', 'Síntomas de Ansiedad', fn ($t) => $t?->nivel_ansiedad),
                self::resultado('depresion', 'Síntomas de Depresión', fn ($t) => $t?->nivel_depresion),

                // El resultado del ASQ va completo (Angélica, 27/08/2026): el
                // título distingue la agudeza y la acción va en letra chica
                // debajo. Es despliegue — la columna del tamizaje sigue en dos
                // valores; el título agudo sale de ResultadoAsq.
                TextColumn::make('suicidio')
                    ->label('Indicadores de Conducta suicida')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->tamizaje ? ResultadoAsq::titulo($record->tamizaje) : 'N/A')
                    ->color(fn (string $state): string => $state === ResultadoAsq::TITULO_AGUDO
                        ? 'danger'
                        : ColorNivel::badge($state))
                    ->description(fn ($record) => $record->tamizaje ? ResultadoAsq::accion($record->tamizaje) : null),

                // El select se pinta con el color oficial de la prioridad, la
                // misma escala que colorea badges y gráficas.
                SelectColumn::make('nivel_riesgo_detectado')
                    ->label(PrioridadAtencion::ETIQUETA)
                    ->options(self::nivelesRiesgo())
                    ->selectablePlaceholder(false)
                    ->extraInputAttributes(fn ($record) => self::chipPrioridad($record->nivel_riesgo_detectado)),
            ]),

            ColumnGroup::make('Consentimiento', [
                SelectColumn::make('consentimiento')
                    ->label('Consentimiento')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ]),
            ]),

            ColumnGroup::make('Estatus de atención', [
                // El select se pinta como el chip del Drive de Angélica
                // (amarillo en seguimiento, verde cerrado atendido, rojo
                // cerrado no atendido...). Los colores viven en el modelo.
                SelectColumn::make('estatus_atencion')
                    ->label('Estatus de atención')
                    ->options(CasoSeguimiento::ESTATUS_ATENCION)
                    ->selectablePlaceholder(false)
                    ->extraInputAttributes(fn ($record) => array_filter([
                        'style' => CasoSeguimiento::estiloChipEstatus($record->estatus_atencion),
                    ])),
            ]),

            ColumnGroup::make('Servicio', [
                CheckboxColumn::make('servicio_medicina')->label('Medicina'),
                CheckboxColumn::make('servicio_psicologia')->label('Psicología'),
                CheckboxColumn::make('servicio_psiquiatria')->label('Psiquiatría'),
                CheckboxColumn::make('servicio_otro_activo')->label('Otro'),

                TextInputColumn::make('servicio_otro')
                    ->label('¿Cuál servicio?')
                    ->toggleable(),
            ]),

            ColumnGroup::make('Solicitud de referencia complementaria', [
                CheckboxColumn::make('referencia_secretaria_salud')
                    ->label('Secretaría de Salud'),

                // No está en el documento de Salud, pero existe desde antes y hay
                // casos con el dato capturado: se conserva oculta y se puede
                // mostrar desde el selector de columnas.
                TextInputColumn::make('institucion_canalizacion')
                    ->label('Institución de canalización')
                    ->toggleable(isToggledHiddenByDefault: true),
            ]),
        ];
    }

    /** Estilo del select de prioridad: el color oficial del nivel, en chip. */
    private static function chipPrioridad(?string $nivel): array
    {
        $hex = PrioridadAtencion::HEX[$nivel] ?? null;

        if (! $hex) {
            return [];
        }

        return ['style' => "background-color: {$hex}; color: #ffffff; border-color: {$hex}; border-radius: 9999px; font-weight: 600;"];
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
                    .'📅 Cita '.$solicitud->fecha_cita->format('d/m/Y H:i')
                    .($unidad ? ' · '.e($unidad) : '')
                    .'</span>'
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

    /**
     * Dato de texto que, si la persona contestó el tamizaje, se muestra tal
     * como lo capturó ahí y no se puede editar. En los casos capturados a mano
     * queda abierto.
     *
     * @param  string|null  $campoTamizaje  Columna del tamizaje, si se llama distinto.
     */
    private static function textoDeTamizaje(string $campo, string $etiqueta, ?string $campoTamizaje = null): TextInputColumn
    {
        $origen = $campoTamizaje ?? $campo;

        return TextInputColumn::make($campo)
            ->label($etiqueta)
            ->getStateUsing(fn ($record) => $record->tamizaje?->{$origen} ?: $record->{$campo})
            ->disabled(fn ($record) => filled($record->tamizaje?->{$origen}));
    }

    private static function resultado(string $nombre, string $etiqueta, callable $valor): TextColumn
    {
        return TextColumn::make($nombre)
            ->label($etiqueta)
            ->badge()
            ->getStateUsing(fn ($record) => $valor($record->tamizaje) ?? 'N/A')
            ->color(fn (string $state): string => ColorNivel::badge($state));
    }
}
