<?php

namespace Tests\Feature;

use App\Models\Autoevaluacion;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaDistintivoExpositionTest extends TestCase
{
    use RefreshDatabase;

    public function test_empresa_model_accessors(): void
    {
        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Accessors',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'test_accessors@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        // Initially no autoevaluacion, so ruta_pdf should be null, estatus should be null
        $this->assertNull($empresa->ruta_pdf);
        $this->assertNull($empresa->estatus);

        // Create autoevaluacion
        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [],
        ]);

        $this->assertNull($empresa->fresh()->ruta_pdf);

        // Mock a validated autoevaluacion with pdf path
        $respuestas = [
            'pdf_distintivo' => 'distintivos/empresa_1_folio_MF-2026-0001.pdf'
        ];
        $autoevaluacion->update([
            'estatus' => 'Validado',
            'respuestas' => $respuestas,
        ]);

        $empresa->update([
            'estatus_distintivo' => 'Validado',
        ]);

        $this->assertEquals('distintivos/empresa_1_folio_MF-2026-0001.pdf', $empresa->fresh()->ruta_pdf);
        $this->assertEquals('Dictaminado', $empresa->fresh()->estatus);
    }

    public function test_resource_infolist_contains_ruta_pdf_text_entry(): void
    {
        $empresa = \Mockery::mock(\App\Models\Empresa::class)->makePartial();
        $empresa->shouldReceive('getAttribute')->with('ruta_pdf')->andReturn('path/to/pdf');

        $livewire = \Mockery::mock(\Livewire\Component::class, \Filament\Schemas\Contracts\HasSchemas::class);
        $livewire->shouldReceive('getId')->andReturn('dummy-id');
        
        $schema = \Filament\Schemas\Schema::make($livewire)->record($empresa);
        $infolistSchema = \App\Filament\Resources\Empresas\EmpresaResource::infolist($schema);

        $components = $infolistSchema->getComponents();
        
        $found = false;
        foreach ($components as $component) {
            $heading = method_exists($component, 'getHeading') ? $component->getHeading() : null;
            if ($component instanceof \Filament\Schemas\Components\Section && $heading === 'Dictamen de Gobierno') {
                foreach ($component->getChildComponents() as $child) {
                    if ($child instanceof \Filament\Infolists\Components\TextEntry && $child->getName() === 'ruta_pdf') {
                        $found = true;
                        $this->assertEquals('Distintivo Otorgado', $child->getLabel());
                        break 2;
                    }
                }
            }
        }
        $this->assertTrue($found, "ruta_pdf TextEntry was not found in the infolist.");
    }

    public function test_empresas_table_contains_descargar_distintivo_action(): void
    {
        $livewire = \Mockery::mock(\Livewire\Component::class, \Filament\Tables\Contracts\HasTable::class);
        $livewire->shouldReceive('getId')->andReturn('dummy-id');
        
        $table = \Filament\Tables\Table::make($livewire);
        $configuredTable = \App\Filament\Resources\Empresas\Tables\EmpresasTable::configure($table);
        
        $actions = $configuredTable->getRecordActions();
        
        $found = false;
        foreach ($actions as $action) {
            if ($action instanceof \Filament\Actions\Action && $action->getName() === 'descargar_distintivo') {
                $found = true;
                $this->assertEquals('Ver Distintivo', $action->getLabel());
                $this->assertEquals('heroicon-o-document-arrow-down', $action->getIcon());
                $this->assertEquals('success', $action->getColor());
                break;
            }
        }
        $this->assertTrue($found, "descargar_distintivo Action was not found in the table recordActions.");
    }

    public function test_view_autoevaluacion_contains_descargar_distintivo_action(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User 8',
            'email' => 'admin8@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 9',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page9@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Validado',
            'respuestas' => [
                'pdf_distintivo' => 'distintivos/empresa_1_folio_MF-2026-0001.pdf'
            ],
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        \Livewire\Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertActionExists('descargar_distintivo')
            ->assertActionVisible('descargar_distintivo');
    }

    public function test_admin_can_view_validated_autoevaluacion_via_http(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User 9',
            'email' => 'admin9@test.com',
            'password' => bcrypt('password'),
            'estatus' => true,
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 10',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page10@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Validado',
            'respuestas' => [
                'pdf_distintivo' => 'distintivos/empresa_1_folio_MF-2026-0001.pdf'
            ],
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        $response = $this->get(\App\Filament\Resources\AutoevaluacionResource::getUrl('view', ['record' => $autoevaluacion->id]));

        $response->assertStatus(200);
    }
}
