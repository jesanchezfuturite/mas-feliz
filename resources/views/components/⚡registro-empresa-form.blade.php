<?php

use Livewire\Component;
use App\Models\Empresa;
use App\Mail\AccesosTableroEmpresa;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

new class extends Component
{
    public string $nombre_empresa = '';
    public string $rfc = '';
    public string $ambito = '';
    public string $domicilio = '';
    public string $municipio = '';
    public string $dias_horario_servicio = '';
    public string $nombre_director = '';
    public string $nombre_responsable = '';
    public string $cargo_enlace = '';
    public string $correo = '';
    public string $telefono = '';
    public string $rubro = '';
    public ?int $numero_trabajadores = null;

    public bool $success = false;
    public string $createdFolio = '';

    protected array $rules = [
        'nombre_empresa' => 'required|string|max:255',
        'rfc' => 'nullable|string|max:15',
        'ambito' => 'required|string|in:Público,Privado,Productivo/Industrial,Social/Comunitario,Otro',
        'domicilio' => 'required|string|max:255',
        'municipio' => 'required|string|in:Abasolo,Acuña,Allende,Arteaga,Candela,Castaños,Cuatro Ciénegas,Escobedo,Francisco I. Madero,Frontera,General Cepeda,Guerrero,Hidalgo,Jiménez,Juárez,Lamadrid,Matamoros,Monclova,Morelos,Múzquiz,Nadadores,Nava,Ocampo,Parras,Piedras Negras,Progreso,Ramos Arizpe,Sabinas,Sacramento,Saltillo,San Buenaventura,San Juan de Sabinas,San Pedro,Sierra Mojada,Torreón,Viesca,Villa Unión,Zaragoza',
        'dias_horario_servicio' => 'required|string|max:255',
        'nombre_director' => 'required|string|max:255',
        'nombre_responsable' => 'required|string|max:255',
        'cargo_enlace' => 'required|string|max:255',
        'correo' => 'required|email|max:255|unique:empresas,correo',
        'telefono' => 'required|string|max:255',
        'rubro' => 'required|string|max:255',
        'numero_trabajadores' => 'required|integer|min:1',
    ];

    public function save(): void
    {
        $this->validate();

        // Generar contraseña temporal
        $passwordTemporal = Str::random(8);

        // Guardar en la base de datos
        $empresa = Empresa::create([
            'nombre_empresa' => $this->nombre_empresa,
            'rfc' => $this->rfc ?: null,
            'ambito' => $this->ambito,
            'domicilio' => $this->domicilio,
            'municipio' => $this->municipio,
            'dias_horario_servicio' => $this->dias_horario_servicio,
            'nombre_director' => $this->nombre_director,
            'nombre_responsable' => $this->nombre_responsable,
            'cargo_enlace' => $this->cargo_enlace,
            'correo' => $this->correo,
            'password' => Hash::make($passwordTemporal),
            'telefono' => $this->telefono,
            'rubro' => $this->rubro,
            'numero_trabajadores' => $this->numero_trabajadores,
        ]);

        // Enviar correo de accesos
        Mail::to($empresa->correo)->send(new AccesosTableroEmpresa($empresa, $passwordTemporal));

        // Establecer estado de éxito
        $this->createdFolio = $empresa->folio;
        $this->success = true;
    }
};
?>

