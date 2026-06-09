<div class="max-w-xl mx-auto px-4 sm:px-6">
    
    @if ($success)
        <!-- Success State -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-xl rounded-3xl p-8 sm:p-12 text-center space-y-6">
            <div class="h-20 w-20 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 rounded-full flex items-center justify-center mx-auto shadow-inner animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <div class="space-y-2">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">¡Muchas gracias!</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-sm mx-auto leading-relaxed">
                    Tus respuestas han sido enviadas de manera **estrictamente anónima y confidencial**.
                </p>
            </div>

            <div class="h-px bg-slate-100 dark:bg-slate-800 my-4"></div>

            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs mx-auto">
                Este diagnóstico nos ayuda a evaluar y mejorar el clima de bienestar y salud emocional dentro de <strong class="font-semibold text-slate-600 dark:text-slate-300">{{ $empresa->nombre_empresa }}</strong>.
            </p>
        </div>
    @else
        <!-- Header Info Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-md rounded-2xl p-6 mb-8 text-center space-y-3">
            <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest bg-blue-50 dark:bg-blue-950/40 px-3 py-1 rounded-full">Cuestionario de Diagnóstico</span>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                {{ $empresa->nombre_empresa }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                Por favor, responde con honestidad. Este tamizaje evalúa aspectos de salud emocional y no recopila ninguna información de identidad (nombre, correo o IP).
            </p>
        </div>

        <!-- Form Questionnaire -->
        <form wire:submit.prevent="submit" class="space-y-8">
            @csrf

            <!-- Section 1: Ansiedad -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 text-white">
                    <h2 class="text-lg font-bold">Módulo 1: Ansiedad</h2>
                    <p class="text-blue-100 text-xs mt-0.5">Evalúa el nivel de tensión y preocupación.</p>
                </div>
                <div class="p-6 space-y-6">
                    
                    <!-- Pregunta 1 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            1. ¿Te has sentido nervioso(a), ansioso(a) o con los nervios de punta?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $ansiedad_1 === $val ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="ansiedad_1" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('ansiedad_1') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pregunta 2 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            2. ¿No has sido capaz de detener o controlar tus preocupaciones?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $ansiedad_2 === $val ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="ansiedad_2" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('ansiedad_2') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pregunta 3 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            3. ¿Has tenido dificultad para relajarte?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $ansiedad_3 === $val ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="ansiedad_3" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('ansiedad_3') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            <!-- Section 2: Depresión -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-4 text-white">
                    <h2 class="text-lg font-bold">Módulo 2: Depresión</h2>
                    <p class="text-indigo-100 text-xs mt-0.5">Evalúa el estado de ánimo y la energía vital.</p>
                </div>
                <div class="p-6 space-y-6">
                    
                    <!-- Pregunta 4 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            4. ¿Te has sentido decaído(a), deprimido(a) o sin esperanzas?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $depresion_1 === $val ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="depresion_1" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('depresion_1') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pregunta 5 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            5. ¿Has tenido poco interés o placer en hacer las cosas?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $depresion_2 === $val ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="depresion_2" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('depresion_2') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pregunta 6 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            6. ¿Te has sentido cansado(a) o con poca energía?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $depresion_3 === $val ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="depresion_3" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('depresion_3') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            <!-- Section 3: Riesgo Suicida -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-red-600 to-rose-600 px-6 py-4 text-white">
                    <h2 class="text-lg font-bold">Módulo 3: Ideación y Riesgo Suicida</h2>
                    <p class="text-red-100 text-xs mt-0.5">Evalúa pensamientos relacionados con el deseo de vivir.</p>
                </div>
                <div class="p-6 space-y-6">
                    
                    <!-- Pregunta 7 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            7. ¿Has tenido pensamientos de que estarías mejor muerto(a) o de hacerte daño de alguna manera?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $suicidio_1 === $val ? 'border-red-600 bg-red-50/50 dark:bg-red-950/20 text-red-700 dark:text-red-300 ring-2 ring-red-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="suicidio_1" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('suicidio_1') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pregunta 8 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            8. ¿Has sentido que la vida no vale la pena?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $suicidio_2 === $val ? 'border-red-600 bg-red-50/50 dark:bg-red-950/20 text-red-700 dark:text-red-300 ring-2 ring-red-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="suicidio_2" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('suicidio_2') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pregunta 9 -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            9. ¿Has deseado no despertar o desaparecer?
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['0' => 'Nunca', '1' => 'A veces', '2' => 'Casi siempre'] as $val => $label)
                                <label class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-150 {{ $suicidio_3 === $val ? 'border-red-600 bg-red-50/50 dark:bg-red-950/20 text-red-700 dark:text-red-300 ring-2 ring-red-500/20 font-semibold' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 text-slate-600 dark:text-slate-400' }}">
                                    <input type="radio" wire:model="suicidio_3" value="{{ $val }}" class="sr-only" />
                                    <span class="text-xs">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('suicidio_3') <span class="text-xs text-red-500 block font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 text-center flex items-center justify-center space-x-2">
                    <span>Enviar Cuestionario Anónimo</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            
        </form>
    @endif

</div>
