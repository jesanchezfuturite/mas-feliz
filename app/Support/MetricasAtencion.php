<?php

namespace App\Support;

use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\SolicitudReferencia;
use App\Models\Tamizaje;
use Illuminate\Support\Collection;

/**
 * Métricas de avance de la atención en salud mental.
 *
 * Angélica pidió que evaluadores y administración puedan dar seguimiento con
 * "métricas y generales de avances, no datos de los trabajadores": cuántos
 * tamizajes llevan, a cuántas personas han atendido y a cuántas han derivado.
 * Por eso aquí solo se cuentan registros; nunca se expone información
 * identificable de un colaborador.
 *
 * El alcance se define con una lista de empresas: el evaluador ve solo las que
 * tiene asignadas y la administración las ve todas.
 */
class MetricasAtencion
{
    /**
     * @param  array<int>|null  $empresaIds  null = todas las empresas.
     */
    public function __construct(private readonly ?array $empresaIds = null) {}

    public static function paraEmpresas(?array $empresaIds = null): self
    {
        return new self($empresaIds);
    }

    private function acotar($query)
    {
        return $this->empresaIds === null
            ? $query
            : $query->whereIn('empresa_id', $this->empresaIds);
    }

    /** Igual que acotar(), pero sobre la tabla de empresas, donde la llave es `id`. */
    private function acotarEmpresas($query)
    {
        return $this->empresaIds === null
            ? $query
            : $query->whereIn('id', $this->empresaIds);
    }

    /**
     * Universo convocado: la suma de colaboradores que las organizaciones
     * declararon al registrarse (`empresas.numero_trabajadores`).
     */
    public function colaboradoresRegistrados(): int
    {
        return (int) $this->acotarEmpresas(Empresa::query())->sum('numero_trabajadores');
    }

    /** Todos los cuestionarios respondidos, incluidas las no participaciones. */
    public function tamizajesAplicados(): int
    {
        return $this->acotar(Tamizaje::query())->count();
    }

    /**
     * Quien declina participar deja un registro con nivel "No participó" para
     * que cuente en el avance sin contaminar los resultados clínicos.
     */
    public function participaron(): int
    {
        return $this->acotar(Tamizaje::query())
            ->where('nivel_riesgo_general', '!=', 'No participó')
            ->count();
    }

    public function noParticiparon(): int
    {
        return $this->acotar(Tamizaje::query())
            ->where('nivel_riesgo_general', 'No participó')
            ->count();
    }

    /** Personas cuyo tamizaje salió en riesgo moderado o urgente. */
    public function detectadosEnRiesgo(): int
    {
        return $this->acotar(Tamizaje::query())
            ->whereIn('nivel_riesgo_general', ['Moderado', 'Urgente'])
            ->count();
    }

    public function casosPorEstatus(): Collection
    {
        return $this->acotar(CasoSeguimiento::query())
            ->selectRaw('estatus_atencion, count(*) as total')
            ->groupBy('estatus_atencion')
            ->pluck('total', 'estatus_atencion');
    }

    /**
     * Conteo de cada estatus del catálogo, incluidos los que van en cero.
     *
     * Punto 7 de Angélica: el evaluador necesita ver todas las opciones que la
     * organización puede asignar —cuántos en seguimiento, cuántos abandonaron—
     * y no solo las que hoy tienen casos.
     *
     * @return array<string,int>
     */
    public function casosPorCadaEstatus(): array
    {
        $conteos = $this->casosPorEstatus();
        $desglose = [];

        foreach (array_keys(CasoSeguimiento::ESTATUS_ATENCION) as $estatus) {
            $desglose[$estatus] = (int) $conteos->get($estatus, 0);
        }

        return $desglose;
    }

    public function casosAbiertos(): int
    {
        return (int) $this->casosPorEstatus()->get('En seguimiento', 0);
    }

    public function casosAbandonados(): int
    {
        return (int) $this->casosPorEstatus()->get('Abandonó', 0);
    }

    public function casosCanalizados(): int
    {
        return (int) $this->casosPorEstatus()->get('Canalizado', 0);
    }

    public function casosCerrados(): int
    {
        return (int) $this->casosPorEstatus()->get('Cerrado satisfactorio', 0);
    }

    /**
     * Casos que se cerraron sin que la persona llegara a ser atendida. Se
     * cuentan aparte de los cerrados satisfactoriamente porque son justo los
     * que Salud querría revisar.
     */
    public function casosCerradosSinAtender(): int
    {
        return (int) $this->casosPorEstatus()->get('Cerrado no atendido', 0);
    }

    public function referenciasEnviadas(): int
    {
        return $this->acotar(SolicitudReferencia::query())->count();
    }

    public function referenciasConCita(): int
    {
        return $this->acotar(SolicitudReferencia::query())->whereNotNull('fecha_cita')->count();
    }

    public function referenciasSinCita(): int
    {
        return $this->referenciasEnviadas() - $this->referenciasConCita();
    }

    /**
     * Avance de participación sobre el universo convocado.
     *
     * Angélica lo pidió explícito (17/08/2026): el porcentaje va sobre el total
     * de colaboradores que la organización registró al inicio, y quien declina
     * también cuenta, porque igual fue convocado. Antes se dividía entre los
     * cuestionarios respondidos, así que en realidad medía la tasa de
     * consentimiento y salía siempre alto: 94% en una empresa de 102
     * colaboradores con 14 tamizajes. La meta indispensable del 90% se lee
     * contra este número.
     *
     * null = la organización no declaró su universo; no hay porcentaje que dar
     * y es mejor decirlo que inventar un 0%.
     */
    public function porcentajeParticipacion(): ?int
    {
        $universo = $this->colaboradoresRegistrados();

        if ($universo === 0) {
            return null;
        }

        return (int) round($this->tamizajesAplicados() * 100 / $universo);
    }

    /**
     * La otra lectura que pidió Angélica: de quienes abrieron el cuestionario,
     * cuántos otorgaron su consentimiento. Es lo que el tablero mostraba antes
     * como "participación", y sigue siendo útil, pero como dato aparte.
     */
    public function porcentajeConsentimiento(): ?int
    {
        $respondieron = $this->tamizajesAplicados();

        if ($respondieron === 0) {
            return null;
        }

        return (int) round($this->participaron() * 100 / $respondieron);
    }
}
