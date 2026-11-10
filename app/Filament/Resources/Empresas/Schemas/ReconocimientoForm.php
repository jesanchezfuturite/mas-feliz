<?php

namespace App\Filament\Resources\Empresas\Schemas;

use App\Models\Empresa;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Los datos que la empresa confirma para su reconocimiento.
 *
 * Angélica (31/10/2026): para la entrega en Laguna se pidieron uno por uno, a
 * mano —el nombre tal cual va en el reconocimiento, el logotipo en HD para el
 * proveedor, y nombre, cargo y celular del Director/Gerente y del enlace para
 * las invitaciones de RP—. Ahora los llena la propia empresa antes de ver su
 * resultado.
 *
 * Vive aquí porque lo usan dos paneles: la empresa lo llena en su tablero y
 * el administrador lo captura o corrige desde el listado de Reconocimientos.
 */
class ReconocimientoForm
{
    /**
     * Lado mayor mínimo de un logotipo en imagen. Lo que se pide es que sirva
     * para imprimir: con menos de esto el proveedor lo regresa.
     */
    public const LADO_MINIMO_PX = 1000;

    /** Tamaño máximo del logotipo, en KB. */
    public const PESO_MAXIMO_KB = 20480;

    /** Carpeta del disco público donde quedan los logotipos. */
    public const CARPETA_LOGOS = 'logos-reconocimiento';

    /**
     * @param  bool  $logoObligatorio  La empresa no puede confirmar sin
     *                                 logotipo; el administrador sí puede
     *                                 corregir un nombre sin tenerlo a la mano.
     * @return array<int, mixed>
     */
    public static function getSchema(bool $logoObligatorio = true): array
    {
        return [
            Section::make('Tu reconocimiento')
                ->description('Así se imprimirá el reconocimiento y así aparecerá tu organización en la imagen del evento.')
                ->schema([
                    TextInput::make('nombre_reconocimiento')
                        ->label('Nombre de la organización, tal cual debe ir en el reconocimiento')
                        ->helperText('Revísalo letra por letra: mayúsculas, acentos y razón social. Si se reconoce a una planta, área o unidad en particular, escribe el nombre completo (por ejemplo: «Nombre de la empresa – Planta Norte»).')
                        ->required()
                        ->maxLength(191)
                        ->columnSpanFull(),

                    FileUpload::make('logo_reconocimiento')
                        ->label('Logotipo en alta resolución')
                        ->helperText('PNG o JPG de al menos '.self::LADO_MINIMO_PX.' px en su lado mayor, o el archivo vectorial en PDF. De preferencia con fondo transparente. Máximo '.(self::PESO_MAXIMO_KB / 1024).' MB.')
                        ->disk('public')
                        ->directory(self::CARPETA_LOGOS)
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'application/pdf'])
                        ->maxSize(self::PESO_MAXIMO_KB)
                        ->required($logoObligatorio)
                        ->downloadable()
                        ->openable()
                        ->rule(fn (): Closure => self::reglaAltaResolucion())
                        ->columnSpanFull(),
                ]),

            Section::make('Director(a) o Gerente General')
                ->description('A quien se dirige la invitación oficial al evento de entrega.')
                ->columns(3)
                ->schema([
                    TextInput::make('nombre_director')
                        ->label('Nombre completo')
                        ->helperText('Con título, si lo usa: Ing., Lic., Dra.')
                        ->required()
                        ->maxLength(191),
                    TextInput::make('cargo_director')
                        ->label('Cargo')
                        ->placeholder('Gerente de Planta')
                        ->required()
                        ->maxLength(191),
                    self::celular('celular_director'),
                ]),

