<?php

namespace Tests\Feature;

use App\Filament\Empresa\Resources\CasoSeguimientos\Pages\ListCasoSeguimientos;
use App\Models\CasoSeguimiento;
use App\Models\Empresa;
use App\Models\Setting;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Angélica (31/08/2026): al capturar el consentimiento en línea, "si a uno le
 * pongo que sí, se quitan en otros o se cambia la respuesta". La columna es
 * booleana en el modelo pero el select nativo compara contra '1'/'0' como
 * texto: el estado true/false no coincidía con ningún option y la
 * resincronización que Filament hace de toda la tabla tras cada guardado
 * pintaba las demás celdas vacías o cambiadas.
 */
class ConsentimientoEnLineaTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::updateOrCreate(['key' => 'global_config'], ['herramientas_empresa_activas' => true]);

        $this->empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Consentimiento',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'consentimiento@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
        ]);

        $this->actingAs($this->empresa, 'empresa');
        Filament::setCurrentPanel(Filament::getPanel('empresa'));
    }

    private function caso(string $nombre, ?bool $consentimiento = null): CasoSeguimiento
    {
        return CasoSeguimiento::create([
            'empresa_id' => $this->empresa->id,
            'identificador_empleado' => $nombre,
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'En seguimiento',
            'consentimiento' => $consentimiento,
        ]);
    }

    /**
     * El estado que la columna manda al select debe ser el texto '1'/'0' que
     * coincide con sus opciones (o null para la celda sin capturar), nunca el
     * booleano crudo del modelo.
     */
    public function test_el_estado_de_la_columna_coincide_con_las_opciones_del_select(): void
    {
        $si = $this->caso('Con Sí', true);
        $no = $this->caso('Con No', false);
        $vacio = $this->caso('Sin capturar');

        $tabla = Livewire::test(ListCasoSeguimientos::class);

        $tabla->assertTableColumnStateSet('consentimiento', '1', $si)
            ->assertTableColumnStateSet('consentimiento', '0', $no)
            ->assertTableColumnStateSet('consentimiento', null, $vacio);
    }

    public function test_capturar_una_fila_no_toca_el_consentimiento_de_las_demas(): void
    {
        $editado = $this->caso('Editado');
        $conSi = $this->caso('Ya tenía Sí', true);
        $conNo = $this->caso('Ya tenía No', false);
        $vacio = $this->caso('Sin capturar');

        Livewire::test(ListCasoSeguimientos::class)
            ->call('updateTableColumnState', 'consentimiento', (string) $editado->getKey(), '1');

        $this->assertTrue($editado->fresh()->consentimiento);
        $this->assertTrue($conSi->fresh()->consentimiento);
        $this->assertFalse($conNo->fresh()->consentimiento);
        $this->assertNull($vacio->fresh()->consentimiento);
    }

    public function test_el_no_se_guarda_como_falso_y_limpiar_regresa_a_nulo(): void
    {
        $caso = $this->caso('Cambia de opinión', true);

        $tabla = Livewire::test(ListCasoSeguimientos::class);

        $tabla->call('updateTableColumnState', 'consentimiento', (string) $caso->getKey(), '0');
        $this->assertFalse($caso->fresh()->consentimiento);

        // Volver al placeholder ("Seleccione una opción") limpia el dato.
        $tabla->call('updateTableColumnState', 'consentimiento', (string) $caso->getKey(), '');
        $this->assertNull($caso->fresh()->consentimiento);
    }
}
