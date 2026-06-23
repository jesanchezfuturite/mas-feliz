<?php

namespace Tests\Feature;

use App\Models\Autoevaluacion;
use App\Models\Empresa;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownloadAcusePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_download_acuse_pdf(): void
    {
        // Enable tools
        Setting::updateOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => true]
        );

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test PDF',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'pdf@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [
                'criterio_1' => [
                    'elemento_1' => ['score' => '10'],
                ]
            ],
        ]);

        $this->actingAs($empresa, 'empresa');

        // Render the PDF view directly to assert content
        $view = view('pdf.acuse-autoevaluacion', [
            'autoevaluacion' => $autoevaluacion,
        ])->render();

        $this->assertStringContainsString('REPORTE PRELIMINAR DE AUTOEVALUACIÓN', $view);
        $this->assertStringContainsString('Logo Coahuila', $view);
        $this->assertStringContainsString('Logo +Feliz', $view);
        $this->assertStringContainsString('Logo Inspira', $view);
        $this->assertStringContainsString('Aviso importante', $view);
        $this->assertStringContainsString('Eje 1. Fortalecimiento', $view);
    }
}