            Section::make('Enlace de la organización')
                ->description('La persona con quien se coordina la entrega.')
                ->columns(3)
                ->schema([
                    TextInput::make('nombre_responsable')
                        ->label('Nombre completo')
                        ->required()
                        ->maxLength(191),
                    TextInput::make('cargo_enlace')
                        ->label('Cargo')
                        ->placeholder('Gerente de Recursos Humanos')
                        ->required()
                        ->maxLength(191),
                    self::celular('celular_enlace'),
                ]),
        ];
    }

    private static function celular(string $nombre): TextInput
    {
        return TextInput::make($nombre)
            ->label('Celular')
            ->tel()
            ->placeholder('871 123 4567')
            ->helperText('A 10 dígitos.')
            ->required()
            ->maxLength(20)
            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                if (strlen(self::soloDigitos($value)) !== 10) {
                    $fail('Escribe el celular a 10 dígitos.');
                }
            });
    }

    /**
     * Un logotipo en imagen tiene que alcanzar el lado mínimo. Los PDF pasan:
     * son vectoriales y no tienen tamaño en pixeles.
     */
    public static function reglaAltaResolucion(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $value instanceof TemporaryUploadedFile) {
                return;
            }

            if (! str_starts_with((string) $value->getMimeType(), 'image/')) {
                return;
            }

            $medidas = @getimagesize($value->getRealPath());

            if (! $medidas) {
                $fail('No se pudo leer la imagen. Sube el logotipo en PNG, JPG o PDF.');

                return;
            }

            [$ancho, $alto] = $medidas;

            if (max($ancho, $alto) < self::LADO_MINIMO_PX) {
                $fail("El logotipo mide {$ancho} × {$alto} px y no alcanza para imprimirse. Sube una versión de al menos ".self::LADO_MINIMO_PX.' px en su lado mayor, o el archivo vectorial en PDF.');
            }
        };
    }

    private static function soloDigitos(mixed $valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor) ?? '';
    }

    /**
     * Valores con los que se abre el formulario. El nombre se propone con el
     * que la empresa se registró, para que lo confirme o lo corrija.
     *
     * @return array<string, mixed>
     */
    public static function valores(Empresa $empresa): array
    {
        return [
            'nombre_reconocimiento' => $empresa->nombre_para_reconocimiento,
            'logo_reconocimiento' => $empresa->logo_reconocimiento,
            'nombre_director' => $empresa->nombre_director,
            'cargo_director' => $empresa->cargo_director,
            'celular_director' => $empresa->celular_director,
            'nombre_responsable' => $empresa->nombre_responsable,
            'cargo_enlace' => $empresa->cargo_enlace,
            'celular_enlace' => $empresa->celular_enlace,
        ];
    }

    /**
     * Lo que se guarda en la empresa. Guardar es confirmar: se sella la fecha
     * y con eso se abre el candado del resultado.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function datos(array $data): array
    {
        return [
            'nombre_reconocimiento' => trim((string) $data['nombre_reconocimiento']),
            'logo_reconocimiento' => $data['logo_reconocimiento'] ?? null,
            'nombre_director' => trim((string) $data['nombre_director']),
            'cargo_director' => trim((string) $data['cargo_director']),
            'celular_director' => self::soloDigitos($data['celular_director']),
            'nombre_responsable' => trim((string) $data['nombre_responsable']),
            'cargo_enlace' => trim((string) $data['cargo_enlace']),
            'celular_enlace' => self::soloDigitos($data['celular_enlace']),
            'reconocimiento_confirmado_at' => now(),
        ];
    }

    /**
     * La acción con la que el administrador captura o corrige los datos de
     * una empresa: lo que puede la empresa lo puede el administrador.
     */
    public static function accion(string $nombre = 'datos_reconocimiento'): Action
    {
        return Action::make($nombre)
            ->label('Datos del reconocimiento')
            ->icon('heroicon-o-pencil-square')
            ->color('primary')
            ->modalHeading('Datos del reconocimiento')
            ->modalDescription(fn (Empresa $record) => $record->nombre_empresa.' · al guardar se dan por confirmados y la empresa deja de ver el candado.')
            ->modalWidth('4xl')
            ->modalFooterActionsAlignment(Alignment::Right)
            ->modalSubmitActionLabel('Guardar datos')
            ->modalCancelActionLabel('Cerrar')
            ->fillForm(fn (Empresa $record) => self::valores($record))
            ->form(self::getSchema(logoObligatorio: false))
            ->action(function (Empresa $record, array $data): void {
                $record->update(self::datos($data));

                Notification::make()
                    ->title('Datos del reconocimiento guardados')
                    ->success()
                    ->send();
            });
    }
}
