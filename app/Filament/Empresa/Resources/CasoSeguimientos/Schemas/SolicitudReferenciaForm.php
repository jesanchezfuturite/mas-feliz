<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

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
     * Catálogos confirmados por Angélica R el 06/08/2026.
     */
    public const UNIDADES_ATENCION = [
        'Centro de Salud' => 'Centro de Salud',
        'Hospital General' => 'Hospital General (HG)',
        'Centro Integral' => 'Centro Integral',
        'CECOSAMA' => 'CECOSAMA',
        'Otro' => 'Otro',
    ];

    /** Las ocho jurisdicciones sanitarias del estado. */
    public const JURISDICCIONES = [
        '1' => 'Jurisdicción 1',
        '2' => 'Jurisdicción 2',
        '3' => 'Jurisdicción 3',
        '4' => 'Jurisdicción 4',
        '5' => 'Jurisdicción 5',
        '6' => 'Jurisdicción 6',
        '7' => 'Jurisdicción 7',
        '8' => 'Jurisdicción 8',
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
     * @param  bool  $soloLectura   true para consultar el formato sin poder editarlo.
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

                                return new \Illuminate\Support\HtmlString(
                                    '<span style="font-weight: 600; color: #0f766e;">'
                                    . ($folio ?: 'Se generará automáticamente al guardar')
                                    . '</span>'
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
                            ->label('Nivel de riesgo')
                            ->options([
                                'Leve' => 'Leve',
                                'Moderado' => 'Moderado',
                                'Urgente' => 'Urgente',
                            ])
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

                    Grid::make(2)->schema([
                        FileUpload::make('ine_path')
                            ->label('INE')
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
                            ->options(self::UNIDADES_ATENCION)
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
                'informe_valoracion_path', 'estatus_cita', 'fecha_cita',
                'unidad_atencion', 'unidad_atencion_otra',
            ]);
        }

        $tamizaje = \App\Models\Tamizaje::where('empresa_id', $caso->empresa_id)
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
