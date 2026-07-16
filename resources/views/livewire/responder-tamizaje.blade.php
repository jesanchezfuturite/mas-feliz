<div class="max-w-4xl mx-auto px-4 sm:px-6" x-data="{ step: $wire.entangle('step'), consentimiento: $wire.entangle('consentimiento_otorgado'), dec1: $wire.entangle('declaracion_1'), dec2: $wire.entangle('declaracion_2'), dec3: $wire.entangle('declaracion_3'), dec4: $wire.entangle('declaracion_4'), dec5: $wire.entangle('declaracion_5') }">
    
    @if ($success)
        <!-- Success State -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-xl rounded-3xl p-8 sm:p-12 text-center space-y-6">
            <div class="h-20 w-20 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 rounded-full flex items-center justify-center mx-auto shadow-inner animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <div class="space-y-4">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight max-w-xl mx-auto leading-snug">
                    ¡Gracias por tu participación! Se han registrado correctamente tus respuestas.
                </h2>
                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                    De acuerdo a los resultados que aquí se obtengan y nuestro interés por tu salud emocional, podrás ser contactado a través de correo, mensaje y/o llamada para recibir alternativas de atención.
                </p>
            </div>

            <div class="h-px bg-slate-100 dark:bg-slate-800 my-4"></div>

            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs mx-auto">
                Este cuestionario nos ayuda a evaluar y mejorar el clima de bienestar y salud emocional dentro de <strong class="font-semibold text-slate-600 dark:text-slate-300">{{ $empresa->nombre_empresa }}</strong>.
            </p>
        </div>
    @else
        <!-- Header Info Card -->
        <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 rounded-3xl p-8 sm:p-10 mb-10 text-center space-y-4">
            <div class="inline-block"><span class="text-[11px] font-bold text-[#7aab36] dark:text-[#92c644] uppercase tracking-widest bg-[#92c644]/10 dark:bg-[#92c644]/20 px-4 py-1.5 rounded-full">Cuestionario Socioemocional</span></div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight font-heading">
                {{ $empresa->nombre_empresa }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                Por favor, responde con honestidad. Este tamizaje recopila información demográfica básica y evalúa aspectos de salud emocional de manera estrictamente confidencial.
            </p>

            <!-- Mensaje inicial -->
            <div class="text-left max-w-2xl mx-auto space-y-3 pt-5 mt-5 border-t border-slate-100 dark:border-slate-800 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                <p>El objetivo del presente tamizaje es implementar un programa de detección y atención temprana de la salud mental de nuestros colaboradores.</p>
                <p>Te invitamos a participar contestando el cuestionario. En caso de que encontremos riesgo de acuerdo a algunas de tus respuestas, te podremos compartir alternativas de atención.</p>
                <p>Si requieres información adicional o tienes duda, puedes dirigirte con el área de Recursos Humanos y/o enlace de +Feliz en tu organización.</p>
            </div>

        <!-- Paso 1: Consentimiento -->
        <div x-show="step === 'consentimiento'" class="space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-xl rounded-3xl p-8 sm:p-10 space-y-6">
                
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 font-heading tracking-tight leading-snug">
                    CONSENTIMIENTO INFORMADO Y AVISO DE CONFIDENCIALIDAD
                </h2>

                <div class="space-y-4 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    <p class="font-bold text-slate-800 dark:text-slate-200">
                        Gracias por participar en el Programa +Feliz.
                    </p>
                    <p>
                        La información que proporciones en este cuestionario será tratada de manera confidencial y utilizada únicamente para identificar necesidades de apoyo y salud emocional, así como para ofrecer orientación o seguimiento cuando sea necesario.
                    </p>
                    <p>
                        Tus respuestas no serán compartidas con otras personas ni utilizadas para fines distintos a los del programa. Los resultados se analizarán de manera protegida y únicamente el personal autorizado podrá tener acceso a ellos.
                    </p>
                    <p>
                        Tu participación es completamente voluntaria. Puedes decidir no responder alguna pregunta o suspender tu participación en cualquier momento, sin que ello genere consecuencias laborales o de cualquier otra índole.
                    </p>
                    <p>
                        Algunas preguntas podrían abordar temas personales o emocionales. Si alguna te genera incomodidad, puedes omitirla. Si durante la evaluación se identifica una situación que pudiera representar un riesgo importante para tu seguridad o bienestar, o si manifiestas necesidad de apoyo, el equipo del Programa +Feliz podrá contactarte para orientarte sobre los servicios de atención disponibles.
                    </p>
                    <p>
                        Los datos personales que, en su caso, proporciones serán protegidos conforme a la legislación aplicable en materia de protección de datos personales y serán utilizados exclusivamente para los fines del programa. Asimismo, podrás solicitar la corrección, actualización o eliminación de tus datos, así como revocar tu consentimiento para su uso.
                    </p>
                    <p>
                        Si tienes dudas o requieres apoyo emocional, puedes acercarte al enlace designado por tu organización o comunicarte a la Línea de la Vida al <strong>800 953 6453</strong>, disponible las 24 horas del día.
                    </p>
                </div>
                
                <div class="h-px bg-slate-100 dark:bg-slate-800 my-6"></div>

                <div class="space-y-4">
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                        ¿Deseas participar en esta evaluación de salud mental? <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <label class="flex items-center p-4 border rounded-2xl cursor-pointer transition-all duration-200"
                               :class="consentimiento === 'si' ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400'">
                            <input type="radio" wire:model.live="consentimiento_otorgado" value="si" class="sr-only" x-model="consentimiento" />
                            <span class="text-sm font-medium">Sí, otorgo mi consentimiento para participar.</span>
                        </label>

                        <label class="flex items-center p-4 border rounded-2xl cursor-pointer transition-all duration-200"
                               :class="consentimiento === 'no' ? 'border-amber-600 bg-amber-50/50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 ring-2 ring-amber-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400'">
                            <input type="radio" wire:model.live="consentimiento_otorgado" value="no" class="sr-only" x-model="consentimiento" />
                            <span class="text-sm font-medium">No, deseo no participar.</span>
                        </label>
                    </div>
                    @error('consentimiento_otorgado') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Declaraciones Checklist -->
                <div x-show="consentimiento === 'si'" x-transition class="space-y-4 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                        DECLARACIÓN DE CONSENTIMIENTO
                    </h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-start p-4 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all duration-150"
                               :class="dec1 ? 'border-blue-600 bg-blue-50/30 dark:bg-blue-950/10' : ''">
                            <input type="checkbox" x-model="dec1" class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" />
                            <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">He leído y comprendido la información anterior.</span>
                        </label>
                        @error('declaracion_1') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror

                        <label class="flex items-start p-4 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all duration-150"
                               :class="dec2 ? 'border-blue-600 bg-blue-50/30 dark:bg-blue-950/10' : ''">
                            <input type="checkbox" x-model="dec2" class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" />
                            <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Entiendo que mi participación es voluntaria.</span>
                        </label>
                        @error('declaracion_2') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror

                        <label class="flex items-start p-4 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all duration-150"
                               :class="dec3 ? 'border-blue-600 bg-blue-50/30 dark:bg-blue-950/10' : ''">
                            <input type="checkbox" x-model="dec3" class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" />
                            <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Entiendo que mis respuestas serán tratadas de manera confidencial.</span>
                        </label>
                        @error('declaracion_3') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror

                        <label class="flex items-start p-4 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all duration-150"
                               :class="dec4 ? 'border-blue-600 bg-blue-50/30 dark:bg-blue-950/10' : ''">
                            <input type="checkbox" x-model="dec4" class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" />
                            <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Autorizo el uso de la información proporcionada para los fines del Programa +Feliz.</span>
                        </label>
                        @error('declaracion_4') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror

                        <label class="flex items-start p-4 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all duration-150"
                               :class="dec5 ? 'border-blue-600 bg-blue-50/30 dark:bg-blue-950/10' : ''">
                            <input type="checkbox" x-model="dec5" class="mt-1 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" />
                            <span class="ml-3 text-sm text-slate-600 dark:text-slate-300">Acepto que el equipo del programa pueda contactarme en caso de identificarse la necesidad de orientación o seguimiento relacionado con mi salud mental.</span>
                        </label>
                        @error('declaracion_5') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div x-show="consentimiento === 'no'" class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-3xl p-6 text-center space-y-3">
                <div class="text-amber-500 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-amber-900 dark:text-amber-300">Agradecemos tu honestidad</h3>
                <p class="text-sm text-amber-700 dark:text-amber-400 max-w-md mx-auto leading-relaxed">
                    Has decidido no participar. No se recopilarán tus datos personales ni respuestas. Presiona <strong>Enviar</strong> para registrar tu decisión y finalizar.
                </p>

                <div class="pt-2">
                    <button type="button"
                            wire:click="enviarNoParticipacion"
                            wire:loading.attr="disabled"
                            wire:target="enviarNoParticipacion"
                            style="background: linear-gradient(to right, #f59e0b, #d97706); color: #ffffff;"
                            class="w-full sm:w-auto sm:mx-auto py-3 px-8 text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-150 disabled:opacity-50 inline-flex items-center justify-center space-x-2 text-base">
                        <span wire:loading.remove wire:target="enviarNoParticipacion">Enviar</span>
                        <span wire:loading wire:target="enviarNoParticipacion">Enviando...</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" wire:loading.remove wire:target="enviarNoParticipacion">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="consentimiento !== 'no'" class="pt-4">
                <button type="button" 
                        wire:click="irADemograficos"
                        :disabled="consentimiento !== 'si' || !dec1 || !dec2 || !dec3 || !dec4 || !dec5"
                        :class="(consentimiento === 'si' && dec1 && dec2 && dec3 && dec4 && dec5) ? 'opacity-100 cursor-pointer' : 'opacity-50 cursor-not-allowed pointer-events-none'"
                        style="background: linear-gradient(to right, #92c644, #84b33d); color: #ffffff;"
                        class="w-full py-4 px-6 bg-gradient-to-r from-[#92c644] to-[#84b33d] hover:from-[#84b33d] hover:to-[#749d36] text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#92c644] focus:ring-offset-2 transition-all duration-150 flex items-center justify-center space-x-2 text-lg">
                    <span>Continuar a Datos Generales</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Paso 2: Datos Demográficos -->
        <div x-show="step === 'demograficos'" class="space-y-6">
            <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden">
                <div class="bg-blue-600 px-8 py-5 text-white">
                    <h2 class="text-lg font-bold">Datos Generales</h2>
                    <p class="text-white/80 text-xs mt-0.5">Información sociodemográfica básica del participante.</p>
                </div>
                <div class="p-8 space-y-8">
                    <!-- Nombre Completo -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            1. Nombre completo (Nombre/s Apellido Apellido) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombre_completo" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" placeholder="Escribe tu nombre completo">
                        @error('nombre_completo') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Sexo -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            2. Sexo <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach(['Hombre', 'Mujer'] as $val)
                                <label class="flex items-center justify-center py-3 px-4 border rounded-full cursor-pointer transition-all duration-200 {{ $genero === $val ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model.live="genero" value="{{ $val }}" class="sr-only" />
                                    <span class="text-sm font-medium">{{ $val }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('genero') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Edad -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            3. ¿En qué grupo de edad se encuentra? <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="edad" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none">
                            <option value="">Selecciona una opción</option>
                            <option value="Menor de 18 años">Menor de 18 años</option>
                            <option value="18 a 24 años">18 a 24 años</option>
                            <option value="25 a 34 años">25 a 34 años</option>
                            <option value="35 a 44 años">35 a 44 años</option>
                            <option value="45 a 54 años">45 a 54 años</option>
                            <option value="55 años o más">55 años o más</option>
                        </select>
                        @error('edad') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actividad -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            4. ¿Cuál describe mejor las actividades que realiza actualmente en su trabajo? <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="actividad_trabajo" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none">
                            <option value="">Selecciona una opción</option>
                            <option value="Operativas">Operativas</option>
                            <option value="Administrativas">Administrativas</option>
                            <option value="Técnicas">Técnicas</option>
                            <option value="Profesionales especializadas">Profesionales especializadas</option>
                            <option value="Supervisión o coordinación">Supervisión o coordinación</option>
                            <option value="Dirección o gerencia">Dirección o gerencia</option>
                            <option value="Atención directa al público, usuarios o clientes">Atención directa al público, usuarios o clientes</option>
                            <option value="Otra">Otra</option>
                        </select>
                        @error('actividad_trabajo') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    @if($actividad_trabajo === 'Otra')
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            Por favor, especifica tu actividad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="actividad_trabajo_otra" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" placeholder="Escribe tu actividad">
                        @error('actividad_trabajo_otra') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <!-- Tiempo Trabajando -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            5. ¿Cuánto tiempo tiene trabajando en esta organización? <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="tiempo_trabajando" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none">
                            <option value="">Selecciona una opción</option>
                            <option value="Menos de 6 meses">Menos de 6 meses</option>
                            <option value="De 6 meses a 1 año">De 6 meses a 1 año</option>
                            <option value="Más de 1 año a 3 años">Más de 1 año a 3 años</option>
                            <option value="Más de 3 años a 5 años">Más de 3 años a 5 años</option>
                            <option value="Más de 5 años">Más de 5 años</option>
                        </select>
                        @error('tiempo_trabajando') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            6. Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" wire:model="telefono" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" placeholder="Ej. 844 123 4567">
                        @error('telefono') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Correo (opcional) -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            7. Correo electrónico <span class="text-xs font-normal text-slate-400">(opcional)</span>
                        </label>
                        <input type="email" wire:model="correo" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" placeholder="Ej. nombre@correo.com">
                        @error('correo') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="button" 
                        wire:click="irACuestionario"
                        style="background: linear-gradient(to right, #2563eb, #1d4ed8); color: #ffffff;"
                        class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150 flex items-center justify-center space-x-2 text-lg">
                    <span>Continuar al Cuestionario</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Paso 3: Cuestionario -->
        @if ($step === 'cuestionario' && $consentimiento_otorgado === 'si')
            <form wire:submit.prevent="submit" class="space-y-8" x-show="step === 'cuestionario'">
                @csrf

                <!-- Section 1: Ansiedad -->
                <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden">
                    <div class="bg-[#8CC63F] px-8 py-5 text-white">
                        <h2 class="text-lg font-bold">Módulo 1: Ansiedad</h2>
                    </div>
                    <div class="p-8 space-y-8">
                        @foreach([
                            1 => '¿Te has sentido nervioso(a), ansioso(a) o con los nervios de punta?',
                            2 => '¿No has sido capaz de detener o controlar tus preocupaciones?',
                            3 => '¿Te has preocupado demasiado por diferentes cosas?',
                            4 => '¿Has tenido dificultad para relajarte?',
                            5 => '¿Has estado tan inquieto(a) que te es difícil permanecer quieto(a)?',
                            6 => '¿Te has sentido irritado(a) o te has enojado fácilmente?',
                            7 => '¿Has sentido miedo como si algo terrible fuera a pasar?'
                        ] as $index => $pregunta)
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                                {{ $index }}. {{ $pregunta }}
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                @foreach(['0' => 'Nunca', '1' => 'Varios días', '2' => 'Más de la mitad de los días', '3' => 'Casi todos los días'] as $val => $label)
                                    <label class="flex items-center justify-center py-3 px-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $this->{'ansiedad_'.$index} == $val && $this->{'ansiedad_'.$index} !== null ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                        <input type="radio" wire:model.live="ansiedad_{{ $index }}" value="{{ $val }}" class="sr-only" />
                                        <span class="text-xs sm:text-sm font-medium text-center leading-tight">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('ansiedad_'.$index) <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Section 2: Depresión -->
                <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden">
                    <div class="bg-[#2AB288] px-8 py-5 text-white">
                        <h2 class="text-lg font-bold">Módulo 2: Depresión</h2>
                    </div>
                    <div class="p-8 space-y-8">
                        @foreach([
                            1 => '¿Has tenido poco interés o placer en hacer cosas?',
                            2 => '¿Te has sentido desanimado(a), deprimido(a) o sin esperanza?',
                            3 => '¿Has tenido dificultad para dormir, o has estado durmiendo demasiado?',
                            4 => '¿Te has sentido cansado(a) o con poca energía?',
                            5 => '¿Has tenido poco apetito o has comido en exceso?',
                            6 => '¿Te has sentido mal contigo mismo(a), o has sentido que eres un fracaso o que te has defraudado a ti mismo(a) o a tu familia?',
                            7 => '¿Has tenido dificultad para concentrarte en cosas cotidianas, como leer el periódico o ver la televisión?',
                            8 => '¿Te has movido o hablado tan despacio que otras personas lo notaron? O por el contrario, ¿has estado tan inquieto(a) o agitado(a) que te has movido mucho más de lo habitual?',
                            9 => '¿Has tenido pensamientos de que estarías mejor muerto(a) o de hacerte daño de alguna manera?'
                        ] as $index => $pregunta)
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                                {{ $index }}. {{ $pregunta }}
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                @foreach(['0' => 'Nunca', '1' => 'Varios días', '2' => 'Más de la mitad de los días', '3' => 'Casi todos los días'] as $val => $label)
                                    <label class="flex items-center justify-center py-3 px-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $this->{'depresion_'.$index} == $val && $this->{'depresion_'.$index} !== null ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 ring-2 ring-emerald-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                        <input type="radio" wire:model.live="depresion_{{ $index }}" value="{{ $val }}" class="sr-only" />
                                        <span class="text-xs sm:text-sm font-medium text-center leading-tight">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('depresion_'.$index) <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Section 3: Riesgo Suicida -->
                <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden">
                    <div class="bg-[#29BFE0] px-8 py-5 text-white">
                        <h2 class="text-lg font-bold">Módulo 3: Ideación y Riesgo Suicida</h2>
                    </div>
                    <div class="p-8 space-y-8">
                        @foreach([
                            1 => 'En las últimas semanas, ¿has deseado estar muerto(a)?',
                            2 => 'En las últimas semanas, ¿has sentido que tú o tu familia estarían mejor si estuvieras muerto(a)?',
                            3 => 'En la última semana, ¿has tenido pensamientos sobre quitarte la vida?',
                            4 => '¿Alguna vez has intentado quitarte la vida?'
                        ] as $index => $pregunta)
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                                {{ $index }}. {{ $pregunta }}
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach(['0' => 'No', '1' => 'Sí'] as $val => $label)
                                    <label class="flex items-center justify-center py-3 px-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $this->{'suicidio_'.$index} == $val && $this->{'suicidio_'.$index} !== null ? 'border-orange-600 bg-orange-50/50 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 ring-2 ring-orange-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                        <input type="radio" wire:model.live="suicidio_{{ $index }}" value="{{ $val }}" class="sr-only" />
                                        <span class="text-sm font-medium text-center leading-tight">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('suicidio_'.$index) <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" wire:loading.attr="disabled"
                            style="background: linear-gradient(to right, #92c644, #84b33d); color: #ffffff;"
                            class="w-full py-4 px-6 bg-gradient-to-r from-[#92c644] to-[#84b33d] hover:from-[#84b33d] hover:to-[#749d36] text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#92c644] focus:ring-offset-2 transition-all duration-150 disabled:opacity-50 flex items-center justify-center space-x-2 text-lg">
                        <span wire:loading.remove>Enviar Cuestionario</span>
                        <span wire:loading>Enviando...</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
            </form>
        @endif
    @endif

</div>
