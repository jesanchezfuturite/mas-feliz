<?php

namespace App\Filament\Empresa\Resources\CasoSeguimientos\Schemas;

use App\Models\CasoSeguimiento;
use App\Support\ColorNivel;
use App\Support\PrioridadAtencion;
use App\Support\ResultadoAsq;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\HtmlString;

/**
 * Detalle completo de un caso de Atención: quién es la persona, qué salió en
 * su tamizaje, cómo va su atención y en qué va su referencia a Salud.
 *
 * Vivía dentro de la modal "Ver detalle" del panel empresa. Se extrajo porque
 * Angélica pidió el 11/09/2026 "que el perfil de gestor pueda ver todos los
 * datos de los usuarios que le mandan (los que salen en el apartado de
 * Atención)": es el mismo detalle, visto desde el otro lado.
 *
 * El registro puede ser un CasoSeguimiento (panel empresa y casos canalizados)
 * o una SolicitudReferencia (listado de referencias del Gestor), que llega a su
 * caso. `$conEmpresa` agrega la organización, porque las pantallas de gobierno
 * cruzan empresas y la de la empresa no.
 */
class DetalleCasoForm
{
    public static function componentes(bool $conEmpresa = false): array
    {
        return [
            Grid::make(3)->schema([
                self::badge(
                    'nivel_ansiedad',
                    'Síntomas de Ansiedad',
                    fn (CasoSeguimiento $caso) => $caso->tamizaje?->nivel_ansiedad,
                ),

                self::badge(
                    'nivel_depresion',
                    'Síntomas de Depresión',
                    fn (CasoSeguimiento $caso) => $caso->tamizaje?->nivel_depresion,
                ),

                // El ASQ va completo, con la agudeza y su acción en letra chica
                // (Angélica, 27/08/2026). Es despliegue: la columna sigue
                // guardando dos valores. El color sale del valor guardado
                // porque ColorNivel no conoce "Positivo: Riesgo Agudo".
                self::badge(
                    'nivel_suicidio',
                    'Indicadores de Conducta suicida',
                    fn (CasoSeguimiento $caso) => $caso->tamizaje ? ResultadoAsq::titulo($caso->tamizaje) : null,
                    apoyo: fn (CasoSeguimiento $caso) => $caso->tamizaje ? ResultadoAsq::accion($caso->tamizaje) : null,
                    nivelParaColor: fn (CasoSeguimiento $caso) => $caso->tamizaje?->nivel_suicidio,
                ),
            ]),

            self::titulo('info_title', 'Información del Empleado'),

            Grid::make(2)->schema(array_filter([
                $conEmpresa
                    ? self::texto('organizacion', 'Organización', fn (CasoSeguimiento $caso) => $caso->empresa?->nombre_empresa)
                    : null,

                self::texto('nombre_completo', 'Nombre Completo', fn (CasoSeguimiento $caso) => $caso->identificador_empleado !== 'N/A'
                    ? $caso->identificador_empleado
                    : $caso->tamizaje?->nombre_completo),

                self::texto('genero', 'Sexo', fn (CasoSeguimiento $caso) => $caso->genero ?: $caso->tamizaje?->genero),

                self::texto('edad', 'Grupo de Edad', fn (CasoSeguimiento $caso) => $caso->edad ?: $caso->tamizaje?->edad),

                self::texto('tiempo_trabajando', 'Tiempo trabajando', fn (CasoSeguimiento $caso) => $caso->tiempo_trabajando ?: $caso->tamizaje?->tiempo_trabajando),

                self::texto('actividad_trabajo', 'Departamento / Actividad', fn (CasoSeguimiento $caso) => self::actividad($caso)),

                // Contacto: el Gestor lo necesita para citar a la persona, y en
                // el panel empresa ya lo tenía en la tabla de Atención.
                self::texto('celular', 'Teléfono', fn (CasoSeguimiento $caso) => $caso->celular ?: $caso->tamizaje?->telefono),

                self::texto('correo', 'Correo', fn (CasoSeguimiento $caso) => $caso->correo ?: $caso->tamizaje?->correo),

                self::texto('prioridad', PrioridadAtencion::ETIQUETA, fn (CasoSeguimiento $caso) => $caso->nivel_riesgo_detectado),

                self::texto('fecha_evaluacion', 'Fecha de Evaluación', fn (CasoSeguimiento $caso) => $caso->tamizaje?->created_at?->format('d/m/Y')),
            ])),

            self::titulo('seguimiento_title', 'Seguimiento'),

            Grid::make(2)->schema([
                self::texto('estatus_atencion', 'Estatus de la Atención', fn (CasoSeguimiento $caso) => $caso->estatus_atencion),

                self::texto('institucion_canalizacion', 'Institución de Canalización', fn (CasoSeguimiento $caso) => $caso->institucion_canalizacion),

                self::texto('servicios', 'Servicio que requiere', fn (CasoSeguimiento $caso) => $caso->servicios_texto),

                self::texto('consentimiento', 'Consentimiento', fn (CasoSeguimiento $caso) => match ($caso->consentimiento) {
                    true => 'Sí',
                    false => 'No',
                    default => 'Sin registrar',
                }),

                Placeholder::make('comentarios')
                    ->label('Comentarios')
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled(self::caso($record)?->notas_clinicas))
                    ->content(fn ($record) => new HtmlString('<div style="color: #6b7280; font-size: 0.95rem; white-space: pre-wrap;">'.e(self::caso($record)?->notas_clinicas).'</div>')),
            ]),

            // Bloque que llena Secretaría de Salud y que aquí se consulta.
            self::titulo('referencia_title', 'Referencia a Secretaría de Salud')
                ->visible(fn ($record) => self::caso($record)?->solicitudReferencia !== null),

            Grid::make(2)
                ->visible(fn ($record) => self::caso($record)?->solicitudReferencia !== null)
                ->schema([
                    Placeholder::make('folio_referencia')
                        ->label('Folio')
                        ->content(fn ($record) => new HtmlString('<div style="color: #0f766e; font-size: 0.95rem; font-weight: 600;">'.e(self::caso($record)?->solicitudReferencia?->folio ?? 'N/A').'</div>')),

                    self::texto('estatus_cita', 'Estatus de la cita', fn (CasoSeguimiento $caso) => $caso->solicitudReferencia?->estatus_cita, 'Sin registrar'),

                    self::texto('fecha_cita', 'Fecha de la cita', fn (CasoSeguimiento $caso) => $caso->solicitudReferencia?->fecha_cita?->format('d/m/Y H:i'), 'Pendiente de asignar'),

                    self::texto('unidad_atencion', 'Unidad de atención', fn (CasoSeguimiento $caso) => $caso->solicitudReferencia?->unidad_atencion_completa, 'Pendiente de asignar'),

                    self::texto('motivo_referencia', 'Motivo de referencia', fn (CasoSeguimiento $caso) => $caso->solicitudReferencia?->motivo_referencia)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * Caso del que se muestra el detalle. Las pantallas del Gestor llegan
     * desde la solicitud de referencia, no desde el caso.
     */
    private static function caso($record): ?CasoSeguimiento
    {
        if ($record instanceof CasoSeguimiento) {
            return $record;
        }

        return $record?->casoSeguimiento;
    }

    private static function actividad(CasoSeguimiento $caso): ?string
    {
        $propia = $caso->actividad_trabajo === 'Otra' ? $caso->actividad_trabajo_otra : $caso->actividad_trabajo;

        if (filled($propia)) {
            return $propia;
        }

        $tamizaje = $caso->tamizaje;

        if (! $tamizaje) {
            return null;
        }

        return $tamizaje->actividad_trabajo === 'Otra' ? $tamizaje->actividad_trabajo_otra : $tamizaje->actividad_trabajo;
    }

    private static function titulo(string $nombre, string $texto): Placeholder
    {
        return Placeholder::make($nombre)
            ->hiddenLabel()
            ->content(new HtmlString('<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-top: 1.5rem;"><h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">'.e($texto).'</h3></div>'));
    }

    private static function texto(string $nombre, string $etiqueta, callable $valor, string $vacio = 'N/A'): Placeholder
    {
        return Placeholder::make($nombre)
            ->label($etiqueta)
            ->content(function ($record) use ($valor, $vacio) {
                $caso = self::caso($record);
                $contenido = $caso ? $valor($caso) : null;

                return new HtmlString('<div style="color: #6b7280; font-size: 0.95rem;">'.e(filled($contenido) ? $contenido : $vacio).'</div>');
            });
    }

    private static function badge(string $nombre, string $titulo, callable $valor, ?callable $apoyo = null, ?callable $nivelParaColor = null): Placeholder
    {
        return Placeholder::make($nombre)
            ->hiddenLabel()
            ->content(function ($record) use ($titulo, $valor, $apoyo, $nivelParaColor) {
                $caso = self::caso($record);
                $nivel = $caso ? $valor($caso) : null;
                $nivel = filled($nivel) ? (string) $nivel : 'N/A';

                $paraColor = ($caso && $nivelParaColor) ? $nivelParaColor($caso) : $nivel;
                $color = ColorNivel::hex(filled($paraColor) ? (string) $paraColor : null);

                $lineaApoyo = ($caso && $apoyo && filled($texto = $apoyo($caso)))
                    ? '<span style="display: block; font-size: 0.72rem; font-weight: 500; margin-top: 2px;">'.e($texto).'</span>'
                    : '';

                return new HtmlString(
                    '<span style="background-color: '.$color.'; color: white; padding: 8px 16px; border-radius: 1rem; font-size: 0.875rem; font-weight: 600; display: inline-block; width: 100%; text-align: center;">'
                    .e($titulo).': '.e($nivel).$lineaApoyo
                    .'</span>'
                );
            });
    }
}
