<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Preliminar de Autoevaluación - +Feliz</title>
    <style>
        @page {
            margin: 150px 50px 70px 50px;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
        }

        /* Fixed header and footer */
        .header {
            position: fixed;
            top: -130px;
            left: 0;
            right: 0;
            height: 100px;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
        }
        
        .footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
            text-align: center;
            padding-top: 10px;
        }

        .logo-container {
            width: 100%;
        }

        .logo-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .title-container {
            margin-top: 15px;
            text-align: center;
        }

        .title-container h1 {
            font-size: 18px;
            margin: 0;
            color: #0f766e;
            font-weight: bold;
        }

        .title-container p {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Info boxes */
        .info-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .info-table td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #475569;
            width: 30%;
        }

        .info-value {
            color: #0f172a;
        }

        /* Results table */
        .results-section {
            margin-top: 25px;
        }

        .results-section h2 {
            font-size: 12px;
            color: #0f766e;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #0f766e;
            padding-bottom: 4px;
        }

        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .criteria-table th {
            background-color: #0f766e;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 10px;
            font-size: 10px;
        }

        .criteria-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }

        .enfoque-header {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
            padding: 6px 10px;
            font-size: 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .badge-si {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-proceso {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-no {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-na {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* Summary section */
        .summary-box {
            margin-top: 25px;
            border-collapse: collapse;
            width: 100%;
            page-break-inside: avoid;
        }

        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 15px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 10px;
            text-align: center;
        }

        .score-display {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            margin: 10px 0;
        }

        .maturity-badge {
            display: block;
            margin: 0 auto;
            width: 200px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @php
        $respuestas = $autoevaluacion->respuestas ?? [];
        $sum = 0;
        if (is_array($respuestas)) {
            foreach ($respuestas as $criterioKey => $criterio) {
                if (is_array($criterio)) {
                    foreach ($criterio as $key => $elemento) {
                        if ($key === 'status' || $key === 'feedback') {
                            continue;
                        }
                        if (is_array($elemento) && isset($elemento['score']) && is_numeric($elemento['score'])) {
                            $sum += (int) $elemento['score'];
                        }
                    }
                }
            }
        }

        // Check Indispensable criteria
        $indispensableIds = [4, 9, 10, 15, 16];
        $indispensablesCount = 0;
        $criterioElementsCount = [
            4 => 3,
            9 => 5,
            10 => 5,
            15 => 3,
            16 => 5,
        ];

        foreach ($indispensableIds as $id) {
            $met = true;
            $numElements = $criterioElementsCount[$id];
            for ($e = 1; $e <= $numElements; $e++) {
                $score = $respuestas["criterio_{$id}"]["elemento_{$e}"]['score'] ?? null;
                if ($score !== '10' && $score !== 'NA') {
                    $met = false;
                    break;
                }
            }
            if ($met) {
                $indispensablesCount++;
            }
        }
        $cumpleIndispensables = ($indispensablesCount === 5);

        if ($cumpleIndispensables) {
            if ($sum >= 180) {
                $level = 'Nivel 3: Excelencia';
                $levelDesc = 'La organización demuestra un alto compromiso y una gestión madura y proactiva de la salud mental y bienestar de sus colaboradores.';
                $maturityColor = '#059669'; // Green
                $maturityBg = '#ecfdf5';
            } elseif ($sum >= 100) {
                $level = 'Nivel 2: Avanzado';
                $levelDesc = 'La organización cuenta con bases de prevención y salud, y se encuentra en camino a consolidar un entorno organizacional favorable.';
                $maturityColor = '#d97706'; // Warning
                $maturityBg = '#fffbeb';
            } else {
                $level = 'Nivel 1: Inicial';
                $levelDesc = 'La organización cuenta con oportunidades significativas para diseñar e integrar políticas de prevención y cuidado emocional.';
                $maturityColor = '#dc2626'; // Danger
                $maturityBg = '#fef2f2';
            }
        } else {
            $level = 'Nivel 1: Inicial';
            $levelDesc = 'La organización cuenta con oportunidades significativas para diseñar e integrar políticas de prevención y cuidado emocional (requiere cumplir todos los indispensables).';
            $maturityColor = '#dc2626'; // Danger
            $maturityBg = '#fef2f2';
        }

        // Define criteria titles & Ejes
        $criteriosData = [
            // Eje 1: Fortalecimiento
            1 => ['title' => 'Criterio 1', 'desc' => 'Existencia de una política/declaración interna para promover la Salud Mental en la organización', 'eje' => 'Fortalecimiento'],
            2 => ['title' => 'Criterio 2', 'desc' => 'La organización conforma un Comité de Salud Mental', 'eje' => 'Fortalecimiento'],
            4 => ['title' => 'Criterio 4', 'desc' => 'Programa de salud mental basado en el diagnóstico de riesgos', 'eje' => 'Fortalecimiento'],
            9 => ['title' => 'Criterio 9', 'desc' => 'Estrategia de promoción de la salud mental', 'eje' => 'Fortalecimiento'],
            10 => ['title' => 'Criterio 10', 'desc' => 'Capacitación a directivos y líderes en señales de alerta', 'eje' => 'Fortalecimiento'],
            11 => ['title' => 'Criterio 11', 'desc' => 'Acciones de capacitación en alfabetización y concientización', 'eje' => 'Fortalecimiento'],
            13 => ['title' => 'Criterio 13', 'desc' => 'Espacios y actividades orientadas al desarrollo de habilidades socioemocionales', 'eje' => 'Fortalecimiento'],
            16 => ['title' => 'Criterio 16', 'desc' => 'Protocolo integral de actuación ante crisis de salud mental', 'eje' => 'Fortalecimiento'],
            20 => ['title' => 'Criterio 20', 'desc' => 'Sistema de gestión de la salud mental integrado', 'eje' => 'Fortalecimiento'],

            // Eje 2: Prevención
            3 => ['title' => 'Criterio 3', 'desc' => 'Plan de prevención, cuidado y fortalecimiento de la Salud Mental', 'eje' => 'Prevención'],
            5 => ['title' => 'Criterio 5', 'desc' => 'Programa de atención de los riesgos psicosociales', 'eje' => 'Prevención'],
            8 => ['title' => 'Criterio 8', 'desc' => 'Estrategia para promover entornos saludables', 'eje' => 'Prevención'],
            17 => ['title' => 'Criterio 17', 'desc' => 'Prueba anual del Protocolo integral ante crisis', 'eje' => 'Prevención'],
            18 => ['title' => 'Criterio 18', 'desc' => 'Estrategia formal de reconocimiento a los trabajadores', 'eje' => 'Prevención'],

            // Eje 3: Cuidado y Atención
            6 => ['title' => 'Criterio 6', 'desc' => 'Evaluación anual del clima laboral y la cultura organizacional', 'eje' => 'Cuidado y Atención'],
            7 => ['title' => 'Criterio 7', 'desc' => 'Estrategias para promover condiciones básicas del ambiente físico', 'eje' => 'Cuidado y Atención'],
            12 => ['title' => 'Criterio 12', 'desc' => 'Capacitación a “gatekeepers” o líderes entrenados ante crisis', 'eje' => 'Cuidado y Atención'],
            14 => ['title' => 'Criterio 14', 'desc' => 'Mecanismos permanentes de difusión sobre servicios de apoyo', 'eje' => 'Cuidado y Atención'],
            15 => ['title' => 'Criterio 15', 'desc' => 'Acceso oportuno y confidencial a servicios de apoyo psicológico', 'eje' => 'Cuidado y Atención'],
            19 => ['title' => 'Criterio 19', 'desc' => 'Acciones de fortalecimiento de competencias parentales y apoyo familiar', 'eje' => 'Cuidado y Atención'],
        ];

        $optionsText = [
            'Cumple' => 'Cumple',
            'En proceso' => 'En proceso',
            'No cumple' => 'No cumple',
            'No aplica' => 'No aplica',
        ];

        $optionsBadgeClass = [
            'Cumple' => 'badge-si',
            'En proceso' => 'badge-proceso',
            'No cumple' => 'badge-no',
            'No aplica' => 'badge-na',
        ];
    @endphp

    <!-- Fixed Header -->
    <div class="header">
        <table class="logo-table">
            <tr>
                <td style="text-align: left; width: 33%; vertical-align: middle;">
                    @if(file_exists(public_path('images/coahuila.png')))
                        <img src="{{ public_path('images/coahuila.png') }}" style="height: 90px; vertical-align: middle; display: inline-block;" alt="Logo Coahuila" />
                    @else
                        <span style="font-weight: bold; color: #1e293b; vertical-align: middle;">COAHUILA</span>
                    @endif
                </td>
                <td style="text-align: center; width: 34%; vertical-align: middle;">
                    @if(file_exists(public_path('images/+FELIZ-LOGO.png')))
                        <img src="{{ public_path('images/+FELIZ-LOGO.png') }}" style="height: 90px; vertical-align: middle; display: inline-block;" alt="Logo +Feliz" />
                    @else
                        <span style="font-weight: bold; color: #0f766e; font-size: 16px; vertical-align: middle;">+Feliz</span>
                    @endif
                </td>
                <td style="text-align: right; width: 33%; vertical-align: middle;">
                    @if(file_exists(public_path('images/inspira.png')))
                        <img src="{{ public_path('images/inspira.png') }}" style="height: 90px; vertical-align: middle; display: inline-block;" alt="Logo Inspira" />
                    @else
                        <span style="font-weight: bold; color: #4f46e5; vertical-align: middle;">Inspira</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Fixed Footer -->
    <div class="footer">
        Distintivo +FELIZ &copy; 2026. Todos los derechos reservados.<br>
        Documento oficial digital generado de manera automática en el Portal de Autoevaluación.
    </div>

    <!-- Document Title -->
    <div class="title-container">
        <h1>REPORTE PRELIMINAR DE AUTOEVALUACIÓN</h1>
    </div>

    <!-- Company Details -->
    <table class="info-table">
        <tr>
            <td class="info-label">Folio de Registro</td>
            <td class="info-value" style="font-weight: bold; color: #0f766e;">{{ $autoevaluacion->empresa->folio }}</td>
            <td class="info-label">Fecha de Evaluación</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($autoevaluacion->fecha_evaluacion)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="info-label">Razón Social / Organización</td>
            <td class="info-value" colspan="3" style="font-weight: bold;">{{ $autoevaluacion->empresa->nombre_empresa }}</td>
        </tr>
        <tr>
            <td class="info-label">Municipio</td>
            <td class="info-value">{{ $autoevaluacion->empresa->municipio }}</td>
            <td class="info-label">Giro o Rubro</td>
            <td class="info-value">{{ $autoevaluacion->empresa->rubro }}</td>
        </tr>
        <tr>
            <td class="info-label">Número de Trabajadores</td>
            <td class="info-value">{{ $autoevaluacion->empresa->numero_trabajadores }}</td>
            <td class="info-label">Responsable del Registro</td>
            <td class="info-value">{{ $autoevaluacion->empresa->nombre_responsable }}</td>
        </tr>
    </table>

    <!-- Results Section -->
    <div class="results-section">
        <h2>Detalle de Respuestas por Enfoque</h2>
        
        <table class="criteria-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Criterio</th>
                    <th style="width: 55%;">Descripción del Criterio</th>
                    <th style="width: 18%; text-align: center;">Respuesta</th>
                    <th style="width: 12%; text-align: center;">Puntaje</th>
                </tr>
            </thead>
            <tbody>
                <!-- Eje 1. Fortalecimiento -->
                <tr>
                    <td colspan="4" class="enfoque-header">Eje 1. Fortalecimiento (+Fortalecimiento)</td>
                </tr>
                @foreach([1, 2, 4, 9, 10, 11, 13, 16, 20] as $i)
                    @php
                        $data = $criteriosData[$i];
                        $criterioScore = 0;
                        $hasAnsweredAny = false;
                        $allNA = true;
                        $elements = $respuestas["criterio_{$i}"] ?? [];
                        if (is_array($elements)) {
                            foreach ($elements as $key => $elemento) {
                                if (str_starts_with($key, 'elemento_') && is_array($elemento) && isset($elemento['score'])) {
                                    $hasAnsweredAny = true;
                                    if ($elemento['score'] !== 'NA') {
                                        $allNA = false;
                                        $criterioScore += (int)$elemento['score'];
                                    }
                                }
                            }
                        }

                        if (!$hasAnsweredAny) {
                            $status = 'No cumple';
                        } elseif ($allNA) {
                            $status = 'No aplica';
                        } else {
                            $totalElements = 0;
                            $metElements = 0;
                            foreach ($elements as $key => $elemento) {
                                if (str_starts_with($key, 'elemento_') && is_array($elemento) && isset($elemento['score'])) {
                                    if ($elemento['score'] !== 'NA') {
                                        $totalElements++;
                                        if ($elemento['score'] === '10') {
                                            $metElements++;
                                        }
                                    }
                                }
                            }
                            if ($totalElements === 0) {
                                $status = 'No aplica';
                            } elseif ($metElements === $totalElements) {
                                $status = 'Cumple';
                            } elseif ($metElements === 0) {
                                $status = 'No cumple';
                            } else {
                                $status = 'En proceso';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $data['title'] }}</td>
                        <td>{{ $data['desc'] }}</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$status] ?? 'badge-na' }}">
                                {{ $optionsText[$status] ?? $status }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $criterioScore }}</td>
                    </tr>
                @endforeach

                <!-- Page break to keep the PDF structured nicely -->
                <tr style="border: none;"><td colspan="4" style="border: none; padding: 0;"><div class="page-break"></div></td></tr>

                <!-- Eje 2. Prevención -->
                <tr>
                    <td colspan="4" class="enfoque-header" style="border-top: none;">Eje 2. Prevención (+Prevención)</td>
                </tr>
                @foreach([3, 5, 8, 17, 18] as $i)
                    @php
                        $data = $criteriosData[$i];
                        $criterioScore = 0;
                        $hasAnsweredAny = false;
                        $allNA = true;
                        $elements = $respuestas["criterio_{$i}"] ?? [];
                        if (is_array($elements)) {
                            foreach ($elements as $key => $elemento) {
                                if (str_starts_with($key, 'elemento_') && is_array($elemento) && isset($elemento['score'])) {
                                    $hasAnsweredAny = true;
                                    if ($elemento['score'] !== 'NA') {
                                        $allNA = false;
                                        $criterioScore += (int)$elemento['score'];
                                    }
                                }
                            }
                        }

                        if (!$hasAnsweredAny) {
                            $status = 'No cumple';
                        } elseif ($allNA) {
                            $status = 'No aplica';
                        } else {
                            $totalElements = 0;
                            $metElements = 0;
                            foreach ($elements as $key => $elemento) {
                                if (str_starts_with($key, 'elemento_') && is_array($elemento) && isset($elemento['score'])) {
                                    if ($elemento['score'] !== 'NA') {
                                        $totalElements++;
                                        if ($elemento['score'] === '10') {
                                            $metElements++;
                                        }
                                    }
                                }
                            }
                            if ($totalElements === 0) {
                                $status = 'No aplica';
                            } elseif ($metElements === $totalElements) {
                                $status = 'Cumple';
                            } elseif ($metElements === 0) {
                                $status = 'No cumple';
                            } else {
                                $status = 'En proceso';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $data['title'] }}</td>
                        <td>{{ $data['desc'] }}</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$status] ?? 'badge-na' }}">
                                {{ $optionsText[$status] ?? $status }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $criterioScore }}</td>
                    </tr>
                @endforeach

                <!-- Eje 3. Cuidado y Atención -->
                <tr>
                    <td colspan="4" class="enfoque-header">Eje 3. Cuidado y Atención (+Cuidado)</td>
                </tr>
                @foreach([6, 7, 12, 14, 15, 19] as $i)
                    @php
                        $data = $criteriosData[$i];
                        $criterioScore = 0;
                        $hasAnsweredAny = false;
                        $allNA = true;
                        $elements = $respuestas["criterio_{$i}"] ?? [];
                        if (is_array($elements)) {
                            foreach ($elements as $key => $elemento) {
                                if (str_starts_with($key, 'elemento_') && is_array($elemento) && isset($elemento['score'])) {
                                    $hasAnsweredAny = true;
                                    if ($elemento['score'] !== 'NA') {
                                        $allNA = false;
                                        $criterioScore += (int)$elemento['score'];
                                    }
                                }
                            }
                        }

                        if (!$hasAnsweredAny) {
                            $status = 'No cumple';
                        } elseif ($allNA) {
                            $status = 'No aplica';
                        } else {
                            $totalElements = 0;
                            $metElements = 0;
                            foreach ($elements as $key => $elemento) {
                                if (str_starts_with($key, 'elemento_') && is_array($elemento) && isset($elemento['score'])) {
                                    if ($elemento['score'] !== 'NA') {
                                        $totalElements++;
                                        if ($elemento['score'] === '10') {
                                            $metElements++;
                                        }
                                    }
                                }
                            }
                            if ($totalElements === 0) {
                                $status = 'No aplica';
                            } elseif ($metElements === $totalElements) {
                                $status = 'Cumple';
                            } elseif ($metElements === 0) {
                                $status = 'No cumple';
                            } else {
                                $status = 'En proceso';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $data['title'] }}</td>
                        <td>{{ $data['desc'] }}</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$status] ?? 'badge-na' }}">
                                {{ $optionsText[$status] ?? $status }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $criterioScore }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</body>
</html>
