<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Distintivo +Feliz: Reconocimiento del Gobierno del Estado de Coahuila para organizaciones que promueven el cuidado y prevención de la salud mental laboral.">
    <title>+Feliz | Distintivo de Salud Mental</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-24 py-2 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                @php
                    $partnerLogo = \App\Models\Setting::where('key', 'landing_partner_logo')->first()?->value;
                @endphp
                @if($partnerLogo)
                    <img src="{{ Storage::disk('public')->url($partnerLogo) }}" alt="Partner" class="w-auto border-r border-slate-200 pr-4" style="height: 76px;" />
                @endif
                <img src="{{ asset('images/coahuila.png') }}" alt="Gobierno de Coahuila" class="w-auto object-contain" style="height: 84px;" />
                <span class="h-12 w-px bg-slate-200"></span>
                <img src="{{ asset('images/inspira.png') }}" alt="Inspira Coahuila" class="w-auto object-contain" style="height: 108px;" />
            </div>
            <nav class="flex items-center space-x-6">
                <a href="#ambitos" class="text-sm font-medium text-slate-600 hover:text-blue-600:text-blue-400 transition-colors">Ámbitos</a>
                <a href="#registro" class="text-sm font-medium text-slate-600 hover:text-blue-600:text-blue-400 transition-colors">Registro</a>
                <a href="/tablero" class="px-4 py-2 text-sm font-semibold text-white bg-[#92c644] hover:bg-[#84b33d] rounded-lg shadow-sm hover:shadow transition-all duration-150">
                    Acceso Portal
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-b from-blue-50/50 via-white to-slate-50 pt-20 pb-16 sm:pt-28 sm:pb-20">
            <!-- Decorative Background Gradients -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
                <div class="absolute top-60 -left-20 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl"></div>
            </div>
            
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">

                <h1 class="flex items-center justify-center">
                    <img src="{{ asset('images/+FELIZ-LOGO.png') }}" alt="+Feliz" class="h-64 sm:h-80 md:h-96 w-auto object-contain hover:scale-105 transition-transform duration-300">
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-slate-600 max-w-3xl mx-auto font-light leading-relaxed">
                    Un reconocimiento oficial para visibilizar las acciones sobresalientes en la <strong class="font-semibold text-slate-800">prevención, cuidado y fortalecimiento de la Salud Mental</strong> dentro de nuestra comunidad.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="#registro" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-[#92c644] to-[#84b33d] hover:from-[#84b33d] hover:to-[#749d36] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-150 transform hover:-translate-y-0.5 text-center">
                        Registrar mi Organización
                    </a>
                    
                    <!-- Split Button "Conoce +" con Dropdown de Descargas -->
                    <div x-data="{ abierto: false }" @click.outside="abierto = false" class="relative inline-flex w-full sm:w-auto rounded-xl shadow-sm">
                        <!-- Lado Izquierdo (Acción Principal) -->
                        <a href="#bento-grid-section" class="w-full sm:w-auto flex-grow sm:flex-grow-0 px-8 py-4 bg-white border border-slate-200 border-r-0 text-slate-700 hover:bg-slate-50 font-semibold rounded-l-xl transition-all duration-150 text-center cursor-pointer flex items-center justify-center">
                            Conoce +
                        </a>
                        <!-- Lado Derecho (Chevron Dropdown Toggle) -->
                        <button type="button" @click="abierto = !abierto" class="px-4 py-4 bg-white border border-slate-200 border-l border-l-slate-200 text-slate-700 hover:bg-slate-50 font-semibold rounded-r-xl transition-all duration-150 flex items-center justify-center cursor-pointer" id="download-dropdown-toggle" aria-expanded="false" :aria-expanded="abierto.toString()">
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="abierto ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Menú Desplegable (Dropdown) -->
                        <div x-show="abierto" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute left-0 right-0 sm:left-auto sm:right-0 top-full mt-2 w-full sm:w-72 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ asset('docs/Convocatoria+Feliz.pdf') }}" download class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-150">
                                    <div class="h-8 w-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-left flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 truncate text-sm">Convocatoria Oficial</p>
                                        <p class="text-xs text-slate-400">Descargar PDF</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                                <div class="border-t border-slate-100"></div>
                                <a href="{{ asset('docs/guia-de-criterios-mas-feliz.pdf') }}" download class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-150">
                                    <div class="h-8 w-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-left flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 truncate text-sm">Guía de Criterios</p>
                                        <p class="text-xs text-slate-400">Descargar PDF</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- What is it & Objectives Section -->
        <section id="bento-grid-section" class="py-20 bg-white border-b border-slate-100 transition-colors duration-300">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                
                <!-- What is it -->
                <div class="space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        ¿Qué es?
                    </h2>
                    <p class="text-lg sm:text-xl text-slate-600 leading-relaxed">
                        <strong class="font-bold text-[#92c644]">+Feliz</strong>, es un distintivo otorgado por el Gobierno del Estado de Coahuila a través de la Oficina Inspira Coahuila, la Secretaría de Economía y la Secretaría de Salud para reconocer a las organizaciones que implementan acciones sistemáticas, medibles y sostenidas para la <strong class="font-semibold text-slate-800">prevención, cuidado y fortalecimiento</strong> de la <strong class="font-semibold text-slate-800">Salud Mental</strong> de sus colaboradores.
                    </p>
                </div>

                <!-- Objectives -->
                <div class="mt-20 space-y-8">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        Objetivos
                    </h2>
                    <ul class="space-y-5">
                        <!-- Objective 1 -->
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-4 text-slate-600 leading-relaxed">
                                <strong class="font-medium text-slate-800">Evaluar y reconocer organizaciones</strong> que cumplen con prácticas de cuidado integral y salud mental en el entorno laboral.
                            </p>
                        </li>
                        <!-- Objective 2 -->
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-4 text-slate-600 leading-relaxed">
                                <strong class="font-medium text-slate-800">Promover la implementación de programas</strong> internos de salud mental en las organizaciones.
                            </p>
                        </li>
                        <!-- Objective 3 -->
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-4 text-slate-600 leading-relaxed">
                                <strong class="font-medium text-slate-800">Sensibilizar a las organizaciones</strong> sobre la importancia de priorizar la salud mental como un elemento clave en la productividad y retención de talento.
                            </p>
                        </li>
                        <!-- Objective 4 -->
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-4 text-slate-600 leading-relaxed">
                                <strong class="font-medium text-slate-800">Fomentar entornos protectores</strong> dentro de los espacios laborales a través de acciones concretas para reducir riesgos psicosociales.
                            </p>
                        </li>
                        <!-- Objective 5 -->
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-4 text-slate-600 leading-relaxed">
                                <strong class="font-medium text-slate-800">Impulsar la corresponsabilidad</strong> entre sector público y sector privado para la prevención y atención de la salud mental.
                            </p>
                        </li>
                    </ul>
                </div>

            </div>
        </section>

        <!-- Spheres (Ámbitos) Section -->
        <section id="ambitos" class="py-20 bg-slate-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        ¿Quiénes pueden ser parte del Distintivo +Feliz?
                    </h2>
                    <p class="text-slate-500">
                        El distintivo +Feliz premia proyectos y planes en diferentes sectores de la sociedad. Encuentra la categoría adecuada para tu organización.
                    </p>
                </div>

                <!-- Flex layout to center orphans -->
                <div class="flex flex-wrap justify-center gap-8">
                    
                    <!-- Public Area -->
                    <div class="w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.35rem)] group p-8 rounded-2xl bg-white shadow-xl shadow-slate-200/40 border border-slate-100 hover:border-[#8CC63F]:border-[#8CC63F] hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-[#8CC63F]/10 text-[#8CC63F] rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800">Ámbito Público</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Dependencias y oficinas de Gobierno Municipal y Estatal
                        </p>
                    </div>

                    <!-- Educational Area -->
                    <div class="w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.35rem)] group p-8 rounded-2xl bg-white shadow-xl shadow-slate-200/40 border border-slate-100 hover:border-[#2AB288]:border-[#2AB288] hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-[#2AB288]/10 text-[#2AB288] rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800">Ámbito Educativo</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Escuelas públicas y privadas de todos los niveles educativos, incluyendo el sector público y privado.
                        </p>
                    </div>

                    <!-- Industrial/Productive Area -->
                    <div class="w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.35rem)] group p-8 rounded-2xl bg-white shadow-xl shadow-slate-200/40 border border-slate-100 hover:border-[#29BFE0]:border-[#29BFE0] hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-[#29BFE0]/10 text-[#29BFE0] rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800">Ámbito Productivo o Industrial</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Empresas públicas, privadas y fabricas.
                        </p>
                    </div>

                    <!-- Social/Community Area -->
                    <div class="w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.35rem)] group p-8 rounded-2xl bg-white shadow-xl shadow-slate-200/40 border border-slate-100 hover:border-[#F49F00]:border-[#F49F00] hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-[#F49F00]/10 text-[#F49F00] rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800">Ámbito Social y Comunitario</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            ONG, Fundaciones, Cooperativas, Organizaciones de vecinos, Clubes, Centros comunitarios, etc.
                        </p>
                    </div>

                    <!-- Others Area -->
                    <div class="w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.35rem)] group p-8 rounded-2xl bg-white shadow-xl shadow-slate-200/40 border border-slate-100 hover:border-[#E6007E]:border-[#E6007E] hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-[#E6007E]/10 text-[#E6007E] rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800">Otros Ámbitos</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Ámbitos complementarios no contemplados en los anteriores.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- 20 Criterios Section -->
        <section id="criterios" x-data="{ active: null }" class="py-24 bg-white transition-colors duration-300 border-t border-slate-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col lg:flex-row items-center justify-between gap-16 lg:gap-8">
                    <!-- Left side: Title -->
                    <div class="flex flex-col items-center lg:items-start space-y-4 text-center lg:text-left max-w-md">
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
                            El Distintivo <span class="text-[#92c644] font-black">+Feliz</span> evalúa 20 criterios
                        </h2>
                        <p class="text-sm text-slate-500">
                            Divididos en tres ejes fundamentales de acción para la salud mental de la organización.
                        </p>
                    </div>

                    <!-- Right side: Circles -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-10 lg:gap-12">
                        <!-- Circle 1: Prevención -->
                        <div class="flex flex-col items-center space-y-4 cursor-pointer"
                             @mouseenter="active = 1" @mouseleave="active = null" @click="active = (active === 1 ? null : 1)">
                            <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-[#E6007E]/70 flex items-center justify-center shadow-lg transform transition-all duration-300"
                                 :class="active === 1 ? 'scale-105 ring-4 ring-[#E6007E]/30 -translate-y-2' : ''">
                                <span class="text-5xl sm:text-6xl font-black text-white drop-shadow-md">6</span>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight whitespace-nowrap">+ Prevención</span>
                        </div>
                        
                        <!-- Circle 2: Cuidado -->
                        <div class="flex flex-col items-center space-y-4 cursor-pointer"
                             @mouseenter="active = 2" @mouseleave="active = null" @click="active = (active === 2 ? null : 2)">
                            <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-[#F49F00]/70 flex items-center justify-center shadow-lg transform transition-all duration-300"
                                 :class="active === 2 ? 'scale-105 ring-4 ring-[#F49F00]/30 -translate-y-2' : ''">
                                <span class="text-5xl sm:text-6xl font-black text-white drop-shadow-md">6</span>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight whitespace-nowrap">+ Cuidado/Atención</span>
                        </div>
                        
                        <!-- Circle 3: Fortalecimiento -->
                        <div class="flex flex-col items-center space-y-4 cursor-pointer"
                             @mouseenter="active = 3" @mouseleave="active = null" @click="active = (active === 3 ? null : 3)">
                            <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-[#2AB288]/70 flex items-center justify-center shadow-lg transform transition-all duration-300"
                                 :class="active === 3 ? 'scale-105 ring-4 ring-[#2AB288]/30 -translate-y-2' : ''">
                                <span class="text-5xl sm:text-6xl font-black text-white drop-shadow-md">8</span>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight whitespace-nowrap">+ Fortalecimiento</span>
                        </div>
                    </div>
                </div>

                <!-- Description Box -->
                <div class="mt-12 min-h-[80px] bg-slate-50 border border-slate-100 rounded-2xl p-6 flex items-center justify-center text-center transition-all duration-300">
                    <p x-show="active === null" class="text-sm text-slate-500 font-medium">
                        Pasa el cursor o haz clic sobre los círculos para ver el detalle de cada eje de evaluación.
                    </p>
                    <p x-show="active === 1" class="text-sm text-slate-600 font-medium" x-transition>
                        El eje <b>+Prevención</b> tiene como objetivo identificar de manera temprana los factores individuales y organizacionales que pueden afectar la salud mental de las personas trabajadoras, con el fin de reducir riesgos y fortalecer factores protectores antes de que generen consecuencias mayores.
Este eje promueve la evaluación periódica de la salud mental del personal, la identificación y gestión de riesgos psicosociales, el análisis del clima organizacional, la sensibilización y capacitación del personal, así como el desarrollo de acciones orientadas a la promoción de la salud mental.
A través de estas estrategias, las organizaciones fortalecen su capacidad para anticipar riesgos, tomar decisiones basadas en evidencia y desarrollar entornos laborales más seguros, respetuosos y protectores para las personas trabajadoras.

                    </p>
                    <p x-show="active === 2" class="text-sm text-slate-600 font-medium" x-transition>
                        El eje <b>+Cuidado/Atención</b> integra las acciones y mecanismos que permiten a las organizaciones brindar apoyo oportuno a las personas trabajadoras, así como generar condiciones organizacionales y físicas que favorezcan la protección de la salud mental.
Este eje considera la implementación de entornos laborales protectores, condiciones adecuadas del ambiente físico, acceso a servicios de apoyo psicológico, difusión de recursos de atención, mecanismos de orientación y canalización, así como protocolos para la atención de situaciones de crisis de salud mental.
Su propósito es asegurar que las personas trabajadoras cuenten con recursos accesibles, oportunos y confidenciales para recibir apoyo cuando lo requieran, contribuyendo a la protección de su salud mental y al fortalecimiento de la capacidad de respuesta de la organización ante situaciones de riesgo.

                    </p>
                    <p x-show="active === 3" class="text-sm text-slate-600 font-medium" x-transition>
                        El eje <b>+Fortalecimiento</b> tiene como objetivo consolidar las estructuras, políticas, mecanismos de coordinación y procesos institucionales necesarios para integrar la salud mental como un componente permanente de la gestión organizacional.
Este eje impulsa la participación activa de la alta dirección, la definición de responsabilidades, la operación de instancias formales de coordinación, el establecimiento de políticas institucionales y la implementación de mecanismos de seguimiento, evaluación y mejora continua.
A través de estas acciones, las organizaciones fortalecen su capacidad para sostener en el tiempo las estrategias de salud mental, asegurar su integración en los procesos institucionales y promover una cultura organizacional orientada al cuidado, la corresponsabilidad y la protección de las personas trabajadoras.

                    </p>
                </div>

            </div>
        </section>

        <!-- Beneficios Section -->
        <section id="beneficios" class="py-24 bg-slate-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    
                    <!-- Left Content -->
                    <div class="lg:col-span-8 space-y-10">
                        <!-- Title -->
                        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                            Beneficios para las organizaciones
                        </h2>
                        
                        <!-- List -->
                        <ul class="space-y-5 mt-8">
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Reconocimiento público como organización comprometida con la salud mental</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Alineación con normativas laborales (NOM-035)</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Mayor prestigio y posicionamiento</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Incremento en la productividad y el clima laboral positivo</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Reducción de conflictos laborales</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Impulso a la innovación organizacional</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Mejora en los indicadores de responsabilidad social empresarial</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Fortalecimiento de la resiliencia organizacional</span>
                            </li>
                            <li class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#92c644]/10 text-[#92c644] flex items-center justify-center mt-1 group-hover:bg-[#92c644] group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-4 text-lg text-slate-600 leading-relaxed">Visibilidad en páginas del Gobierno del Estado</span>
                            </li>
                        </ul>

                        <!-- Bottom text box -->
                        <div class="mt-10 pt-8 border-t border-slate-200">
                            <p class="text-lg text-slate-600 leading-relaxed">
                                El Distintivo <strong class="font-bold text-[#92c644]">+Feliz</strong> no solo impulsa a las organizaciones a mejorar; también reconoce y hace visible a aquellas que ya lo están haciendo.
                            </p>
                        </div>
                    </div>

                    <!-- Right Icon (Distintivo) -->
                    <div class="lg:col-span-4 flex justify-center lg:justify-end">
                        <div class="relative group">
                            <!-- Background glow -->
                            <div class="absolute -inset-4 bg-[#F49F00] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity duration-500"></div>
                            
                            <!-- Ribbon Badge SVG -->
                            <svg class="relative w-40 h-40 sm:w-56 sm:h-56 text-[#F49F00] drop-shadow-lg transform group-hover:scale-105 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Pasos a Seguir Section -->
        <section id="pasos" class="py-24 bg-white transition-colors duration-300 border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title -->
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        Pasos a seguir
                    </h2>
                </div>

                <!-- Steps Diagram -->
                <div class="flex flex-col lg:flex-row items-start justify-center gap-6 lg:gap-4 xl:gap-6 mt-16">
                    
                    <!-- Step 1: Registro -->
                    <div class="flex flex-col items-center text-center w-full lg:w-32 xl:w-40 group">
                        <div class="w-20 h-20 bg-[#8CC63F] text-white rounded-full flex items-center justify-center transition-transform duration-300 shadow-md group-hover:scale-110 mb-4">
                            <!-- Clipboard pen SVG -->
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-tight">1. Registro</span>
                    </div>

                    <!-- Arrow -->
                    <div class="hidden lg:flex text-slate-400 mt-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </div>
                    <div class="flex lg:hidden text-slate-400 my-2">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                    </div>

                    <!-- Step 2: Diagnóstico inicial -->
                    <div class="flex flex-col items-center text-center w-full lg:w-32 xl:w-40 group">
                        <div class="w-20 h-20 bg-[#2AB288] text-white rounded-full flex items-center justify-center transition-transform duration-300 shadow-md group-hover:scale-110 mb-4">
                            <!-- People talking SVG -->
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-tight">2. Diagnóstico inicial/Autoevaluación</span>
                    </div>

                    <!-- Arrow -->
                    <div class="hidden lg:flex text-slate-400 mt-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </div>
                    <div class="flex lg:hidden text-slate-400 my-2">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                    </div>

                    <!-- Step 3: Retroalimentación y Acompañamiento -->
                    <div class="flex flex-col items-center text-center w-full lg:w-32 xl:w-40 group">
                        <div class="w-20 h-20 bg-[#29BFE0] text-white rounded-full flex items-center justify-center transition-transform duration-300 shadow-md group-hover:scale-110 mb-4">
                            <!-- Sync circular arrows chat SVG -->
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-tight">3. Retroalimentación y Acompañamiento</span>
                    </div>

                    <!-- Arrow -->
                    <div class="hidden lg:flex text-slate-400 mt-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </div>
                    <div class="flex lg:hidden text-slate-400 my-2">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                    </div>

                    <!-- Step 4: Plan de acción -->
                    <div class="flex flex-col items-center text-center w-full lg:w-32 xl:w-40 group">
                        <div class="w-20 h-20 bg-[#F49F00] text-white rounded-full flex items-center justify-center transition-transform duration-300 shadow-md group-hover:scale-110 mb-4">
                            <!-- Book check SVG -->
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-tight">4. Plan de acción/Implementación</span>
                    </div>

                    <!-- Arrow -->
                    <div class="hidden lg:flex text-slate-400 mt-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </div>
                    <div class="flex lg:hidden text-slate-400 my-2">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                    </div>

                    <!-- Step 5: Evaluación -->
                    <div class="flex flex-col items-center text-center w-full lg:w-32 xl:w-40 group">
                        <div class="w-20 h-20 bg-[#E6007E] text-white rounded-full flex items-center justify-center transition-transform duration-300 shadow-md group-hover:scale-110 mb-4">
                            <!-- Checklist hand click SVG -->
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-tight">5. Evaluación y Dictaminación</span>
                    </div>

                    <!-- Arrow -->
                    <div class="hidden lg:flex text-slate-400 mt-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </div>
                    <div class="flex lg:hidden text-slate-400 my-2">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                    </div>

                    <!-- Step 6: Reconocimiento -->
                    <div class="flex flex-col items-center text-center w-full lg:w-32 xl:w-40 group">
                        <!-- Looping back to the first color for the final step -->
                        <div class="w-20 h-20 bg-[#8CC63F] text-white rounded-full flex items-center justify-center transition-transform duration-300 shadow-md group-hover:scale-110 mb-4">
                            <!-- Badge SVG -->
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-tight">6. Reconocimiento acorde al nivel de Madurez</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- Registration Section (Livewire Form embedded) -->
        <section id="registro" class="py-20 bg-slate-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto space-y-4 mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        Registra tu Organización
                    </h2>
                    <p class="text-slate-500 text-sm">
                        Completa el formulario oficial para inscribir tu Organización y postularte al distintivo estatal.
                    </p>
                </div>

                <!-- Embed the Livewire/Volt Component -->
                <div class="relative z-10">
                    <livewire:registro-empresa-form />
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 border-t border-slate-900 py-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-12">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-12 border-b border-slate-900">
                <!-- Left: Info -->
                <div class="md:col-span-8 text-center md:text-left flex flex-col justify-center">
                    <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-6">
                        <img src="{{ asset('images/+FELIZ-LOGO.png') }}" alt="+Feliz" class="w-auto object-contain flex-shrink-0" style="height: 120px;" />
                        <p class="text-sm text-slate-400 max-w-xl leading-relaxed text-center md:text-left">
                            Iniciativa estatal para promover entornos organizacionales saludables, seguros y favorables para el desarrollo integral de la salud mental de los trabajadores.
                        </p>
                    </div>
                </div>
                
                <!-- Right: Contacto -->
                <div class="md:col-span-4 space-y-3 text-center md:text-left md:border-l md:border-slate-900 md:pl-8 flex flex-col justify-center">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Contacto</h4>
                    <div class="flex flex-col space-y-3 text-sm">
                        <a href="mailto:masfeliz@coahuila.gob.mx" class="flex items-center justify-center md:justify-start space-x-2 text-slate-300 hover:text-white transition-colors">
                            <svg class="w-4 h-4 text-[#92c644]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>masfeliz@coahuila.gob.mx</span>
                        </a>
                        <a href="https://wa.me/528446636977" target="_blank" class="flex items-center justify-center md:justify-start space-x-2 text-slate-300 hover:text-white transition-colors">
                            <svg class="w-4 h-4 text-[#92c644]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.993L2 22l5.184-1.359a9.95 9.95 0 0 0 4.824 1.243h.004c5.507 0 9.99-4.478 9.99-9.985A9.998 9.998 0 0 0 12.012 2zm6.035 14.224c-.25.705-1.458 1.34-2.012 1.418-.549.078-1.229.144-3.666-.86-2.955-1.217-4.836-4.243-4.985-4.441-.148-.198-1.208-1.603-1.208-3.058 0-1.455.76-2.171 1.032-2.469.273-.297.594-.372.793-.372.198 0 .396.002.57.01.18.007.42-.069.658.502.247.594.842 2.057.917 2.206.074.148.124.32.025.518-.099.198-.148.32-.297.495-.148.175-.313.39-.446.522-.148.148-.303.309-.13.607.173.298.769 1.266 1.65 2.049 1.135 1.009 2.09 1.321 2.387 1.47.298.148.471.124.645-.076.173-.198.743-.865.94-1.162.198-.297.396-.248.669-.148.272.099 1.732.817 2.029.965.297.148.495.223.57.346.074.124.074.717-.176 1.423z"/>
                            </svg>
                            <span>WhatsApp: 844 663 6977</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Credits & Logos -->
            <div class="flex flex-col md:flex-row items-center justify-between text-xs space-y-4 md:space-y-0 text-slate-600">
                <div class="flex flex-col space-y-2 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start space-x-2">
                        <span class="font-bold text-slate-500">+Feliz Coahuila</span>
                        <span>&copy; 2026. Todos los derechos reservados.</span>
                    </div>
                    <p class="max-w-2xl leading-relaxed">
                        Esta es una iniciativa impulsada por el Gobierno del Estado de Coahuila de Zaragoza para la prevención, cuidado y fortalecimiento de la salud mental de los trabajadores.
                    </p>
                </div>
                
                <!-- Small logo badges, like payment icons in footer -->
                <div class="relative flex flex-wrap items-center justify-center md:justify-end gap-6 pt-2 md:pt-0">
                    <!-- Subtle Glow / Lens Flare Effect -->
                    <div class="absolute -inset-6 bg-[radial-gradient(circle,rgba(255,255,255,0.1)_0%,rgba(255,255,255,0)_70%)] blur-lg pointer-events-none rounded-full"></div>
                    
                    <img src="{{ asset('images/secretaria-economia.png') }}" alt="Secretaría de Economía" class="relative z-10 h-12 md:h-16 w-auto object-contain hover:scale-105 transition-transform duration-300" />
                    <img src="{{ asset('images/secretaria-salud.png') }}" alt="Secretaría de Salud" class="relative z-10 h-12 md:h-16 w-auto object-contain hover:scale-105 transition-transform duration-300" />
                    <img src="{{ asset('images/salud-mental.png') }}" alt="Salud Mental" class="relative z-10 h-6 md:h-8 w-auto object-contain hover:scale-105 transition-transform duration-300" />
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
