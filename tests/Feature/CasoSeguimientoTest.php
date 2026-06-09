<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;
use App\Models\CasoSeguimiento;
use App\Filament\Empresa\Resources\CasoSeguimientos\CasoSeguimientoResource;

class CasoSeguimientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_and_tenant_scoping_are_secure(): void
    {
        // Create Company A
        $empresaA = Empresa::create([
            'nombre_empresa' => 'Empresa A',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director A',
            'nombre_responsable' => 'Responsable A',
            'correo' => 'a@empresa.com',
            'telefono' => '1234',
            'rubro' => 'Manufactura',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        // Create Company B
        $empresaB = Empresa::create([
            'nombre_empresa' => 'Empresa B',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director B',
            'nombre_responsable' => 'Responsable B',
            'correo' => 'b@empresa.com',
            'telefono' => '5678',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 20,
            'password' => bcrypt('password'),
        ]);

        // Create case for Company A
        $caseA = CasoSeguimiento::create([
            'empresa_id' => $empresaA->id,
            'identificador_empleado' => 'EMP-01',
            'nivel_riesgo_detectado' => 'Urgente',
            'estatus_atencion' => 'En seguimiento',
        ]);

        // Create case for Company B
        $caseB = CasoSeguimiento::create([
            'empresa_id' => $empresaB->id,
            'identificador_empleado' => 'EMP-02',
            'nivel_riesgo_detectado' => 'Leve',
            'estatus_atencion' => 'Cerrado satisfactorio',
        ]);

        // Verify hasMany and belongsTo relationships
        $this->assertCount(1, $empresaA->casosSeguimiento);
        $this->assertEquals('EMP-01', $empresaA->casosSeguimiento->first()->identificador_empleado);
        $this->assertEquals($empresaA->id, $caseA->empresa->id);

        $this->assertCount(1, $empresaB->casosSeguimiento);
        $this->assertEquals('EMP-02', $empresaB->casosSeguimiento->first()->identificador_empleado);
        $this->assertEquals($empresaB->id, $caseB->empresa->id);

        // Authenticate as Company A and check resource query scoping
        $this->actingAs($empresaA, 'empresa');

        $queryResult = CasoSeguimientoResource::getEloquentQuery()->get();

        $this->assertCount(1, $queryResult);
        $this->assertEquals('EMP-01', $queryResult->first()->identificador_empleado);
        $this->assertNotContains('EMP-02', $queryResult->pluck('identificador_empleado')->toArray());
    }
}
