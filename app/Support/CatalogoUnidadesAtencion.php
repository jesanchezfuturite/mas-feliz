<?php

namespace App\Support;

/**
 * Unidades de atención de la Secretaría de Salud a las que se puede referir a
 * una persona.
 *
 * El listado lo entregó Angélica R el 06/08/2026 desde el concentrado oficial
 * de Salud (hoja "unidades de atención" del documento "ATENCIÓN EMPRESAS
 * +FELIZ"). Los nombres se guardan tal cual vienen del concentrado, con sus
 * abreviaturas y su ortografía, para que coincidan con los registros de Salud
 * y se puedan cotejar sin traducciones de por medio.
 *
 * Si Salud actualiza el catálogo, se edita esta lista.
 */
class CatalogoUnidadesAtencion
{
    /** Se agrega al final del desplegable: permite capturar una unidad fuera del catálogo. */
    public const OTRO = 'Otro';

    public const UNIDADES = [
        'CENTRO INTEGRAL DE SALUD MENTAL',
        'CECOSAMA PIEDRAS NEGRAS',
        'CECOSAMA ACUÑA',
        'CECOSAMA NUEVA ROSITA',
        'CECOSAMA MONCLOVA',
        'CECOSAMA TORREON',
        'CECOSAMA MATAMOROS',
        'CECOSAMA SAN PEDRO',
        'CECOSAMA SALTILLO',
        'HG PIEDRAS NEGRAS',
        'HG ALLENDE',
        'HG ACUÑA',
        'CSCH PARRAS',
        'CSCH SABINAS',
        'HG MUZQUIZ PALAU',
        'HG NUEVA ROSITA',
        'HG MONCLOVA',
        'HG CUATROCIENEGAS',
        'HG TORREON',
        'HOSPITAL INTEGRAL MATAMOROS',
        'HG FCO I. MADERO',
        'HG SAN PEDRO',
        'HG DE SALTILLO',
        'CENTRO DE SALUD URBANO 01 NÚCLEO BÁSICO GUERRERO',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO HIDALGO',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEOS BÁSICOS NAVA',
        'CENTRO DE SALUD URBANO DE 03 NÚCLEOS MUNDO NUEVO DE PIEDRAS NEGRAS',
        'URBANO DE 01 NÚCLEO BÁSICO COL. BUENOS AIRES',
        'URBANO DE 01 NÚCLEO BAS. COL. LÁZARO CÁRDENAS',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO EJ. PIEDRAS NEGRAS',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO VILLA UNIÓN',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEOS BÁSICOS VILLA DE FUENTE',
        'CENTRO DE SALUD URBANO DE O1 NÚCLEO BÁSICO JIMÉNEZ',
        'RURAL DE 01 NÚCLEO BÁSICO PALMIRA',
        'RURAL DE 01 NÚCLEO BÁSICO SAN CARLOS',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEOS BÁSICOS MORELOS',
        'CENTRO DE SALUD URBANO ZARAGOZA',
        'RURAL DE 01 NÚCLEO BÁSICO SANTA EULALIA',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO JUÁREZ',
        'CENTRO DE SALUD URBANO DE 6 NÚCLEOS BÁSICOS MÚZQUIZ',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO MINAS DE BARROTERÀN',
        'URBANO DE 01 NÚCLEO BÁSICO PALAÚ',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO PROGRESO',
        'RURAL DE 01 NÚCLEOS BÁSICOS SAN JOSÉ DE AURA',
        'CENTRO DE SALUD CON HOSPITAL SABINAS',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO CLOETE',
        'RURAL DE 01 NÚCLEO BÁSICO AGUJITA',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO SAN JUAN DE SABINAS',
        'RURAL DE 01 NÚCLEO BÁSICO SANTA MARÍA',
        'CENTRO DE SALUD RURAL MARGARITA GONZALEZ (COMUNIDAD NEGROS MASCOGOS)',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO ABASOLO',
        'RURAL DE 01 NÚCLEO BÁSICO CONGREGACIÓN RODRÍGUEZ',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO CANDELA',
        'URBANO DE 01 NÚCLEO BÁSICO CASTAÑOS',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO ESCOBEDO',
        'RURAL DE 01 NÚCLEO EJ.PRIMERO DE MAYO',
        'URBANO DE 01 NÚCLEOS BÁSICOS COL. BORJA',
        'URBANO DE 02 NÚCLEOS BÁSICOS OCCIDENTAL',
        'URBANO DE 01 NÚCLEOS BÁSICOS CEFARE',
        'CENTRO DE SALUD URBANO GUADALUPE DE MONCLOVA',
        'URBANO DE 02 NÚCLEOS BAS. LEANDRO VALLE',
        'URBANO DE 02 NÚCLEOS BAS. INDEPENDENCIA',
        'URBANO DE 03 NÚCLEOS BÁSICOS SAN MIGUEL',
        'URBANO DE 02 NÚCLEOS BAS. COL. PURÍSIMA',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO NADADORES',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO SAN BUENAVENTURA',
        'URBANO DE 02 NÚCLEOS BÁSICOS COL. PRADERAS',
        'URBANO DE 02 NÚCLEO BÁSICO MONCLOVA ORIENTE',
        'RURAL DE 01 NÚCLEO BÁSICO, LA CRUZ',
        'CENTRO DE SALUD RURAL DE 01 NUCLEO BÁSICO, SARDINAS',
        'RURAL DE 01 NÚCLEO BAS.ESTANQUE DE PALOMAS',
        'RURAL DE 01 NÚCLEO BÁSICO EL VENADO',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEOS BÁSICOS LAMADRID',
        'CENTRO DE SALUD URBANO DE 03 NÚCLEOS BÁSICOS OCAMPO',
        'RURAL DE 01 NUC. BAS. BOQUILLAS DEL CARMEN',
        'RURAL DE 01 NÚCLEO BÁSICO LA ROSITA',
        'RURAL DE 01 NÚCLEO BÁSICO SAN MIGUEL',
        'CENTRO DE SALUD ACEBUCHES',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO SACRAMENTO',
        'URBANO DE 03 NÚCLEOS BÁSICOS COYOTE',
        'RURAL DE 01 NÚCLEO BÁSICO LA LUZ',
        'RURAL DE 01 NÚCLEO BÁSICO EL PILAR',
        'RURAL DE 02 NÚCLEOS BAS. SANTO NIÑO AGUANAVAL',
        'RURAL DE 01 NÚCLEOS BÁSICOS SOLIMA',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEOS BÁSICOS EL CAMBIO',
        'RURAL DE 01 NÚCLEOS BÁSICOS CONGREGACIÓN HIDALGO',
        'URBANO DE 01 NÚCLEO BÁSICO AQUILES SERDÁN',
        'URBANO DE 01 NÚCLEO BÁSICO BRAULIO FERNÁNDEZ AGUIRRE',
        'URBANO DE 01 NÚCLEO BÁSICO COMPRESORA',
        'URBANO DE 01 NÚCLEO BÁSICO CAROLINAS',
        'URBANO DE 01 NÚCLEO BÁSICO DIV. DEL NORTE',
        'URBANO DE 01 NÚCLEO BÁSICO SAN ANTONIO DE LOS BRAVOS',
        'URBANO DE 01 NÚCLEO BÁSICO SAN JOAQUÍN',
        'URBANO DE 03 NÚCLEO BÁSICO LAS LUISAS',
        'URBANO DE 06 NÚCLEOS BÁSICOS ABASTOS',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEOS BÁSICOS LA CONCHA',
        'RURAL DE 01 NÚCLEO BÁSICO LA FLOR DE JIMULCO',
        'RURAL DE 01 NÚCLEOS BÁSICOS JUAN EUGENIO',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO LA PARTIDA',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEO BÁSICO VIESCA',
        'RURAL DE 01 NUC. BÁSICO EMILIANO ZAPATA',
        'RURAL DE 01 NÚCLEO BÁSICO SAN JOSÉ DEL AGUAJE',
        'RURAL DE 01 NÚCLEO BÁSICO LA VENTANA',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO LA JOYA',
        'RURAL DE 01 NUCLEO BASICO JALISCO',
        'UNIDAD DE CONSULTA EXTERNA TORREÓN',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEOS BÁSICOS BATOPILAS',
        'RURAL DE 01 NÚCLEO BÁSICO CORUÑA',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEOS BÁSICOS LEQUEITIO',
        'RURAL DE 01 NÚCLEOS BÁSICOS LA PINTA',
        'CENTRO DE SALUD RURAL DE 01 NUC. BAS. SAN JOSÉ DE LA NIÑA',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO CHULAVISTA',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO ALBIA',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEOS BÁSICOS LA ROSITA',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO BEGOÑA',
        'CENTRO DE SALUD URBANO DE 02 NÚCLEOS BÁSICOS LUCHANA',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEOS BÁSICOS MAYRÁN',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO EL RETIRO',
        'RURAL DE 02 NÚCLEOS BÁSICOS SAN IGNACIO',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEOS BÁSICO SAN LORENZO',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO SAN MARCOS',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO SOFÍA DE ARRIBA',
        'CENTRO DE SALUD CHARCOS DE RISA',
        'CENTRO DE SALUD NUEVA VICTORIA',
        'CENTRO DE SALUD URBANO DE 01 NÚCLEO BÁSICO SIERRA MOJADA',
        'CENTRO DE SALUD RURAL DE 01 NÚCLEO BÁSICO ESMERALDA',
        'CENTRO DE SALUD URBANO DE 01 NUC. BAS. COL. LÁZARO CÁRDENAS',
        'CENTRO DE SALUD URBANO DE 01 NUC. BAS. COL. MIGUEL HIDALGO',
        'CENTRO DE SALUD URBANO HÉRCULES',
        'CENTRO DE SALUD CUAUTLA',
        'URBANO DE 02 NÚCLEOS BÁSICOS ARTEAGA',
        'RURAL DE 01 NÚCLEO BÁSICO HUACHICHIL',
        'RURAL DE 01 NÚCLEO BÁSICO LOS LIRIOS',
        'RURAL DE 02 NUC. BAS. SAN ANTONIO DE LAS ALAZANAS',
        'RURAL DE 01 NÚCLEO BÁSICO EL TUNAL',
        'CENTRO DE SALUD CON SERVICIOS AMPLIADOS GENERAL CEPEDA',
        'RURAL DE 01 NÚCLEO BÁSICO LA ROSA',
        'RURAL DE 01 NÚCLEO BÁSICO SABANILLAS',
        'RURAL DE 01 NUC. BAS. SAN JOSÉ PATAGALANA',
        'URBANO DE 04 NUCLEOS BÁSICOS RAMOS ARIZPE',
        'RURAL DE 01 NÚCLEO BÁSICO FRAUSTRO',
        'RURAL DE 01 NÚCLEO BÁSICO HIPÓLITO',
        'RURAL DE 02 NÚCLEOS BÁSICOS PAREDÓN',
        'CENTRO DE SALUD URBANO MADERO',
        'URBANO DE 02 NÚCLEOS BÁSICO NVA. JERUSALEM',
        'URBANO DE 04 NÚCLEOS BÁSICOS COL. ASTURIAS',
        'URBANO DE 03 NÚCLEOS BÁSICOS COL. GIRASOL',
        'URBANO DE 03 NÚCLEOS BÁSICOS AMPLIACIÓN MORELOS',
        'URBANO DE 03 NÚCLEOS BÁSICOS SATÉLITE',
        'URBANO DE 09 NÚCLEOS BAS. LOS GONZÁLEZ',
        'RURAL DE 01 NÚCLEO BÁSICO DERRAMADERO',
        'RURAL DE 01 NÚCLEO BÁSICO GÓMEZ FARIAS',
        'RURAL DE 01 NUC. BÁSICO SAN JUAN DE LA VAQUERÍA',
        'RURAL DE 01 NUC. BÁSICO SAN JUAN DEL RETIRO',
        'RURAL DE 01 NÚCLEO BÁSICO LA VENTURA',
        'RURAL DE 01 NÚCLEO BÁSICO, BELLA UNIÓN',
    ];

