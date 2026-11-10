<?php

namespace App\Support;

/**
 * Las cinco regiones de Coahuila y sus 38 municipios.
 *
 * La región no se captura: sale del municipio de la empresa. Los
 * reconocimientos se entregan por región (Angélica, 31/10/2026: «el martes
 * entregamos los primeros en Laguna, y así nos iremos en cada región»), así
 * que los listados y la exportación se filtran con este mapa.
 */
class RegionesCoahuila
{
    /** Lo que se muestra cuando el municipio no es de Coahuila o viene vacío. */
    public const SIN_REGION = 'Sin región';

    /** @var array<string, list<string>> */
    public const MUNICIPIOS = [
        'Laguna' => [
            'Francisco I. Madero', 'Matamoros', 'San Pedro', 'Torreón', 'Viesca',
        ],
        'Sureste' => [
            'Arteaga', 'General Cepeda', 'Parras', 'Ramos Arizpe', 'Saltillo',
        ],
        'Centro-Desierto' => [
            'Abasolo', 'Candela', 'Castaños', 'Cuatro Ciénegas', 'Escobedo', 'Frontera',
            'Lamadrid', 'Monclova', 'Nadadores', 'Ocampo', 'Sacramento',
            'San Buenaventura', 'Sierra Mojada',
        ],
        'Carbonífera' => [
            'Juárez', 'Múzquiz', 'Progreso', 'Sabinas', 'San Juan de Sabinas',
        ],
        'Norte' => [
            'Acuña', 'Allende', 'Guerrero', 'Hidalgo', 'Jiménez', 'Morelos', 'Nava',
            'Piedras Negras', 'Villa Unión', 'Zaragoza',
        ],
    ];

    /** La región de un municipio, o null si no está en el mapa. */
    public static function de(?string $municipio): ?string
    {
        $municipio = trim((string) $municipio);

        if ($municipio === '') {
            return null;
        }

        foreach (self::MUNICIPIOS as $region => $municipios) {
            if (in_array($municipio, $municipios, true)) {
                return $region;
            }
        }

        return null;
    }

    /**
     * Las regiones, para los desplegables.
     *
     * @return array<string, string>
     */
    public static function opciones(): array
    {
        $regiones = array_keys(self::MUNICIPIOS);

        return array_combine($regiones, $regiones);
    }

    /**
     * Los municipios de una región, para filtrar consultas.
     *
     * @return list<string>
     */
    public static function municipiosDe(?string $region): array
    {
        return self::MUNICIPIOS[$region] ?? [];
    }

    /**
     * Posición de la región en el orden en que se listan, para ordenar la
     * exportación; lo que no tiene región va al final.
     */
    public static function orden(?string $region): int
    {
        $posicion = array_search($region, array_keys(self::MUNICIPIOS), true);

        return $posicion === false ? count(self::MUNICIPIOS) : $posicion;
    }
}
