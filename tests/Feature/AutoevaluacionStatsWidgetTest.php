<?php

namespace Tests\Feature;

use App\Models\Autoevaluacion;
use App\Models\Empresa;
use App\Filament\Empresa\Resources\Autoevaluacions\Widgets\AutoevaluacionStatsWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AutoevaluacionStatsWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_calculates_initial_scores_from_record(): void
    {
        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'test@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        // Create responses where some elements have score
        // Let's set criterion 1 elements to score 10 (7 elements) => 70 pts
        // Criterion 4 elements to score 10 (7 elements) => 70 pts
        // Criterion 9 elements to score 10 (7 elements) => 70 pts
        // Total points = 210
        // Wait, indispensables count: 1, 4, 9 are met. 15 and 16 are not met.
        // So indispensablesCount = 3. cumplesIndispensables = false.
        // Madurez = Inicial.
        $respuestas = [];
        for ($i = 1; $i <= 7; $i++) {
            $respuestas['criterio_1']["elemento_{$i}"] = ['score' => '10'];
            $respuestas['criterio_4']["elemento_{$i}"] = ['score' => '10'];
            $respuestas['criterio_9']["elemento_{$i}"] = ['score' => '10'];
        }

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => $respuestas,
        ]);

        Livewire::test(AutoevaluacionStatsWidget::class, ['record' => $autoevaluacion])
            ->assertSet('record', $autoevaluacion)
            ->assertSet('respuestasState', $respuestas)
            ->assertSee('210 pts')
            ->assertSee('3 de 5')
            ->assertSee('Inicial');
    }

    public function test_widget_updates_reactively_on_form_updated_event(): void
    {
        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test 2',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'test2@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [],
        ]);

        // Start with empty answers (0 pts, 0 de 5, Inicial)
        $component = Livewire::test(AutoevaluacionStatsWidget::class, ['record' => $autoevaluacion])
            ->assertSee('0 pts')
            ->assertSee('0 de 5')
            ->assertSee('Inicial');

        // Now dispatch formUpdated with answers that meet all requirements
        // We need 1, 4, 9, 15, 16 met:
        // Criterio 1: 7 elements
        // Criterio 4: 7 elements
        // Criterio 9: 7 elements
        // Criterio 15: 6 elements
        // Criterio 16: 5 elements
        // Total elements = 32 elements. If all are 10, sum = 320.
        // Meets all indispensables, sum >= 180 => Excelencia.
        $newRespuestas = [];
        foreach ([1 => 7, 4 => 7, 9 => 7, 15 => 6, 16 => 5] as $criterioId => $elementsCount) {
            for ($e = 1; $e <= $elementsCount; $e++) {
                $newRespuestas["criterio_{$criterioId}"]["elemento_{$e}"] = ['score' => '10'];
            }
        }

        $component->dispatch('formUpdated', respuestas: $newRespuestas)
            ->assertSet('respuestasState', $newRespuestas)
            ->assertSee('320 pts')
            ->assertSee('5 de 5')
            ->assertSee('Excelencia');
    }

    public function test_edit_page_renders_successfully(): void
    {
        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [],
        ]);

        $this->actingAs($empresa, 'empresa');

        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('empresa'));

        Livewire::test(\App\Filament\Empresa\Resources\Autoevaluacions\Pages\EditAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertSuccessful();
    }

    public function test_view_page_renders_successfully(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 2',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page2@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [],
        ]);

        $this->actingAs($user, 'web');

        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertSuccessful();
    }
}
