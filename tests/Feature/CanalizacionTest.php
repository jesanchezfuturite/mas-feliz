<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\Autoevaluacions\Schemas\AutoevaluacionForm;
use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\SolicitudReferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanalizacionTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Canalización',
            'municipio' => 'Torreón',
            'dias_horario_servicio' => 'Lunes a viernes 9am-6pm',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'canalizacion@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 50,
        ]);
    }

    private function crearCaso(array $extra = []): CasoSeguimiento
    {
        return CasoSeguimiento::create(array_merge([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => 'Persona Test',
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'Canalizado',
        ], $extra));
    }

    public function test_los_folios_de_referencia_son_correlativos_y_unicos(): void
    {
        $primera = SolicitudReferencia::create([
            'caso_seguimiento_id' => $this->crearCaso()->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Persona Uno',
        ]);

        $segunda = SolicitudReferencia::create([
            'caso_seguimiento_id' => $this->crearCaso()->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Saltillo',
            'nombre_usuario' => 'Persona Dos',
        ]);

        $anio = now()->year;

        $this->assertSame("REF-{$anio}-0001", $primera->folio);
        $this->assertSame("REF-{$anio}-0002", $segunda->folio);
    }

    public function test_una_solicitud_sin_fecha_de_cita_no_esta_agendada(): void
    {
        $caso = $this->crearCaso();

        $solicitud = SolicitudReferencia::create([
            'caso_seguimiento_id' => $caso->id,
            'empresa_id' => $this->empresa->id,
            'municipio' => 'Torreón',
            'nombre_usuario' => 'Persona Test',
        ]);

        $this->assertFalse($solicitud->esta_agendada);

        $solicitud->update([
            'fecha_cita' => now()->addWeek(),
            'unidad_atencion' => 'Otro',
            'unidad_atencion_otra' => 'Clínica 16',
        ]);

        $this->assertTrue($solicitud->fresh()->esta_agendada);
        // Cuando la unidad es "Otro" se muestra el texto libre que capturó el Gestor.
        $this->assertSame('Clínica 16', $solicitud->fresh()->unidad_atencion_completa);
    }

    public function test_el_caso_resuelve_el_servicio_otro_a_texto_libre(): void
    {
        $caso = $this->crearCaso([
            'servicios' => ['Psicología', 'Otro'],
            'servicio_otro' => 'Trabajo social',
        ]);

        $this->assertSame('Psicología, Trabajo social', $caso->servicios_texto);
        $this->assertSame('N/A', $this->crearCaso()->servicios_texto);
    }

    public function test_una_autoevaluacion_vacia_reporta_los_veinte_criterios_incompletos(): void
    {
        $this->assertCount(20, AutoevaluacionForm::criteriosIncompletos([]));
        $this->assertCount(20, AutoevaluacionForm::criteriosIncompletos(null));
    }

    public function test_una_autoevaluacion_contestada_no_reporta_pendientes(): void
    {
        $this->assertEmpty(AutoevaluacionForm::criteriosIncompletos($this->respuestasCompletas()));
    }

    public function test_no_aplica_cuenta_como_respuesta_valida(): void
    {
        $respuestas = $this->respuestasCompletas();
        $respuestas['criterio_7']['elemento_2']['score'] = 'NA';

        $this->assertEmpty(AutoevaluacionForm::criteriosIncompletos($respuestas));
    }

    public function test_detecta_el_criterio_exacto_que_quedo_sin_contestar(): void
    {
        $respuestas = $this->respuestasCompletas();
        unset($respuestas['criterio_7']['elemento_2']);
        $respuestas['criterio_12']['elemento_1']['score'] = '';

        $pendientes = AutoevaluacionForm::criteriosIncompletos($respuestas);

        $this->assertSame([7 => 1, 12 => 1], $pendientes);
    }

    /**
     * Todos los elementos de los 20 criterios calificados con "Cumple".
     */
    private function respuestasCompletas(): array
    {
        $respuestas = [];

        foreach (AutoevaluacionForm::ELEMENTOS_POR_CRITERIO as $criterio => $totalElementos) {
            for ($elemento = 1; $elemento <= $totalElementos; $elemento++) {
                $respuestas["criterio_{$criterio}"]["elemento_{$elemento}"]['score'] = '10';
            }
        }

        return $respuestas;
    }
}