<div class="w-full max-w-4xl mx-auto bg-white shadow-xl rounded-2xl border border-slate-100 overflow-hidden transition-all duration-300">
    @if ($success)
        <div class="p-8 sm:p-12 text-center flex flex-col items-center justify-center space-y-6">
            <div class="h-20 w-20 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 rounded-full flex items-center justify-center shadow-inner animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <div class="space-y-2">
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100">¡Registro Exitoso!</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Los datos de tu empresa se han almacenado correctamente.
                </p>
            </div>

            <div class="bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80 rounded-xl p-6 w-full max-w-md shadow-sm">
                <span class="block text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Folio Asignado</span>
                <span class="block text-3xl font-mono font-bold text-[#92c644] dark:text-[#92c644] tracking-wider">{{ $createdFolio }}</span>
            </div>

            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">
                Se ha enviado un correo electrónico con su contraseña temporal de acceso e instrucciones para ingresar al tablero de seguimiento.
            </p>
        </div>
    @else
        <div class="bg-gradient-to-r from-[#84b33d] to-[#749d36] px-8 py-6 text-white text-center">
            <h3 class="text-2xl font-bold">Formulario de Registro</h3>
        </div>

        <form wire:submit="save" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre de la Organización -->
                <div class="space-y-2">
                    <label for="nombre_empresa" class="block text-sm font-semibold text-slate-700">Nombre de la Organización</label>
                    <input type="text" id="nombre_empresa" wire:model="nombre_empresa" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Mi Organización S.A.">
                    @error('nombre_empresa') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- RFC (Opcional) -->
                <div class="space-y-2">
                    <label for="rfc" class="block text-sm font-semibold text-slate-700">RFC (Opcional)</label>
                    <input type="text" id="rfc" wire:model="rfc" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. ABC123456XYZ">
                    @error('rfc') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Municipio -->
                <div class="space-y-2">
                    <label for="municipio" class="block text-sm font-semibold text-slate-700">Municipio</label>
                    <select id="municipio" wire:model="municipio" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none bg-white">
                        <option value="">Selecciona un municipio</option>
                        @foreach(['Abasolo', 'Acuña', 'Allende', 'Arteaga', 'Candela', 'Castaños', 'Cuatro Ciénegas', 'Escobedo', 'Francisco I. Madero', 'Frontera', 'General Cepeda', 'Guerrero', 'Hidalgo', 'Jiménez', 'Juárez', 'Lamadrid', 'Matamoros', 'Monclova', 'Morelos', 'Múzquiz', 'Nadadores', 'Nava', 'Ocampo', 'Parras', 'Piedras Negras', 'Progreso', 'Ramos Arizpe', 'Sabinas', 'Sacramento', 'Saltillo', 'San Buenaventura', 'San Juan de Sabinas', 'San Pedro', 'Sierra Mojada', 'Torreón', 'Viesca', 'Villa Unión', 'Zaragoza'] as $muni)
                            <option value="{{ $muni }}">{{ $muni }}</option>
                        @endforeach
                    </select>
                    @error('municipio') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Ámbito -->
                <div class="space-y-2">
                    <label for="ambito" class="block text-sm font-semibold text-slate-700">Ámbito</label>
                    <select id="ambito" wire:model="ambito" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none bg-white">
                        <option value="">Selecciona un ámbito</option>
                        @foreach(['Público', 'Privado', 'Productivo/Industrial', 'Social/Comunitario', 'Otro'] as $amb)
                            <option value="{{ $amb }}">{{ $amb }}</option>
                        @endforeach
                    </select>
                    @error('ambito') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Domicilio -->
                <div class="space-y-2 md:col-span-2">
                    <label for="domicilio" class="block text-sm font-semibold text-slate-700">Domicilio</label>
                    <input type="text" id="domicilio" wire:model="domicilio" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Calle Victoria #123, Col. Centro, Saltillo, Coahuila">
                    @error('domicilio') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Director -->
                <div class="space-y-2">
                    <label for="nombre_director" class="block text-sm font-semibold text-slate-700">Nombre del Director</label>
                    <input type="text" id="nombre_director" wire:model="nombre_director" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Gerardo Sánchez">
                    @error('nombre_director') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Responsable (Persona enlace) -->
                <div class="space-y-2">
                    <label for="nombre_responsable" class="block text-sm font-semibold text-slate-700">Persona enlace (responsable del llenado de la información y seguimiento)</label>
                    <input type="text" id="nombre_responsable" wire:model="nombre_responsable" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Lic. Ana María Gómez">
                    @error('nombre_responsable') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Cargo de la persona enlace -->
                <div class="space-y-2">
                    <label for="cargo_enlace" class="block text-sm font-semibold text-slate-700">Cargo de la persona enlace</label>
                    <input type="text" id="cargo_enlace" wire:model="cargo_enlace" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Gerente de Recursos Humanos">
                    @error('cargo_enlace') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Correo Electrónico -->
                <div class="space-y-2">
                    <label for="correo" class="block text-sm font-semibold text-slate-700">Correo Electrónico</label>
                    <input type="email" id="correo" wire:model="correo" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. contacto@organizacion.com">
                    @error('correo') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="space-y-2">
                    <label for="telefono" class="block text-sm font-semibold text-slate-700">Teléfono</label>
                    <input type="text" id="telefono" wire:model="telefono" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. +52 81 1234 5678">
                    @error('telefono') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Rubro -->
                <div class="space-y-2">
                    <label for="rubro" class="block text-sm font-semibold text-slate-700">Rubro</label>
                    <input type="text" id="rubro" wire:model="rubro" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Tecnología, Manufactura">
                    @error('rubro') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Número de Trabajadores (Número de colaboradores) -->
                <div class="space-y-2">
                    <label for="numero_trabajadores" class="block text-sm font-semibold text-slate-700">Número de colaboradores</label>
                    <input type="number" id="numero_trabajadores" wire:model="numero_trabajadores" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. 120" min="1">
                    @error('numero_trabajadores') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Horario de Servicio (Horarios de la empresa) -->
                <div class="space-y-2 md:col-span-2">
                    <label for="dias_horario_servicio" class="block text-sm font-semibold text-slate-700">Horarios de la empresa</label>
                    <input type="text" id="dias_horario_servicio" wire:model="dias_horario_servicio" 
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-[#92c644] focus:border-[#92c644] transition duration-150 outline-none" 
                        placeholder="Ej. Lunes a Viernes de 9:00 AM a 6:00 PM">
                    @error('dias_horario_servicio') 
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p> 
                    @enderror
                </div>
            </div>

            <!-- Aviso de Privacidad / Consentimiento -->
            <div class="pt-4 border-t border-slate-100 flex flex-col space-y-4">
                <div class="flex items-start space-x-3 text-xs text-slate-500">
                    <input type="checkbox" id="accept_privacy" required checked class="mt-1 h-4 w-4 rounded border-slate-300 text-[#92c644] focus:ring-[#92c644]">
                    <label for="accept_privacy" class="leading-relaxed">
                        Acepto los términos del distintivo. La organización es responsable de la veracidad y protección de los datos proporcionados.
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" wire:loading.attr="disabled"
                    class="px-6 py-3 bg-gradient-to-r from-[#92c644] to-[#84b33d] hover:from-[#84b33d] hover:to-[#749d36] text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#92c644] focus:ring-offset-2 transition-all duration-150 disabled:opacity-50 flex items-center justify-center space-x-2">
                    <span wire:loading.remove>Enviar Registro</span>
                    <span wire:loading>Enviando...</span>
                    <!-- Spinner Icon when loading -->
                    <svg wire:loading class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    @endif
</div>