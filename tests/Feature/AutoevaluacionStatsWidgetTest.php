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
            'estatus' => 'En revisión',
            'respuestas' => [],
        ]);

        $this->actingAs($user, 'web');

        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertSuccessful();
    }

    public function test_evaluar_criterio_action_validation(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User 2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 3',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page3@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'En revisión',
            'respuestas' => [],
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // 1. Evaluating with unqualified policies should automatically calculate status as 'Rechazado'
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callFormComponentAction('evaluar_criterio_actions_1', 'evaluar_criterio_1', [
                'retroalimentacion_general' => 'Faltan políticas por calificar.',
            ])
            ->assertHasNoFormComponentActionErrors();

        $autoevaluacion->refresh();
        $this->assertEquals('Rechazado', $autoevaluacion->respuestas['criterio_1']['status']);
        $this->assertEquals('Faltan políticas por calificar.', $autoevaluacion->respuestas['criterio_1']['feedback']);
        $this->assertEquals('admin2@test.com', $autoevaluacion->respuestas['criterio_1']['evaluador_email']);

        // 2. Filling answers for all 7 elements of Criterion 1 to Aprobado
        $respuestas = $autoevaluacion->respuestas ?? [];
        for ($e = 1; $e <= 7; $e++) {
            $respuestas['criterio_1']["elemento_{$e}"] = [
                'score' => '10',
                'calificacion_politica' => 'Aprobado'
            ];
        }
        $autoevaluacion->update(['respuestas' => $respuestas]);

        // 3. Now evaluating Criterion 1 should automatically calculate status as 'Aprobado'
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callFormComponentAction('evaluar_criterio_actions_1', 'evaluar_criterio_1', [
                'retroalimentacion_general' => 'Todo excelente',
            ])
            ->assertHasNoFormComponentActionErrors();

        $autoevaluacion->refresh();
        $this->assertEquals('Aprobado', $autoevaluacion->respuestas['criterio_1']['status']);
        $this->assertEquals('Todo excelente', $autoevaluacion->respuestas['criterio_1']['feedback']);

        // 4. Since status is Aprobado, the action should be disabled (Gold Rule)
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertFormComponentActionDisabled('evaluar_criterio_actions_1', 'evaluar_criterio_1');
    }

    public function test_admin_cannot_view_draft_autoevaluacion(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User 3',
            'email' => 'admin3@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 4',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page4@test.com',
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

        $this->get(\App\Filament\Resources\AutoevaluacionResource::getUrl('view', ['record' => $autoevaluacion->getKey()]))
            ->assertStatus(403);
    }

    public function test_validar_autoevaluacion_button_visibility(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User 4',
            'email' => 'admin4@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 5',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page5@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        // Autoevaluacion initially is 'En revisión', but answers are empty
        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'En revisión',
            'respuestas' => [],
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // 1. Initially, since indispensables are not validated, the button should be hidden
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertActionHidden('validar');

        // 2. Validate some but not all indispensables (only criteria 1, 4, 9)
        $respuestas = [];
        foreach ([1, 4, 9] as $id) {
            $respuestas["criterio_{$id}"] = ['status' => 'Aprobado'];
        }
        $autoevaluacion->update(['respuestas' => $respuestas]);

        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertActionHidden('validar');

        // 3. Validate all indispensables (1, 4, 9, 15, 16)
        foreach ([15, 16] as $id) {
            $respuestas["criterio_{$id}"] = ['status' => 'Aprobado'];
        }
        $autoevaluacion->update(['respuestas' => $respuestas]);

        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->assertActionVisible('validar');
    }

    public function test_evaluar_politica_action_validation(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin Test User 5',
            'email' => 'admin5@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 6',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page6@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'En revisión',
            'respuestas' => [],
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // 1. Trying to reject a policy without feedback should fail validation
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callFormComponentAction('detalles_actions_1_1', 'detalles_1_1', [
                'calificacion_politica' => 'Rechazado',
                'feedback' => '',
            ])
            ->assertHasFormComponentActionErrors(['feedback']);

        // 2. Rejecting with feedback should succeed
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callFormComponentAction('detalles_actions_1_1', 'detalles_1_1', [
                'calificacion_politica' => 'Rechazado',
                'feedback' => 'Falta evidencia válida.',
            ])
            ->assertHasNoFormComponentActionErrors();

        // Check it was saved in the record
        $autoevaluacion->refresh();
        $this->assertEquals('Rechazado', $autoevaluacion->respuestas['criterio_1']['elemento_1']['calificacion_politica']);
        $this->assertEquals('Falta evidencia válida.', $autoevaluacion->respuestas['criterio_1']['elemento_1']['feedback']);
        $this->assertEquals('admin5@test.com', $autoevaluacion->respuestas['criterio_1']['elemento_1']['evaluador_email']);

        // 3. Approving without feedback should succeed
        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callFormComponentAction('detalles_actions_1_1', 'detalles_1_1', [
                'calificacion_politica' => 'Aprobado',
                'feedback' => '',
            ])
            ->assertHasNoFormComponentActionErrors();

        $autoevaluacion->refresh();
        $this->assertEquals('Aprobado', $autoevaluacion->respuestas['criterio_1']['elemento_1']['calificacion_politica']);
    }

    public function test_devolver_autoevaluacion_action(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = \App\Models\User::create([
            'name' => 'Admin Test User 6',
            'email' => 'admin6@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 7',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page7@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'En revisión',
            'respuestas' => [],
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callAction('devolver');

        $autoevaluacion->refresh();
        $this->assertEquals('Borrador', $autoevaluacion->estatus);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AutoevaluacionDevueltaMail::class, function ($mail) use ($empresa) {
            return $mail->hasTo($empresa->correo);
        });
    }

    public function test_validar_autoevaluacion_action(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = \App\Models\User::create([
            'name' => 'Admin Test User 7',
            'email' => 'admin7@test.com',
            'password' => bcrypt('password'),
        ]);

        $empresa = Empresa::create([
            'nombre_empresa' => 'Empresa Test Page 8',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes',
            'nombre_director' => 'Director Test',
            'nombre_responsable' => 'Responsable Test',
            'correo' => 'page8@test.com',
            'telefono' => '1234567890',
            'rubro' => 'Servicios',
            'numero_trabajadores' => 10,
            'password' => bcrypt('password'),
        ]);

        // Prepopulate responses with Aprobado status for indispensables so the button is visible
        $respuestas = [];
        $criterioElementsCount = [1 => 7, 4 => 7, 9 => 7, 15 => 6, 16 => 5];
        foreach ([1, 4, 9, 15, 16] as $id) {
            $respuestas["criterio_{$id}"] = ['status' => 'Aprobado'];
            for ($e = 1; $e <= $criterioElementsCount[$id]; $e++) {
                $respuestas["criterio_{$id}"]["elemento_{$e}"] = ['score' => '10'];
            }
        }

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'En revisión',
            'respuestas' => $respuestas,
        ]);

        $this->actingAs($user, 'web');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        Livewire::test(\App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::class, [
            'record' => $autoevaluacion->getKey(),
        ])
            ->callAction('validar', [
                'dictamen_final' => 'Excelente autoevaluación, cumple con todos los criterios de madurez.',
            ]);

        $autoevaluacion->refresh();
        $empresa->refresh();

        $this->assertEquals('Validado', $autoevaluacion->estatus);
        $this->assertEquals('Excelente autoevaluación, cumple con todos los criterios de madurez.', $autoevaluacion->respuestas['dictamen_final']);
        
        $expectedPdfPath = "distintivos/empresa_{$empresa->id}_folio_{$empresa->folio}.pdf";
        $this->assertEquals($expectedPdfPath, $autoevaluacion->respuestas['pdf_distintivo']);

        $this->assertEquals('Validado', $empresa->estatus_distintivo);
        $this->assertEquals('Excelencia', $empresa->nivel_madurez_asignado);
        $this->assertEquals('Excelente autoevaluación, cumple con todos los criterios de madurez.', $empresa->retroalimentacion_gobierno);

        // Verify PDF was generated and stored
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($expectedPdfPath);

        // Verify Mail was sent
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\DistintivoAprobadoMail::class, function ($mail) use ($empresa, $expectedPdfPath) {
            return $mail->hasTo($empresa->correo) && 
                   $mail->nivelMadurez === 'Excelencia' &&
                   $mail->pdfPath === $expectedPdfPath;
        });
    }
}
