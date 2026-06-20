<?php

namespace Tests\Feature;

use App\Mail\AccesosTableroEmpresa;
use App\Mail\AutoevaluacionDevueltaMail;
use App\Mail\DistintivoAprobadoMail;
use App\Mail\EvaluadorBienvenidaMail;
use App\Models\Autoevaluacion;
use App\Models\Empresa;
use App\Models\User;
use App\Notifications\ResetPasswordNotificationEs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_accesos_tablero_empresa_mailable_embeds_images(): void
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

        $mailable = new AccesosTableroEmpresa($empresa, 'temp-pass');
        $html = $mailable->render();

        $this->assertStringContainsString('data:image/', $html);
        $this->assertStringNotContainsString('src="http://localhost/images/', $html);
    }

    public function test_autoevaluacion_devuelta_mailable_embeds_images(): void
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

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Borrador',
            'respuestas' => [],
        ]);

        $mailable = new AutoevaluacionDevueltaMail($autoevaluacion);
        $html = $mailable->render();

        $this->assertStringContainsString('data:image/', $html);
        $this->assertStringNotContainsString('src="http://localhost/images/', $html);
    }

    public function test_distintivo_aprobado_mailable_embeds_images(): void
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

        $autoevaluacion = Autoevaluacion::create([
            'empresa_id' => $empresa->id,
            'estatus' => 'Validado',
            'respuestas' => [],
        ]);

        $mailable = new DistintivoAprobadoMail($autoevaluacion, 'Nivel Oro', 'Buen trabajo', 'path/to/pdf.pdf');
        $html = $mailable->render();

        $this->assertStringContainsString('data:image/', $html);
        $this->assertStringNotContainsString('src="http://localhost/images/', $html);
    }

    public function test_evaluador_bienvenida_mailable_embeds_images(): void
    {
        $evaluador = new \stdClass();
        $evaluador->nombres = 'Evaluador';
        $evaluador->apellidos = 'Test';
        $evaluador->correo = 'eval@test.com';

        $mailable = new EvaluadorBienvenidaMail($evaluador, 'temp-pass');
        $html = $mailable->render();

        $this->assertStringContainsString('data:image/', $html);
        $this->assertStringNotContainsString('src="http://localhost/images/', $html);
    }

    public function test_recuperar_password_notification_embeds_images(): void
    {
        $user = new User();
        $user->name = 'Responsable Test';
        $user->email = 'test@test.com';

        $notification = new ResetPasswordNotificationEs('some-token');
        $notification->url = 'http://localhost/reset-password?token=some-token';
        $mailMessage = $notification->toMail($user);
        $html = $mailMessage->render();

        $this->assertStringContainsString('data:image/', $html);
        $this->assertStringNotContainsString('src="http://localhost/images/', $html);
    }
}
