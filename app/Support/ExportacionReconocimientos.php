<?php

namespace App\Support;

use App\Models\Empresa;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use ZipArchive;

/**
 * Lo que Gobierno se lleva para elaborar los reconocimientos y las
 * invitaciones: la hoja con las organizaciones, su nivel y sus contactos, y
 * los logotipos en alta resolución para el proveedor.
 *
 * Angélica (31/10/2026) armó esto a mano para Laguna —un PDF con
 * Organización / Logo / Nivel y una hoja con Director, enlace y acompañante
 * para RP— y pidió que la plataforma lo exporte «por región, municipio».
 * Quien llama decide el alcance con la consulta: aquí solo se ordena y se
 * escribe.
 */
class ExportacionReconocimientos
{
    public const ENCABEZADOS = [
        'Organización (nombre para el reconocimiento)',
        'Nombre registrado en la plataforma',
        'Folio',
        'Municipio',
        'Región',
        'Nivel de Madurez',
        'Director/Gerente: nombre',
        'Director/Gerente: puesto',
        'Director/Gerente: celular',
        'Enlace: nombre',
        'Enlace: puesto',
        'Enlace: celular',
        'Correo de la organización',
        'Acompañante actual',
        'Logotipo (archivo)',
        'Datos confirmados',
        'Fecha de confirmación',
    ];

    public const TIPO_MIME_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public const TIPO_MIME_ZIP = 'application/zip';

    /** Posición (base 0) de la fecha de confirmación, que va como fecha. */
    private const COLUMNA_FECHA = 16;

    /**
     * Las empresas de la consulta, en el orden en que se entregan los
     * reconocimientos: por región, luego municipio, luego nombre. Se ordena
     * en memoria porque la región no es una columna y el listado de
     * reconocidas es de decenas, no de miles.
     *
     * @return Collection<int, Empresa>
     */
    public static function empresas(Builder $consulta): Collection
    {
        return $consulta->clone()->reorder()->with('evaluadores')->get()
            ->sortBy(fn (Empresa $empresa) => [
                RegionesCoahuila::orden($empresa->region),
                (string) $empresa->municipio,
                Str::lower(Str::ascii($empresa->nombre_para_reconocimiento)),
            ])
            ->values();
    }

    /**
     * Un renglón de la hoja, en el orden de {@see ENCABEZADOS}.
     *
     * @return list<string|\DateTimeInterface>
     */
    public static function fila(Empresa $empresa): array
    {
        $confirmado = $empresa->reconocimiento_confirmado_at;

        return [
            $empresa->nombre_para_reconocimiento,
            (string) $empresa->nombre_empresa,
            (string) $empresa->folio,
            (string) $empresa->municipio,
            $empresa->region ?? RegionesCoahuila::SIN_REGION,
            (string) $empresa->nivel_madurez_asignado,
            (string) $empresa->nombre_director,
            (string) $empresa->cargo_director,
            (string) $empresa->celular_director,
            (string) $empresa->nombre_responsable,
            (string) $empresa->cargo_enlace,
            (string) $empresa->celular_enlace,
            (string) $empresa->correo,
            self::acompanantes($empresa),
            filled($empresa->logo_reconocimiento) ? self::nombreLogo($empresa) : 'Pendiente',
            $confirmado ? 'Sí' : 'Pendiente',
            $confirmado ?: '',
        ];
    }

    /** Quienes acompañan a la empresa, como en la hoja de RP. */
    public static function acompanantes(Empresa $empresa): string
    {
        return $empresa->evaluadores
            ->map(fn ($usuario) => trim($usuario->name.' '.$usuario->apellidos))
            ->filter()
            ->implode(', ');
    }

    /**
     * Nombre con el que el logotipo sale en el ZIP: el de la organización,
     * para que el proveedor no reciba archivos con nombres al azar. Va sin
     * acentos porque los Windows de oficina los parten al descomprimir.
     */
    public static function nombreLogo(Empresa $empresa): string
    {
        $extension = Str::lower(pathinfo((string) $empresa->logo_reconocimiento, PATHINFO_EXTENSION)) ?: 'png';

        $nombre = Str::ascii($empresa->nombre_para_reconocimiento);
        $nombre = trim(preg_replace('/[^A-Za-z0-9 ._-]+/', ' ', $nombre) ?? '');
        $nombre = Str::limit(preg_replace('/\s+/', ' ', $nombre) ?? '', 80, '');

        return trim($nombre.' ('.$empresa->folio.')').'.'.$extension;
    }

