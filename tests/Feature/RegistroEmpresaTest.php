<?php

namespace Tests\Feature;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RegistroEmpresaTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_validates_required_fields(): void
    {
        Livewire::test('registro-empresa-form')
            ->call('save')
            ->assertHasErrors([
                'nombre_empresa' => 'required',
                'municipio' => 'required',
                'ambito' => 'required',
                'domicilio' => 'required',
                'dias_horario_servicio' => 'required',
                'nombre_director' => 'required',
                'nombre_responsable' => 'required',
                'cargo_enlace' => 'required',
                'correo' => 'required',
                'telefono' => 'required',
                'rubro' => 'required',
                'numero_trabajadores' => 'required',
            ]);
    }

    public function test_registration_form_validates_strict_municipio_and_ambito(): void
    {
        Livewire::test('registro-empresa-form')
            ->set('nombre_empresa', 'Empresa Test')
            ->set('municipio', 'Municipio Inventado') // Invalid option
            ->set('ambito', 'Ámbito Inventado') // Invalid option
            ->set('domicilio', 'Calle Falsa 123')
            ->set('dias_horario_servicio', 'Lunes a Viernes 9-6')
            ->set('nombre_director', 'Director Test')
            ->set('nombre_responsable', 'Responsable Test')
            ->set('cargo_enlace', 'Gerente')
            ->set('correo', 'test@empresa.com')
            ->set('telefono', '1234567890')
            ->set('rubro', 'Tecnología')
            ->set('numero_trabajadores', 50)
            ->call('save')
            ->assertHasErrors(['municipio', 'ambito'])
            ->assertHasNoErrors(['nombre_empresa', 'domicilio', 'cargo_enlace']);
    }

    public function test_registration_form_saves_successfully_with_new_fields(): void
    {
        Mail::fake();

        Livewire::test('registro-empresa-form')
            ->set('nombre_empresa', 'Empresa Exito S.A.')
            ->set('rfc', 'XAXX010101000') // Optional but valid
            ->set('ambito', 'Privado') // Valid select option
            ->set('domicilio', 'Av. Carranza #456')
            ->set('municipio', 'Saltillo') // Valid select option
            ->set('dias_horario_servicio', 'Lunes-Viernes 8-5')
            ->set('nombre_director', 'Director General')
            ->set('nombre_responsable', 'Juan Perez')
            ->set('cargo_enlace', 'Coordinador de Salud')
            ->set('correo', 'contacto@exito.com')
            ->set('telefono', '8441234567')
            ->set('rubro', 'Manufactura')
            ->set('numero_trabajadores', 150)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('success', true);

        $this->assertDatabaseHas('empresas', [
            'nombre_empresa' => 'Empresa Exito S.A.',
            'rfc' => 'XAXX010101000',
            'ambito' => 'Privado',
            'domicilio' => 'Av. Carranza #456',
            'municipio' => 'Saltillo',
            'dias_horario_servicio' => 'Lunes-Viernes 8-5',
            'nombre_director' => 'Director General',
            'nombre_responsable' => 'Juan Perez',
            'cargo_enlace' => 'Coordinador de Salud',
            'correo' => 'contacto@exito.com',
            'telefono' => '8441234567',
            'rubro' => 'Manufactura',
            'numero_trabajadores' => 150,
        ]);
    }
}
