<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Schemas;

use App\Models\Tamizaje;
use App\Support\CatalogoUnidadesAtencion;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

/**
 * Formato de referencia complementaria a Secretaría de Salud.
 *
 * La empresa captura la solicitud; el Gestor (trabajador social) y el admin
 * son los únicos que pueden asignar fecha de cita y unidad de atención, que
 * después la empresa ve en su propio tablero.
 *
 * El mismo esquema se usa desde el caso de seguimiento (la empresa) y desde el
 * listado de referencias (Gestor y admin), por eso los datos de cabecera se
 * resuelven tanto si $record es un CasoSeguimiento como si es una
 * SolicitudReferencia.
 */
class SolicitudReferenciaForm
{
    /**
     * Catálogo oficial de unidades de atención, agrupado por tipo.
     * Vive en App\Support\CatalogoUnidadesAtencion porque son 156 unidades.
     *
     * @return array<string, array<string, string>|string>
     */
    public static function unidadesAtencion(): array
    {
        return CatalogoUnidadesAtencion::opciones();
    }

    /**
     * Las ocho jurisdicciones sanitarias del estado, con su municipio sede
     * (hoja de Angélica, 31/08/2026). Se guarda solo el número; el municipio
     * es parte de la etiqueta para que quien llena el formato no tenga que
     * saberse el mapa de jurisdicciones.
     */
    public const JURISDICCIONES = [
        '1' => 'Jurisdicción 1: Piedras Negras',
        '2' => 'Jurisdicción 2: Acuña',
        '3' => 'Jurisdicción 3: Sabinas',
        '4' => 'Jurisdicción 4: Monclova',
        '5' => 'Jurisdicción 5: Cuatrociénegas',
        '6' => 'Jurisdicción 6: Torreón',
        '7' => 'Jurisdicción 7: Francisco I. Madero',
        '8' => 'Jurisdicción 8: Saltillo',
    ];

    /** Avance de la cita. Antes se llamaba "Estatus SOMOS+". */
    public const ESTATUS_CITA = [
        'Confirmo asistencia de cita' => 'Confirmo asistencia de cita',
        'Acudió a cita' => 'Acudió a cita',
        'Reagendo cita' => 'Reagendo cita',
        'Atendido por ruta alterna' => 'Atendido por ruta alterna',
        'Notificación a empresa' => 'Notificación a empresa',
    ];

    /** Colores con los que Salud distingue cada estatus en su propio tablero. */
    public const COLORES_ESTATUS_CITA = [
        'Confirmo asistencia de cita' => 'purple',
        'Acudió a cita' => 'success',
        'Reagendo cita' => 'danger',
        'Atendido por ruta alterna' => 'teal',
        'Notificación a empresa' => 'warning',
    ];

    public const DERECHOHABIENCIA = [
        'IMSS' => 'IMSS',
        'ISSSTE' => 'ISSSTE',
        'IMSS-Bienestar' => 'IMSS-Bienestar',
        'PEMEX' => 'PEMEX',
        'SEDENA / SEMAR' => 'SEDENA / SEMAR',
        'Seguro privado' => 'Seguro privado',
        'Ninguna' => 'Ninguna',
    ];

    /**
     * @param  bool  $puedeAgendar  true para Gestor y admin: habilita el bloque de cita.
     * @param  bool  $soloLectura  true para consultar el formato sin poder editarlo.
     */
    public static function componentes(bool $puedeAgendar = false, bool $soloLectura = false): array
    {
        $ro = $soloLectura;

        return [
            Section::make('Datos de la solicitud')
                ->schema([
                    Grid::make(3)->schema([
                        Placeholder::make('folio')
                            ->label('Folio')
                            ->content(function ($record) {
                                $folio = $record?->folio ?? $record?->solicitudReferencia?->folio;

                                return new HtmlString(
                                    '<span style="font-weight: 600; color: #0f766e;">'
                                    .($folio ?: 'Se generará automáticamente al guardar')
                                    .'</span>'
                                );
                            }),

                        DatePicker::make('fecha_solicitud')
                            ->label('Fecha de solicitud')
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->required(! $ro)
                            ->disabled($ro),

                        Placeholder::make('empresa_solicitante')
                            ->label('Empresa que solicita')
                            ->content(fn ($record) => $record?->empresa?->nombre_empresa ?? 'N/A'),
                    ]),

                    Grid::make(3)->schema([
                        TextInput::make('municipio')
                            ->label('Municipio')
                            ->required(! $ro)
                            ->disabled($ro)
                            ->maxLength(255),

                        Select::make('jurisdiccion')
                            ->label('Jurisdicción')
                            ->options(self::JURISDICCIONES)
                            ->disabled($ro),

                        Select::make('nivel_riesgo')
                            ->label(PrioridadAtencion::ETIQUETA)
                            ->options(PrioridadAtencion::opciones())
                            ->helperText(PrioridadAtencion::NOTA)
                            ->disabled($ro),
                    ]),
                ]),

            Section::make('Datos de la persona referida')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('nombre_usuario')
                            ->label('Nombre del usuario')
                            ->required(! $ro)
                            ->disabled($ro)
                            ->maxLength(255),

                        Select::make('sexo')
                            ->label('Sexo')
                            ->options([
                                'Hombre' => 'Hombre',
                                'Mujer' => 'Mujer',
                            ])
                            ->disabled($ro),

                        TextInput::make('edad')
                            ->label('Edad')
                            ->disabled($ro)
                            ->maxLength(30),
                    ]),

