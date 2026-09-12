<?php

namespace App\Support;

use App\Models\Empresa;
use App\Models\Tamizaje;
use Illuminate\Contracts\Database\Eloquent\Builder;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

/**
 * Exporta a Excel el listado de personas tamizadas con sus resultados.
 *
 * Lo pidió Angélica el 03/09/2026: las empresas ya están explorando sus
 * resultados y quieren llevárselos en una hoja de cálculo.
 *
 * Los encabezados y los valores son los mismos que la plataforma muestra en
 * pantalla —"Síntomas de...", "Indicadores de Conducta suicida", la prioridad
 * de {@see PrioridadAtencion} y el título del ASQ de {@see ResultadoAsq}— para
 * que la hoja se pueda comparar contra el listado sin traducir nada.
 *
 * Sale un .xlsx de verdad, con openspout (ya viene con filament/actions), y no
 * un CSV: el Excel de Windows abre los CSV en UTF-8 con los acentos partidos y
 * la hoja la van a leer en oficinas de gobierno.
 */
class ExportacionTamizajes
{
    /**
     * Columnas de la hoja "Resultados", en orden. Las de puntaje traen el
     * rango del instrumento porque el número solo no dice nada.
     */
    public const ENCABEZADOS = [
        'Nombre Completo',
        'Sexo',
        'Grupo de Edad',
        'Departamento',
        'Tiempo trabajando',
        'Teléfono',
        'Correo',
        'Fecha',
        'Participó',
        'Síntomas de Ansiedad',
        'Puntaje GAD-7 (0-21)',
        'Síntomas de Depresión',
        'Puntaje PHQ-9 (0-27)',
        'Indicadores de Conducta suicida',
        'Puntaje ASQ (0-4)',
        PrioridadAtencion::ETIQUETA,
        'Acción que corresponde',
        'Comentarios',
    ];

    /**
     * Encabezados del archivo. Cuando el archivo cruza organizaciones —el
     * admin exportando varias empresas— se antepone la columna
     * "Organización", porque si no, no hay forma de saber de quién es cada
     * renglón.
     *
     * @return list<string>
     */
    public static function encabezados(?Empresa $empresa): array
    {
        return $empresa
            ? self::ENCABEZADOS
            : array_merge(['Organización'], self::ENCABEZADOS);
    }

    /** Tipo con el que Excel reconoce el archivo al abrirlo. */
    public const TIPO_MIME = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /** Posición (base 0) de la columna "Fecha", que va como fecha y no como texto. */
    private const COLUMNA_FECHA = 7;

    /**
     * Un renglón de la hoja, en el orden de {@see encabezados()}.
     *
     * @return list<string|int|\DateTimeInterface>
     */
    public static function fila(Tamizaje $tamizaje, ?string $organizacion = null): array
    {
        $participo = (bool) $tamizaje->consentimiento_otorgado;

        // Quien declinó participar no tiene resultados: sus puntajes quedaron
        // en 0 y sus niveles vacíos. Las celdas van en blanco —un 0 se leería
        // como "sin síntomas"—, pero el renglón se queda porque cuenta en el
        // avance de participación.
        $siParticipo = fn (string|int $valor): string|int => $participo ? $valor : '';

        $departamento = $tamizaje->actividad_trabajo === 'Otra'
            ? $tamizaje->actividad_trabajo_otra
            : $tamizaje->actividad_trabajo;

        $fila = [
            (string) $tamizaje->nombre_completo,
            (string) $tamizaje->genero,
            (string) $tamizaje->edad,
            (string) $departamento,
            (string) $tamizaje->tiempo_trabajando,
            (string) $tamizaje->telefono,
            (string) $tamizaje->correo,
            $tamizaje->created_at ?: '',
            $participo ? 'Sí' : 'No',
            $siParticipo((string) $tamizaje->nivel_ansiedad),
            $siParticipo((int) $tamizaje->riesgo_ansiedad),
            $siParticipo((string) $tamizaje->nivel_depresion),
            $siParticipo((int) $tamizaje->riesgo_depresion),
            $siParticipo(ResultadoAsq::titulo($tamizaje)),
            $siParticipo((int) $tamizaje->riesgo_conducta_suicida),
            (string) $tamizaje->nivel_riesgo_general,
            $siParticipo((string) ResultadoAsq::accion($tamizaje)),
            (string) $tamizaje->comentarios,
        ];

        if ($organizacion !== null) {
            array_unshift($fila, $organizacion);
        }

        return $fila;
    }

