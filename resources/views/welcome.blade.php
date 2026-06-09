<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col antialiased">

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">+Feliz</span>
                <span class="h-6 w-px bg-slate-200 dark:bg-slate-800"></span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:inline-block">Programa Estatal</span>
            </div>
            <nav class="flex items-center space-x-6">
                <a href="#ambitos" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Ámbitos</a>
                <a href="#registro" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Registro</a>
                <a href="/admin" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm hover:shadow transition-all duration-150">
                    Acceso Portal
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-b from-blue-50/50 via-white to-slate-50 dark:from-slate-900/20 dark:via-slate-950 dark:to-slate-950 pt-20 pb-16 sm:pt-28 sm:pb-20 overflow-hidden">
            <!-- Decorative Background Gradients -->
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-400/20 dark:bg-blue-600/10 rounded-full blur-3xl"></div>
            <div class="absolute top-60 -left-20 w-80 h-80 bg-indigo-400/20 dark:bg-indigo-600/10 rounded-full blur-3xl"></div>
            
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">
                <div class="inline-flex items-center space-x-2 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider">
                    <span>Distintivo Gubernamental Oficial</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                    Iniciativa <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent font-black">+Feliz</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto font-light leading-relaxed">
                    Un reconocimiento oficial para visibilizar y galardonar las acciones sobresalientes en la <strong class="font-semibold text-slate-800 dark:text-slate-200">prevención, cuidado y fortalecimiento de la Salud Mental</strong> dentro de nuestra comunidad.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="#registro" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-150 transform hover:-translate-y-0.5 text-center">
                        Registrar mi Institución
                    </a>
                    <a href="#ambitos" class="w-full sm:w-auto px-8 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 font-semibold rounded-xl transition-all duration-150 text-center">
                        Conoce los Ámbitos
                    </a>
                </div>
            </div>
        </section>

        <!-- Spheres (Ámbitos) Section -->
        <section id="ambitos" class="py-20 bg-white dark:bg-slate-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Ámbitos de Reconocimiento
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400">
                        El distintivo +Feliz premia proyectos y planes en diferentes sectores de la sociedad. Encuentra la categoría adecuada para tu organización.
                    </p>
                </div>

                <!-- Grid layout of cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Public Area -->
                    <div class="group p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800 dark:text-slate-200">Ámbito Público</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Organismos descentralizados, dependencias gubernamentales y áreas municipales que implementen políticas de bienestar psicosocial.
                        </p>
                    </div>

                    <!-- Educational Area -->
                    <div class="group p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 hover:border-indigo-500 dark:hover:border-indigo-400 hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800 dark:text-slate-200">Ámbito Educativo</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Escuelas, colegios, institutos y universidades enfocados en guiar, formar y asegurar la salud emocional de estudiantes y docentes.
                        </p>
                    </div>

                    <!-- Industrial/Productive Area -->
                    <div class="group p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 hover:border-violet-500 dark:hover:border-violet-400 hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-violet-100 dark:bg-violet-900/50 text-violet-600 dark:text-violet-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800 dark:text-slate-200">Ámbito Productivo o Industrial</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Empresas comerciales, fábricas y corporativos que cuenten con programas preventivos contra el estrés y fatiga laboral.
                        </p>
                    </div>

                    <!-- Social/Community Area -->
                    <div class="group p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 hover:border-fuchsia-500 dark:hover:border-fuchsia-400 hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-600 dark:text-fuchsia-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800 dark:text-slate-200">Ámbito Social y Comunitario</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Organizaciones no gubernamentales, colectivos de asistencia y centros deportivos que fomenten el tejido social y la resiliencia.
                        </p>
                    </div>

                    <!-- Others Area -->
                    <div class="group p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 hover:border-emerald-500 dark:hover:border-emerald-400 hover:shadow-xl transition-all duration-300">
                        <div class="h-12 w-12 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-slate-800 dark:text-slate-200">Otros Ámbitos</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Cualquier otra iniciativa fuera de las categorías anteriores que trabaje activamente por mejorar el ecosistema emocional de las personas.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Registration Section (Livewire Form embedded) -->
        <section id="registro" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto space-y-4 mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Registra tu Institución o Empresa
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">
                        Completa el formulario oficial para inscribir tu centro de trabajo y postularte al distintivo estatal.
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
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/80 py-12 text-center text-slate-500 dark:text-slate-400 text-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-center space-x-2">
                <span class="font-bold text-slate-700 dark:text-slate-300">+Feliz</span>
                <span>&copy; 2026</span>
            </div>
            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-md mx-auto">
                Esta es una iniciativa de alta prioridad gubernamental para reconocer las mejores prácticas organizacionales e institucionales en el cuidado de la salud mental de los trabajadores.
            </p>
        </div>
    </footer>

</body>
</html>