    /**
     * Escribe la hoja de cálculo y devuelve cuántas organizaciones lleva.
     */
    public static function escribirExcel(Builder $consulta, string $ruta): int
    {
        $empresas = self::empresas($consulta);

        $opciones = new Options;
        $opciones->setColumnWidthForRange(44, 1, 2);
        $opciones->setColumnWidthForRange(18, 3, 6);
        $opciones->setColumnWidthForRange(30, 7, 8);
        $opciones->setColumnWidth(18, 9);
        $opciones->setColumnWidthForRange(30, 10, 11);
        $opciones->setColumnWidth(18, 12);
        $opciones->setColumnWidthForRange(32, 13, 15);
        $opciones->setColumnWidthForRange(18, 16, 17);

        $escritor = new Writer($opciones);
        $escritor->openToFile($ruta);
        $escritor->getCurrentSheet()->setName('Reconocimientos');

        $escritor->addRow(Row::fromValues(
            self::ENCABEZADOS,
            (new Style)->setFontBold()->setShouldWrapText()->setBackgroundColor('E5E7EB'),
        ));

        $estiloFecha = (new Style)->setFormat('dd/mm/yyyy');

        foreach ($empresas as $empresa) {
            $escritor->addRow(Row::fromValuesWithStyles(
                self::fila($empresa),
                null,
                [self::COLUMNA_FECHA => $estiloFecha],
            ));
        }

        $escritor->addNewSheetAndMakeItCurrent()->setName('Resumen');

        foreach (self::resumen($empresas) as $renglon) {
            $escritor->addRow(Row::fromValues($renglon));
        }

        $escritor->close();

        return $empresas->count();
    }

    /**
     * Escribe el ZIP con los logotipos, renombrados con el nombre de cada
     * organización, y devuelve cuántos entraron.
     */
    public static function escribirZipLogos(Builder $consulta, string $ruta): int
    {
        $disco = Storage::disk('public');

        $zip = new ZipArchive;
        $zip->open($ruta, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $total = 0;

        foreach (self::empresas($consulta) as $empresa) {
            $logo = $empresa->logo_reconocimiento;

            if (blank($logo) || ! $disco->exists($logo)) {
                continue;
            }

            $carpeta = Str::ascii($empresa->region ?? RegionesCoahuila::SIN_REGION);

            $zip->addFile($disco->path($logo), $carpeta.'/'.self::nombreLogo($empresa));
            $total++;
        }

        if ($total === 0) {
            // Un ZIP vacío no se puede cerrar: se deja el archivo en cero y
            // quien llama avisa que no hay logotipos.
            $zip->addFromString('LEEME.txt', 'Ninguna organización del listado ha subido su logotipo.');
        }

        $zip->close();

        return $total;
    }

    public static function nombreArchivo(string $prefijo, string $extension, ?string $region = null): string
    {
        $alcance = $region ? Str::ascii($region) : 'Todas-las-regiones';

        return $prefijo.'_'.str_replace(' ', '-', $alcance).'_'.now()->format('Y-m-d').'.'.$extension;
    }

    /**
     * Segunda hoja: el conteo por nivel —el recuadro que Angélica traía a un
     * lado de su tabla— y cuántas faltan por confirmar.
     *
     * @param  Collection<int, Empresa>  $empresas
     * @return list<list<string|int>>
     */
    private static function resumen(Collection $empresas): array
    {
        $renglones = [
            ['Distintivo +Feliz — Reconocimientos'],
            [''],
            ['Organizaciones en el archivo', $empresas->count()],
            ['Con datos confirmados', $empresas->filter(fn (Empresa $e) => filled($e->reconocimiento_confirmado_at))->count()],
            ['Con logotipo', $empresas->filter(fn (Empresa $e) => filled($e->logo_reconocimiento))->count()],
            [''],
            ['Por nivel de madurez'],
        ];

        foreach (Empresa::NIVELES_MADUREZ as $nivel) {
            $renglones[] = [$nivel, $empresas->where('nivel_madurez_asignado', $nivel)->count()];
        }

        $sinNivel = $empresas->filter(fn (Empresa $e) => blank($e->nivel_madurez_asignado))->count();

        if ($sinNivel > 0) {
            $renglones[] = ['Sin nivel asignado', $sinNivel];
        }

        $renglones[] = [''];
        $renglones[] = ['Fecha de generación', now()->format('d/m/Y H:i')];
        $renglones[] = [
            'Aviso',
            'El archivo contiene nombres y teléfonos personales: compártelo solo con quien elabora los reconocimientos y las invitaciones.',
        ];

        return $renglones;
    }
}
