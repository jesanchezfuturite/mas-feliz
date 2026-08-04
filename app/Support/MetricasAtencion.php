<?php

namespace App\Support;

use App\Models\CasoSeguimiento;
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

    public function casosAbiertos(): int
    {
        return (int) $this->casosPorEstatus()->get('En seguimiento', 0);
    }

    public function casosCanalizados(): int
    {
        return (int) $this->casosPorEstatus()->get('Canalizado', 0);
    }

    public function casosCerrados(): int
    {
        return (int) $this->casosPorEstatus()->get('Cerrado satisfactorio', 0);
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
     * Porcentaje de participación sobre los cuestionarios enviados.
     */
    public function porcentajeParticipacion(): int
    {
        $total = $this->tamizajesAplicados();

        return $total === 0 ? 0 : (int) round($this->participaron() * 100 / $total);
    }
}
