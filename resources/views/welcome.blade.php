<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <link class="favicon" rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>+Feliz | Distintivo de Salud Mental Coahuila</title>
    
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
        /* Custom scrollbar hiding for swipeable carousel */
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-stone-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-rose-500 selection:text-white">

    <!-- Header / Navigation Bar -->
    <header class="sticky top-0 z-40 bg-stone-50/80 backdrop-blur-md border-b border-stone-200/60 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Left Side: Government Partners -->
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/coahuila.png') }}" alt="Gobierno de Coahuila" class="w-auto object-contain" style="height: 60px;" />
                <span class="h-10 w-px bg-stone-300"></span>
                <img src="{{ asset('images/inspira.png') }}" alt="Inspira Coahuila" class="w-auto object-contain" style="height: 54px;" />
            </div>
            
            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="#bento" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">La Plataforma</a>
                <a href="#criterios" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">20 Criterios</a>
                <a href="#beneficios" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">Beneficios</a>
                <a href="#registro" class="px-5 py-2.5 text-sm font-bold text-white bg-[#749d36] hover:bg-[#84b33d] rounded-lg shadow-sm hover:shadow transition-all duration-150">
                    Registrar Empresa
                </a>
                <a href="/admin" class="text-sm font-semibold text-[#db2777] hover:text-rose-700 transition-colors pl-2">
                    Acceso Portal
                </a>
            </nav>
            
            <!-- Mobile Navigation Toggle (Direct to Registration) -->
            <div class="lg:hidden flex items-center space-x-3">
                <a href="/admin" class="text-xs font-bold text-slate-600 px-3 py-2">
                    Portal
                </a>
                <a href="#registro" class="px-4 py-2 text-xs font-bold text-white bg-[#749d36] rounded-md shadow-sm">
                    Registro
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        
        <!-- Hero Section: Editorial & Asymmetric with Video Background -->
        <section class="relative bg-stone-950 pt-20 pb-24 md:pt-28 md:pb-32 overflow-hidden border-b border-stone-850">
            <!-- Background Video -->
            <video autoplay loop muted playsinline poster="{{ asset('images/video-fallback.jpg') }}" class="absolute top-0 left-0 w-full h-full object-cover z-0 pointer-events-none">
                <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
            </video>
            
            <!-- Overlay / Scrim to ensure text legibility (WCAG AAA compliant contrast) -->
            <div class="absolute inset-0 bg-stone-950/70 z-10"></div>
            
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    
                    <!-- Left: Asymmetric Typography & Copy (White text for high contrast on dark overlay) -->
                    <div class="lg:col-span-7 space-y-8 text-left">
                        
                        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white leading-[1.15]">
                            La salud mental ya no es una métrica blanda. Es la base de la <span class="underline decoration-[#92c644] decoration-4 underline-offset-4">productividad</span> corporativa.
                        </h1>
                        
                        <p class="text-lg text-stone-300 max-w-2xl font-normal leading-relaxed">
                            Certifica a tu organización ante el Gobierno del Estado de Coahuila. Implementa el diagnóstico clínico, evalúa tus buenas prácticas y accede a una red de acompañamiento profesional sin costo.
                        </p>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-4">
                            <a href="#registro" class="px-8 py-4 bg-gradient-to-r from-[#92c644] to-[#749d36] hover:from-[#749d36] hover:to-[#166534] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-center">
                                Iniciar Autoevaluación
                            </a>
                            <a href="#bento" class="px-8 py-4 bg-stone-900/80 border border-stone-700 text-stone-200 hover:bg-stone-800 font-bold rounded-xl transition-all duration-200 text-center backdrop-blur-sm">
                                Conocer Plataforma
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right: Insignia Showcase (Translucent glassmorphism) -->
                    <div class="lg:col-span-5 flex justify-center lg:justify-end">
                        <div class="relative w-full max-w-md bg-stone-900/35 backdrop-blur-md p-8 rounded-3xl border border-white/10 shadow-2xl">
                            <div class="absolute -top-3 -right-3 h-8 w-8 bg-emerald-500 rounded-full flex items-center justify-center text-white font-bold text-xs shadow">✓</div>
                            
                            <div class="flex flex-col items-center text-center space-y-6">
                                <img src="{{ asset('images/mas-feliz.png') }}" alt="Distintivo +Feliz" class="h-28 sm:h-32 w-auto object-contain" />
                                
                                <div class="space-y-2">
                                    <h3 class="text-xl font-bold text-white">Distintivo Oficial +Feliz</h3>
                                    <p class="text-xs text-stone-400 max-w-xs">
                                        Reconocimiento estatal otorgado a las empresas con altos estándares en prevención psicosocial.
                                    </p>
                                </div>
                                
                                <div class="w-full bg-stone-950/50 p-4 rounded-xl border border-white/5 flex items-center justify-center text-center">
                                    <span class="inline-block text-[10px] font-bold bg-[#92c644]/20 text-[#92c644] px-2.5 py-1 rounded border border-[#92c644]/30">Prioritario</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <!-- Bento Grid: The Platform Architecture (Desktop Grid, Mobile Carousel) -->
        <section id="bento" class="py-24 bg-stone-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="max-w-3xl mb-16 text-left space-y-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#749d36]">Módulos del Programa</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                        Una plataforma integral para el entorno laboral
                    </h2>
                    <p class="text-slate-600 max-w-2xl font-light">
                        Diseñado para líderes de Recursos Humanos que buscan monitorear, diagnosticar y canalizar casos clínicos con rigor técnico.
                    </p>
                </div>

                <!-- Bento Container (Mobile scroll container, Desktop grid) -->
                <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none gap-6 pb-8 lg:pb-0 lg:grid lg:grid-cols-4 lg:grid-rows-2 lg:h-[640px] lg:gap-6 lg:overflow-x-visible lg:snap-none">
                    
                    <!-- Card A: El costo del silencio (cols 1 & 2, row 1) -->
                    <div class="snap-start shrink-0 w-[85%] md:w-[60%] lg:w-auto lg:shrink lg:col-span-3 lg:h-full overflow-hidden bg-white p-8 rounded-3xl border border-stone-200/80 shadow-sm flex flex-col justify-between space-y-8 transition-all duration-300 transform hover:scale-[1.01] hover:shadow-lg hover:border-[#749d36]/30">
                        <div class="space-y-4">
                            <span class="text-xs font-bold uppercase text-[#db2777]">1. Estadísticas & Retorno</span>
                            <h3 class="text-2xl font-bold text-slate-900">El Costo del Silencio Psicosocial</h3>
                            <p class="text-sm text-slate-600 leading-relaxed max-w-xl">
                                La falta de atención al estrés laboral (bajo la NOM-035) eleva el ausentismo y la rotación. Intervenir de forma temprana disminuye la rotación voluntaria hasta en un 35%.
                            </p>
                        </div>
                        

                    </div>
                    
                    <!-- Card B: Cédula de autoevaluación (col 3, row 1 & 2 - Tall Card) -->
                    <!-- Alpine.js interactive calculator simulation -->
                    <div x-data="{ lead: 10, health: 10, dev: 5, bienestar: 5, get total() { return (parseInt(this.lead) + parseInt(this.health) + parseInt(this.dev) + parseInt(this.bienestar)) * 5 } }" 
                         class="snap-start shrink-0 w-[85%] md:w-[60%] lg:w-auto lg:shrink lg:col-span-1 lg:row-span-2 lg:h-full overflow-hidden bg-white p-8 rounded-3xl border border-stone-200/80 shadow-sm flex flex-col justify-between space-y-6 transition-all duration-300 transform hover:scale-[1.01] hover:shadow-lg hover:border-[#749d36]/30">
                        <div class="space-y-4">
                            <span class="text-xs font-bold uppercase text-[#749d36]">2. Autoevaluación en Vivo</span>
                            <h3 class="text-2xl font-bold text-slate-900">Cédula de Madurez</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Evalúa tu empresa en base a los 25 criterios clínicos. Simula los valores abajo para ver tu nivel proyectado:
                            </p>
                        </div>
                        
                        <!-- Mini Interactive Selector inside Bento Card -->
                        <div class="space-y-3 bg-stone-50 p-4 rounded-2xl border border-stone-100 text-xs">
                            <!-- Category 1 -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-slate-600">Liderazgo y Prevención</span>
                                <div class="flex bg-stone-200/60 p-0.5 rounded-lg shrink-0">
                                    <button type="button" @click="lead = 10" :class="lead == 10 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Sí</button>
                                    <button type="button" @click="lead = 5" :class="lead == 5 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Proc.</button>
                                    <button type="button" @click="lead = 0" :class="lead == 0 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">No</button>
                                </div>
                            </div>
                            <!-- Category 2 -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-slate-600">Salud Emocional</span>
                                <div class="flex bg-stone-200/60 p-0.5 rounded-lg shrink-0">
                                    <button type="button" @click="health = 10" :class="health == 10 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Sí</button>
                                    <button type="button" @click="health = 5" :class="health == 5 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Proc.</button>
                                    <button type="button" @click="health = 0" :class="health == 0 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">No</button>
                                </div>
                            </div>
                            <!-- Category 3 -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-slate-600">Desarrollo Humano</span>
                                <div class="flex bg-stone-200/60 p-0.5 rounded-lg shrink-0">
                                    <button type="button" @click="dev = 10" :class="dev == 10 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Sí</button>
                                    <button type="button" @click="dev = 5" :class="dev == 5 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Proc.</button>
                                    <button type="button" @click="dev = 0" :class="dev == 0 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">No</button>
                                </div>
                            </div>
                            <!-- Category 4 -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-slate-600">Entorno Psicosocial</span>
                                <div class="flex bg-stone-200/60 p-0.5 rounded-lg shrink-0">
                                    <button type="button" @click="bienestar = 10" :class="bienestar == 10 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Sí</button>
                                    <button type="button" @click="bienestar = 5" :class="bienestar == 5 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">Proc.</button>
                                    <button type="button" @click="bienestar = 0" :class="bienestar == 0 ? 'bg-white text-slate-800 font-bold shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md text-[10px] transition-all">No</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dynamic Results Output (WCAG Safe Contrast) -->
                        <div class="bg-slate-900 text-white p-4 rounded-xl space-y-2">
                            <div class="flex justify-between items-baseline">
                                <span class="text-[10px] text-slate-400 font-bold uppercase">Puntaje Estimado</span>
                                <span class="text-xl font-bold font-mono text-[#92c644]" x-text="total + ' pts'"></span>
                            </div>
                            <div class="flex justify-between items-center border-t border-slate-800 pt-2 text-[10px] font-bold">
                                <span class="text-slate-400">Nivel Proyectado:</span>
                                <span class="px-2 py-0.5 rounded text-white" 
                                      :class="total >= 150 ? 'bg-[#749d36]' : (total >= 80 ? 'bg-[#ca8a04]' : 'bg-[#c2410c]')"
                                      x-text="total >= 150 ? 'Sobresaliente' : (total >= 80 ? 'En Proceso' : 'Inicial')">
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card C: El Tamizaje Anónimo (col 1, row 2) -->
                    <div class="snap-start shrink-0 w-[85%] md:w-[60%] lg:w-auto lg:shrink lg:col-span-1 lg:h-full overflow-hidden bg-white p-8 rounded-3xl border border-stone-200/80 shadow-sm flex flex-col justify-between space-y-6 transition-all duration-300 transform hover:scale-[1.01] hover:shadow-lg hover:border-[#749d36]/30">
                        <div class="space-y-4">
                            <span class="text-xs font-bold uppercase text-[#ca8a04]">3. Diagnóstico Clínico</span>
                            <h3 class="text-2xl font-bold text-slate-900">Tamizaje Clínico</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Evaluaciones rápidas y anónimas mediante tokens cifrados. Analizan sub-módulos de **Ansiedad**, **Depresión** e **Ideación Suicida** de forma segura.
                            </p>
                        </div>
                        

                    </div>
                    
                    <!-- Card D: Acompañamiento - Lo Que Sientes (col 2, row 2) -->
                    <div class="snap-start shrink-0 w-[85%] md:w-[60%] lg:w-auto lg:shrink lg:col-span-2 lg:h-full overflow-hidden bg-white p-8 rounded-3xl border border-stone-200/80 shadow-sm flex flex-col justify-between space-y-8 transition-all duration-300 transform hover:scale-[1.01] hover:shadow-lg hover:border-[#749d36]/30">
                        <div class="space-y-4">
                            <span class="text-xs font-bold uppercase text-[#db2777]">4. Contención Inmediata</span>
                            <h3 class="text-2xl font-bold text-slate-900">Acompañamiento y Línea de Apoyo</h3>
                            <p class="text-sm text-slate-600 leading-relaxed max-w-xl">
                                Los colaboradores identificados con riesgo elevado obtienen acceso inmediato de contención con psicólogos especializados de la red estatal.
                            </p>
                        </div>
                        
                        <!-- Partnering Program -->
                        <div class="flex items-center space-x-4 bg-[#db2777]/5 p-5 rounded-2xl border border-rose-100/50">
                            <img src="{{ asset('images/logo-loquesientes.png') }}" alt="Lo que sientes importa" class="h-12 w-auto object-contain" />
                            <div>
                                <span class="block text-[10px] font-bold text-[#db2777] uppercase tracking-wider">Línea Estatal Oficial</span>
                                <span class="text-sm font-bold text-slate-800">Acompañamiento Psicológico 24/7</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 20 Criterios Section (Editorial design) -->
        <section id="criterios" class="py-24 bg-white border-t border-b border-stone-200/40 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    
                    <!-- Left: Huge Typography Editorial -->
                    <div class="lg:col-span-5 space-y-6 text-left">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#db2777]">El Núcleo del Distintivo</span>
                        <h2 class="text-6xl sm:text-7xl font-extrabold tracking-tight text-slate-900 leading-none">
                            20 <span class="block text-3xl font-light text-slate-500 mt-2">Criterios de Evaluación</span>
                        </h2>
                        <p class="text-slate-600 text-sm leading-relaxed max-w-sm">
                            El distintivo evalúa la estructura preventiva en base a 20 reactivos específicos distribuidos en tres grandes áreas de salud laboral.
                        </p>
                    </div>

                    <!-- Right: Large interactive circles showing the breakdown -->
                    <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-3 gap-8">
                        
                        <!-- Sphere 1: Prevención -->
                        <div class="bg-stone-50 p-8 rounded-3xl border border-stone-200/60 flex flex-col items-center text-center space-y-4 transition-transform hover:-translate-y-1 duration-300">
                            <div class="w-20 h-20 bg-[#db2777] rounded-full flex items-center justify-center text-white font-extrabold text-3xl shadow-md">6</div>
                            <h3 class="font-bold text-slate-900 tracking-tight">Prevención</h3>
                            <p class="text-xs text-slate-500">Acciones básicas para evitar riesgos psicosociales y fatiga extrema.</p>
                        </div>
                        
                        <!-- Sphere 2: Cuidado -->
                        <div class="bg-stone-50 p-8 rounded-3xl border border-stone-200/60 flex flex-col items-center text-center space-y-4 transition-transform hover:-translate-y-1 duration-300">
                            <div class="w-20 h-20 bg-[#ca8a04] rounded-full flex items-center justify-center text-white font-extrabold text-3xl shadow-md">6</div>
                            <h3 class="font-bold text-slate-900 tracking-tight">Cuidado</h3>
                            <p class="text-xs text-slate-500">Monitoreo activo y detección oportuna de síntomas y crisis.</p>
                        </div>
                        
                        <!-- Sphere 3: Fortalecimiento -->
                        <div class="bg-stone-50 p-8 rounded-3xl border border-stone-200/60 flex flex-col items-center text-center space-y-4 transition-transform hover:-translate-y-1 duration-300">
                            <div class="w-20 h-20 bg-[#749d36] rounded-full flex items-center justify-center text-white font-extrabold text-3xl shadow-md">8</div>
                            <h3 class="font-bold text-slate-900 tracking-tight">Fortalecimiento</h3>
                            <p class="text-xs text-slate-500">Talleres, integración familiar y promoción activa de salud.</p>
                        </div>

                    </div>
                    
                </div>

            </div>
        </section>

        <!-- Beneficios B2B (WCAG compliant) -->
        <section id="beneficios" class="py-24 bg-stone-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    
                    <!-- Left: List of B2B benefits -->
                    <div class="lg:col-span-8 space-y-8">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#749d36]">El Retorno del Bienestar</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                            Beneficios Corporativos y ROI
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            
                            <!-- Benefit 1 -->
                            <div class="flex items-start space-x-3">
                                <div class="w-5 h-5 rounded-full bg-[#749d36] text-white flex items-center justify-center mt-1 flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-800 text-sm">Distintivo Oficial de Gobierno</h4>
                                    <p class="text-xs text-slate-500">Uso del sello oficial en tus comunicados y campañas de responsabilidad social.</p>
                                </div>
                            </div>
                            
                            <!-- Benefit 2 -->
                            <div class="flex items-start space-x-3">
                                <div class="w-5 h-5 rounded-full bg-[#749d36] text-white flex items-center justify-center mt-1 flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-800 text-sm">Cumplimiento NOM-035</h4>
                                    <p class="text-xs text-slate-500">Herramientas técnicas que facilitan la alineación con las inspecciones federales de trabajo.</p>
                                </div>
                            </div>
                            
                            <!-- Benefit 3 -->
                            <div class="flex items-start space-x-3">
                                <div class="w-5 h-5 rounded-full bg-[#749d36] text-white flex items-center justify-center mt-1 flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-800 text-sm">Reducción de Rotación</h4>
                                    <p class="text-xs text-slate-500">Disminución sustancial en los costos asociados a reclutamiento y despidos.</p>
                                </div>
                            </div>
                            
                            <!-- Benefit 4 -->
                            <div class="flex items-start space-x-3">
                                <div class="w-5 h-5 rounded-full bg-[#749d36] text-white flex items-center justify-center mt-1 flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-800 text-sm">Incentivos Estatales</h4>
                                    <p class="text-xs text-slate-500">Acceso a beneficios y convocatorias exclusivas de la Secretaría de Economía y Salud.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <!-- Right: Large Insignia Render Box -->
                    <div class="lg:col-span-4 flex justify-center lg:justify-end">
                        <div class="relative bg-white p-10 rounded-3xl border border-stone-200 shadow-xl flex items-center justify-center">
                            <!-- Background radial glow -->
                            <div class="absolute inset-0 bg-[#749d36]/5 rounded-3xl"></div>
                            
                            <!-- Badge icon inside container -->
                            <svg class="w-36 h-36 text-[#749d36] relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Registration Section (Frictionless embed) -->
        <section id="registro" class="py-24 bg-white border-t border-stone-200/40 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                
                <div class="max-w-3xl mx-auto text-center space-y-4 mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#db2777]">Postulación Digital</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                        Registra tu Empresa o Institución
                    </h2>
                    <p class="text-slate-600 font-light text-sm max-w-lg mx-auto">
                        Ingresa los datos del centro de trabajo para recibir tu folio único de seguimiento y comenzar tu autoevaluación.
                    </p>
                </div>

                <!-- Embed the Livewire/Volt Component -->
                <div class="relative z-10">
                    <livewire:registro-empresa-form />
                </div>

            </div>
        </section>

    </main>

    <!-- Footer / Institutional backing -->
    <footer class="bg-slate-950 text-slate-400 py-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-12">
            
            <!-- Logo Row (Footer Support) -->
            <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12 pb-8 border-b border-slate-800">
                <img src="{{ asset('images/pasos.png') }}" alt="Coahuila: A pasos de gigante" class="h-10 w-auto object-contain opacity-60 hover:opacity-100 transition-opacity duration-200" />
                <span class="hidden md:inline h-6 w-px bg-slate-800"></span>
                <img src="{{ asset('images/salud-mental.png') }}" alt="Secretaría de Salud" class="h-10 w-auto object-contain opacity-60 hover:opacity-100 transition-opacity duration-200" />
            </div>

            <!-- Credits and Details -->
            <div class="flex flex-col md:flex-row items-center justify-between text-xs space-y-4 md:space-y-0 text-slate-500">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-slate-400">+Feliz Coahuila</span>
                    <span>&copy; 2026. Todos los derechos reservados.</span>
                </div>
                <p class="max-w-md text-center md:text-right leading-relaxed">
                    Iniciativa impulsada por la Oficina Inspira Coahuila y la Secretaría de Salud del Estado para promover entornos psicosociales protectores y saludables en los centros de trabajo.
                </p>
            </div>
            
        </div>
    </footer>

</body>
</html>
