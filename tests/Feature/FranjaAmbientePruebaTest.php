<?php

namespace Tests\Feature;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La franja "AMBIENTE DE PRUEBA" existe porque Angélica pasó días revisando el
 * servidor de prueba creyendo que era producción (25/08/2026). Se enciende con
 * AMBIENTE_PRUEBA=true —solo en el .env de ese servidor— y debe verse en todas
 * las superficies: paneles de Filament, landing y cuestionario público.
 */
class FranjaAmbientePruebaTest extends TestCase
{
    use RefreshDatabase;

    private const TEXTO = 'AMBIENTE DE PRUEBA';

    private function empresa(): Empresa
    {
        return Empresa::create([
            'nombre_empresa' => 'Empresa Franja',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes a viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'franja@empresa.test',
            'password' => bcrypt('secret'),
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
        ]);
    }

    public function test_apagada_no_se_dibuja_en_ninguna_superficie(): void
    {
        config(['app.ambiente_prueba' => false]);

        $this->get('/')->assertSuccessful()->assertDontSee(self::TEXTO);
        $this->get('/admin/login')->assertSuccessful()->assertDontSee(self::TEXTO);
    }

    public function test_encendida_se_dibuja_en_la_landing(): void
    {
        config(['app.ambiente_prueba' => true]);

        $this->get('/')->assertSuccessful()->assertSee(self::TEXTO);
    }

    public function test_encendida_se_dibuja_en_los_paneles(): void
    {
        config(['app.ambiente_prueba' => true]);

        // El hook BODY_START se dibuja desde la pantalla de login, sin sesión.
        $this->get('/admin/login')->assertSuccessful()->assertSee(self::TEXTO);
        $this->get('/tablero/login')->assertSuccessful()->assertSee(self::TEXTO);
        $this->get('/evaluador/login')->assertSuccessful()->assertSee(self::TEXTO);
        $this->get('/gestor/login')->assertSuccessful()->assertSee(self::TEXTO);
    }

    public function test_encendida_se_dibuja_en_el_cuestionario_publico(): void
    {
        config(['app.ambiente_prueba' => true]);

        $this->get('/diagnostico/'.$this->empresa()->token_tamizaje)
            ->assertSuccessful()
            ->assertSee(self::TEXTO);
    }
}