    /**
     * Opciones agrupadas por tipo de unidad. Con más de 150 opciones, el
     * desplegable se usa siempre con búsqueda; los grupos ayudan a ubicarse
     * cuando se navega en lugar de escribir.
     *
     * @return array<string, array<string, string>|string>
     */
    public static function opciones(): array
    {
        $grupos = [];

        foreach (self::UNIDADES as $unidad) {
            $grupos[self::grupoDe($unidad)][$unidad] = $unidad;
        }

        $grupos['Otra unidad'][self::OTRO] = 'Otro (especificar)';

        return $grupos;
    }

    /**
     * Cuántas opciones debe alcanzar a mostrar el desplegable.
     *
     * Los Select buscables de Filament recortan la lista a 50 opciones. Con el
     * catálogo completo eso la dejaba cortada en "CENTRO DE SALUD RURAL
     * MARGARITA GONZALEZ (COMUNIDAD NEGROS MASCOGOS)", que es justo la número
     * 50, y las 106 unidades restantes solo aparecían si se escribía en el
     * buscador. Se deriva del tamaño del catálogo para que el límite siga
     * siendo correcto si Salud lo actualiza.
     */
    public static function limiteOpciones(): int
    {
        return count(self::UNIDADES) + 1;
    }

    /**
     * Lista plana, para validar o para mostrar sin agrupar.
     *
     * @return array<string, string>
     */
    public static function planas(): array
    {
        $opciones = array_combine(self::UNIDADES, self::UNIDADES);
        $opciones[self::OTRO] = 'Otro (especificar)';

        return $opciones;
    }

    private static function grupoDe(string $unidad): string
    {
        return match (true) {
            str_starts_with($unidad, 'CECOSAMA') => 'CECOSAMA',
            str_starts_with($unidad, 'CENTRO INTEGRAL') => 'Centro Integral de Salud Mental',
            str_starts_with($unidad, 'HG ')
                || str_starts_with($unidad, 'HOSPITAL')
                || str_starts_with($unidad, 'CSCH') => 'Hospitales',
            default => 'Centros de Salud',
        };
    }
}
