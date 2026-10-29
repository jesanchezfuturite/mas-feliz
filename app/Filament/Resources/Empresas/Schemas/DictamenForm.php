<?php

namespace App\Filament\Resources\Empresas\Schemas;

use App\Models\Empresa;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;

/**
 * Los cuatro campos del «Dictamen de Gobierno» de una empresa.
 *
 * Viven aquí porque el administrador los llena desde tres lugares —el
 * listado de empresas, el encabezado de la ficha y la propia tarjeta del
 * dictamen— y porque el nivel de madurez tiene que salir siempre de
 * Empresa::NIVELES_MADUREZ.
 */
class DictamenForm
{
    /**
     * @return array<int, mixed>
     */
    public static function getSchema(): array
    {
        return [
            Select::make('estatus_distintivo')
                ->label('Estatus del Distintivo')
                // Las opciones salen del estado del propio campo para que un
                // estatus heredado ('Validado') no aparezca en blanco.
                ->options(fn (Get $get) => Empresa::opcionesEstatusDistintivo($get('estatus_distintivo')))
                ->required()
                ->live(),

            Select::make('nivel_madurez_asignado')
                ->label('Nivel de Madurez Asignado')
                ->options(Empresa::opcionesNivelMadurez())
                ->placeholder('Sin nivel asignado')
                ->required(fn (Get $get) => $get('estatus_distintivo') === 'Aprobado')
                ->hidden(fn (Get $get) => $get('estatus_distintivo') === 'No aprobado'),

            DateTimePicker::make('fecha_dictamen')
                ->label('Fecha de Dictamen')
                ->seconds(false)
                ->displayFormat('d/m/Y h:i A')
                ->helperText('Se propone la fecha de hoy; se puede cambiar.'),

            // Campo abierto y opcional: Angélica lo pidió así, con esa
            // etiqueta y sin nada que obligue a llenarlo (28/10/2026).
            Textarea::make('retroalimentacion_gobierno')
                ->label('Comentario')
                ->rows(4)
                ->columnSpanFull(),
        ];
    }

    /**
     * Valores con los que se abre el formulario: lo que ya tiene la empresa,
     * con la fecha de hoy propuesta cuando todavía no se ha dictaminado.
     *
     * @return array<string, mixed>
     */
    public static function valores(Empresa $empresa): array
    {
        return [
            'estatus_distintivo' => $empresa->estatus_distintivo,
            'nivel_madurez_asignado' => $empresa->nivel_madurez_asignado,
            'fecha_dictamen' => $empresa->fecha_dictamen ?? now(),
            'retroalimentacion_gobierno' => $empresa->retroalimentacion_gobierno,
        ];
    }

    /**
     * Lo que se guarda en la empresa. Una empresa no aprobada no se queda
     * con nivel de madurez.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function datos(array $data): array
    {
        $estatus = $data['estatus_distintivo'];

        return [
            'estatus_distintivo' => $estatus,
            'nivel_madurez_asignado' => $estatus === 'No aprobado' ? null : ($data['nivel_madurez_asignado'] ?? null),
            'fecha_dictamen' => $data['fecha_dictamen'] ?? null,
            'retroalimentacion_gobierno' => $data['retroalimentacion_gobierno'] ?? null,
        ];
    }

    /**
     * La acción lista para colgarse del listado, de la ficha o de la tarjeta
     * del dictamen.
     */
    public static function accion(string $nombre = 'emitir_dictamen'): Action
    {
        return Action::make($nombre)
            ->label('Emitir Dictamen')
            ->icon('heroicon-o-shield-check')
            ->color('primary')
            ->modalHeading('Dictamen de Gobierno')
            ->modalDescription(fn (Empresa $record) => $record->nombre_empresa)
            ->modalFooterActionsAlignment(Alignment::Right)
            ->modalSubmitActionLabel('Guardar dictamen')
            ->modalCancelActionLabel('Cerrar')
            ->fillForm(fn (Empresa $record) => self::valores($record))
            ->form(self::getSchema())
            ->action(function (Empresa $record, array $data): void {
                $record->update(self::datos($data));

                Notification::make()
                    ->title('Dictamen guardado')
                    ->body('La empresa ya aparece con el dictamen en su tablero.')
                    ->success()
                    ->send();
            });
    }
}
