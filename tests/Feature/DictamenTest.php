<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Empresa;

class DictamenTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_dictamen_fields_can_be_updated(): void
    {
        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Dictamen',
            'municipio' => 'Toluca',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'dictamen@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 30,
            'password' => bcrypt('password'),
        ]);

        $empresa->refresh();

        // Default value should be 'En revisión'
        $this->assertEquals('En revisión', $empresa->estatus_distintivo);
        $this->assertNull($empresa->nivel_madurez_asignado);
        $this->assertNull($empresa->retroalimentacion_gobierno);
        $this->assertNull($empresa->fecha_dictamen);

        // Update to Approved
        $empresa->update([
            'estatus_distintivo' => 'Aprobado',
            'nivel_madurez_asignado' => 'Avanzado',
            'retroalimentacion_gobierno' => 'Excelente implementación de políticas.',
            'fecha_dictamen' => now(),
        ]);

        $this->assertEquals('Aprobado', $empresa->estatus_distintivo);
        $this->assertEquals('Avanzado', $empresa->nivel_madurez_asignado);
        $this->assertEquals('Excelente implementación de políticas.', $empresa->retroalimentacion_gobierno);
        $this->assertNotNull($empresa->fecha_dictamen);

        // Update to Rejected
        $empresa->update([
            'estatus_distintivo' => 'Rechazado',
            'nivel_madurez_asignado' => null,
            'retroalimentacion_gobierno' => 'Se requiere mejorar la tasa de participación del personal en los tamizajes.',
            'fecha_dictamen' => now(),
        ]);

        $this->assertEquals('Rechazado', $empresa->estatus_distintivo);
        $this->assertNull($empresa->nivel_madurez_asignado);
        $this->assertEquals('Se requiere mejorar la tasa de participación del personal en los tamizajes.', $empresa->retroalimentacion_gobierno);
    }

    public function test_resource_infolist_uses_text_entry_components(): void
    {
        $livewire = \Mockery::mock(\Livewire\Component::class, \Filament\Schemas\Contracts\HasSchemas::class);
        $livewire->shouldReceive('getId')->andReturn('dummy-id');
        
        $schema = \Filament\Schemas\Schema::make($livewire);
        $infolistSchema = \App\Filament\Resources\Empresas\EmpresaResource::infolist($schema);

        $components = $infolistSchema->getComponents();
        
        $this->assertNotEmpty($components);
        
        $this->checkComponentsDoNotContainForms($components);
    }

    private function checkComponentsDoNotContainForms(array $components): void
    {
        foreach ($components as $component) {
            $class = get_class($component);
            $this->assertStringStartsNotWith('Filament\Forms\Components\\', $class, "Component should not be a Form component: {$class}");
            
            if (method_exists($component, 'getChildComponents')) {
                $children = $component->getChildComponents();
                $this->checkComponentsDoNotContainForms($children);
            }
        }
    }

    public function test_resource_defines_relation_managers(): void
    {
        $relations = \App\Filament\Resources\Empresas\EmpresaResource::getRelations();
        
        $this->assertContains(
            \App\Filament\Resources\Empresas\RelationManagers\TamizajesRelationManager::class,
            $relations
        );
        $this->assertContains(
            \App\Filament\Resources\Empresas\RelationManagers\CasosSeguimientoRelationManager::class,
            $relations
        );
    }
}