    /**
     * Escribe el archivo y devuelve cuántos tamizajes quedaron dentro.
     *
     * Recorre la consulta con `cursor()` para que la empresa más grande
     * (miles de tamizajes) no tenga que caber en memoria.
     */
    public static function escribir(Builder $consulta, ?Empresa $empresa, string $ruta): int
    {
        $encabezados = self::encabezados($empresa);
        $desplazamiento = count($encabezados) - count(self::ENCABEZADOS);

        // Los nombres de las organizaciones se traen de una sola consulta: la
        // tabla de empresas es chica y `cursor()` no hace eager loading, así
        // que leer `$tamizaje->empresa` por renglón serían miles de consultas.
        $organizaciones = $empresa ? null : Empresa::pluck('nombre_empresa', 'id');

        $opciones = new Options;
        $opciones->setColumnWidthForRange(34, 1, 1 + $desplazamiento);
        $opciones->setColumnWidthForRange(20, 2 + $desplazamiento, 8 + $desplazamiento);
        $opciones->setColumnWidthForRange(24, 9 + $desplazamiento, count($encabezados));

        $escritor = new Writer($opciones);
        $escritor->openToFile($ruta);
        $escritor->getCurrentSheet()->setName('Resultados');

        $escritor->addRow(Row::fromValues(
            $encabezados,
            (new Style)->setFontBold()->setShouldWrapText()->setBackgroundColor('E5E7EB'),
        ));

        $estiloFecha = (new Style)->setFormat('dd/mm/yyyy');
        $total = 0;

        foreach ($consulta->cursor() as $tamizaje) {
            $organizacion = $organizaciones === null
                ? null
                : (string) ($organizaciones[$tamizaje->empresa_id] ?? '');

            $escritor->addRow(Row::fromValuesWithStyles(
                self::fila($tamizaje, $organizacion),
                null,
                [self::COLUMNA_FECHA + $desplazamiento => $estiloFecha],
            ));

            $total++;
        }

        $escritor->addNewSheetAndMakeItCurrent()->setName('Información');

        foreach (self::informacion($empresa, $total) as $renglon) {
            $escritor->addRow(Row::fromValues($renglon));
        }

        $escritor->close();

        return $total;
    }

    public static function nombreArchivo(?Empresa $empresa): string
    {
        $identificador = $empresa?->folio ?: 'Todas-las-organizaciones';

        return 'Tamizajes_'.$identificador.'_'.now()->format('Y-m-d').'.xlsx';
    }

    /**
     * Segunda hoja: de dónde salió el archivo y la nota de la escala. La nota
     * va en su propia hoja para que la de resultados quede con un solo
     * renglón de encabezados y los filtros de Excel funcionen.
     *
     * @return list<list<string|int>>
     */
    private static function informacion(?Empresa $empresa, int $total): array
    {
        $renglones = [
            ['Distintivo +Feliz — Diagnóstico/ Tamizaje'],
            [''],
        ];

        if ($empresa) {
            $renglones[] = ['Organización', (string) $empresa->nombre_empresa];

            if ($empresa->folio) {
                $renglones[] = ['Folio', (string) $empresa->folio];
            }
        } else {
            $renglones[] = ['Alcance', 'Todas las organizaciones del listado'];
        }

        $renglones[] = ['Registros exportados', $total];
        $renglones[] = ['Fecha de generación', now()->format('d/m/Y H:i')];
        $renglones[] = [''];
        $renglones[] = ['Nota', PrioridadAtencion::NOTA];
        $renglones[] = [
            'Aviso',
            'El archivo contiene datos personales y de salud de las personas evaluadas: '
            .'resguárdalo y compártelo solo con quien participa en su atención.',
        ];

        return $renglones;
    }
}