                    Grid::make(3)->schema([
                        TextInput::make('curp')
                            ->label('CURP')
                            ->maxLength(18)
                            ->disabled($ro)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;'])
                            ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper(trim($state)) : null),

                        TextInput::make('telefono_contacto')
                            ->label('Teléfono de contacto')
                            ->tel()
                            ->disabled($ro)
                            ->maxLength(20),

                        Select::make('derechohabiencia')
                            ->label('Derechohabiencia')
                            ->options(self::DERECHOHABIENCIA)
                            ->disabled($ro),
                    ]),

                    TextInput::make('domicilio')
                        ->label('Domicilio')
                        ->maxLength(255)
                        ->disabled($ro)
                        ->columnSpanFull(),

                    TextInput::make('servicio_solicitado')
                        ->label('Servicio que solicita')
                        ->maxLength(255)
                        ->disabled($ro)
                        ->columnSpanFull(),

                    // Campo abierto que pidió Angélica el 10/09/2026: el
                    // catálogo de servicios no alcanza a explicar el contexto.
                    Textarea::make('motivo_referencia')
                        ->label('Motivo de referencia')
                        ->helperText('Descripción general de la situación o necesidad.')
                        ->rows(4)
                        ->maxLength(65535)
                        ->disabled($ro)
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        // Obligatoria desde el 11/09/2026, a petición de
                        // Angélica: Salud no puede agendar sin identificación.
                        FileUpload::make('ine_path')
                            ->label('INE')
                            ->required(! $ro)
                            ->helperText('Adjunta identificación oficial. Máximo 10 MB.')
                            ->disk('public')
                            ->directory('referencias/ine')
                            ->downloadable()
                            ->openable()
                            ->maxSize(10240)
                            ->disabled($ro)
                            ->acceptedFileTypes(['application/pdf', 'image/*']),

                        FileUpload::make('informe_valoracion_path')
                            ->label('Informe de valoración')
                            ->helperText('Adjunta el informe en PDF. Máximo 10 MB.')
                            ->disk('public')
                            ->directory('referencias/informes')
                            ->downloadable()
                            ->openable()
                            ->maxSize(10240)
                            ->disabled($ro)
                            ->acceptedFileTypes(['application/pdf', 'image/*']),
                    ]),
                ]),

            Section::make('Resultados del diagnóstico en línea')
                // La aclaración solo aplica cuando hay resultados; si no, el
                // aviso de más abajo es el que explica por qué están vacíos.
                ->description(fn ($record) => self::tamizajeDe($record)
                    ? 'Los contestó la persona en el cuestionario; se arrastran tal cual, no se capturan aparte.'
                    : null)
                ->schema([
                    Grid::make(3)
                        ->visible(fn ($record) => self::tamizajeDe($record) !== null)
                        ->schema([
                            self::resultado(
                                'resultado_ansiedad',
                                'Síntomas de Ansiedad',
                                fn (Tamizaje $t) => $t->nivel_ansiedad,
                                fn (Tamizaje $t) => 'GAD-7: '.(int) $t->riesgo_ansiedad.' de 21',
                            ),

                            self::resultado(
                                'resultado_depresion',
                                'Síntomas de Depresión',
                                fn (Tamizaje $t) => $t->nivel_depresion,
                                fn (Tamizaje $t) => 'PHQ-9: '.(int) $t->riesgo_depresion.' de 27',
                            ),

                            // El ASQ va con el título completo de la pantalla
                            // —"Positivo: Riesgo Agudo" cuando la pregunta 5
                            // fue "Sí"— y su acción debajo, igual que en el
                            // detalle del tamizaje.
                            self::resultado(
                                'resultado_conducta_suicida',
                                'Indicadores de Conducta suicida',
                                fn (Tamizaje $t) => ResultadoAsq::titulo($t),
                                fn (Tamizaje $t) => ResultadoAsq::accion($t),
                                nivelParaColor: fn (Tamizaje $t) => $t->nivel_suicidio,
                            ),
                        ]),

                    Placeholder::make('sin_tamizaje')
                        ->hiddenLabel()
                        ->visible(fn ($record) => self::tamizajeDe($record) === null)
                        ->content(new HtmlString('<div style="color: #6b7280; font-size: 0.9rem;">Este caso se capturó a mano: no hay cuestionario en línea del que arrastrar resultados. El detalle clínico va en el informe de valoración.</div>')),

                    Placeholder::make('nota_resultados')
                        ->hiddenLabel()
                        ->visible(fn ($record) => self::tamizajeDe($record) !== null)
                        ->content(new HtmlString('<div style="color: #6b7280; font-size: 0.78rem; line-height: 1.4;"><strong>Nota:</strong> '.e(PrioridadAtencion::NOTA).'</div>')),
                ]),

            Section::make('Asignación de cita')
                ->description($puedeAgendar
                    ? 'Este bloque lo captura el Gestor o el administrador y la empresa lo ve en su tablero.'
                    : 'Lo asigna Secretaría de Salud. Aquí verás la cita en cuanto quede agendada.')
                ->schema([
                    Grid::make(3)->schema([
                        DateTimePicker::make('fecha_cita')
                            ->label('Fecha de la cita')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->disabled($ro || ! $puedeAgendar),

                        Select::make('unidad_atencion')
                            ->label('Unidad de atención')
                            ->options(self::unidadesAtencion())
                            ->searchable()
                            ->optionsLimit(CatalogoUnidadesAtencion::limiteOpciones())
                            ->live()
                            ->disabled($ro || ! $puedeAgendar),

                        Select::make('estatus_cita')
                            ->label('Estatus')
                            ->options(self::ESTATUS_CITA)
                            ->disabled($ro || ! $puedeAgendar),
                    ]),

                    TextInput::make('unidad_atencion_otra')
                        ->label('¿En dónde?')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('unidad_atencion') === 'Otro')
                        ->required(fn (Get $get): bool => ! $ro && $puedeAgendar && $get('unidad_atencion') === 'Otro')
                        ->disabled($ro || ! $puedeAgendar),
                ]),
        ];
    }

    /**
     * Tamizaje del que salen los resultados. El esquema lo consumen tres
     * paneles, así que el registro puede ser un CasoSeguimiento (tiene el
     * accesor `tamizaje`, que lo busca por nombre) o una SolicitudReferencia,
     * que llega a él por su caso.
     */
    private static function tamizajeDe($record): ?Tamizaje
    {
        return $record?->tamizaje ?? $record?->casoSeguimiento?->tamizaje;
    }

    /**
     * Resultado de un instrumento, con el color del nivel y su renglón de
     * apoyo —el puntaje o la acción— en letra chica, como en el detalle del
     * tamizaje. Es de solo lectura en los tres paneles: el resultado lo
     * calcula el instrumento, no lo escribe quien llena el formato.
     */
    private static function resultado(
        string $nombre,
        string $titulo,
        callable $valor,
        callable $apoyo,
        ?callable $nivelParaColor = null,
    ): Placeholder {
        return Placeholder::make($nombre)
            ->hiddenLabel()
            ->content(function ($record) use ($titulo, $valor, $apoyo, $nivelParaColor) {
                $tamizaje = self::tamizajeDe($record);

                if (! $tamizaje) {
                    return null;
                }

                $nivel = (string) $valor($tamizaje);

                // El color sale del valor guardado, no del título de pantalla:
                // ColorNivel conoce "Positivo", no "Positivo: Riesgo Agudo", y
                // si no se distingue el badge del ASQ se pinta gris. Igual que
                // en el detalle del tamizaje.
                $color = ColorNivel::hex($nivelParaColor ? (string) $nivelParaColor($tamizaje) : $nivel);
                $renglonApoyo = $apoyo($tamizaje);

                $apoyoHtml = $renglonApoyo
                    ? '<span style="display: block; font-size: 0.72rem; font-weight: 500; margin-top: 2px;">'.e($renglonApoyo).'</span>'
                    : '';

                return new HtmlString(
                    '<span style="background-color: '.$color.'; color: white; padding: 8px 16px; border-radius: 1rem; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;">'
                    .e($titulo).': '.e($nivel).$apoyoHtml
                    .'</span>'
                );
            });
    }

    /**
     * Valores por defecto de la modal, tomados del caso y del tamizaje para no
     * pedirle a la empresa datos que la plataforma ya conoce.
     */
    public static function valoresIniciales($caso): array
    {
        $solicitud = $caso->solicitudReferencia;

        if ($solicitud) {
            return $solicitud->only([
                'fecha_solicitud', 'municipio', 'jurisdiccion', 'nivel_riesgo',
                'nombre_usuario', 'sexo', 'edad', 'curp', 'telefono_contacto',
                'ine_path', 'domicilio', 'derechohabiencia', 'servicio_solicitado',
                'motivo_referencia',
                'informe_valoracion_path', 'estatus_cita', 'fecha_cita',
                'unidad_atencion', 'unidad_atencion_otra',
            ]);
        }

        $tamizaje = Tamizaje::where('empresa_id', $caso->empresa_id)
            ->where('nombre_completo', $caso->identificador_empleado)
            ->first();

        return [
            'fecha_solicitud' => now(),
            'municipio' => $caso->empresa?->municipio,
            'nivel_riesgo' => $caso->nivel_riesgo_detectado,
            'nombre_usuario' => $caso->identificador_empleado,
            'sexo' => $caso->genero ?: $tamizaje?->genero,
            'edad' => $caso->edad ?: $tamizaje?->edad,
            'telefono_contacto' => $caso->celular ?: $tamizaje?->telefono,
            'servicio_solicitado' => $caso->servicios_texto === 'N/A' ? null : $caso->servicios_texto,
        ];
    }
}
