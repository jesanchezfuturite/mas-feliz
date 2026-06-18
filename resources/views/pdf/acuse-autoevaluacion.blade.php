<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse de Autoevaluación - +Feliz</title>
    <style>
        @page {
            margin: 110px 50px 70px 50px;
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
            top: -90px;
            left: 0;
            right: 0;
            height: 60px;
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
        $sum = 0;
        for ($i = 1; $i <= 25; $i++) {
            $val = $autoevaluacion->{"criterio_{$i}"};
            if (is_numeric($val)) {
                $sum += (int) $val;
            }
        }
        
        if ($sum >= 200) {
            $level = 'Nivel 3: Sobresaliente';
            $levelDesc = 'La organización demuestra un alto compromiso y una gestión madura y proactiva de la salud mental y bienestar de sus colaboradores.';
            $maturityColor = '#065f46';
            $maturityBg = '#d1fae5';
        } elseif ($sum >= 100) {
            $level = 'Nivel 2: En proceso';
            $levelDesc = 'La organización cuenta con bases de prevención y salud, y se encuentra en camino a consolidar un entorno organizacional favorable.';
            $maturityColor = '#92400e';
            $maturityBg = '#fef3c7';
        } else {
            $level = 'Nivel 1: Inicial';
            $levelDesc = 'La organización cuenta con oportunidades significativas para diseñar e integrar políticas de prevención y cuidado emocional.';
            $maturityColor = '#991b1b';
            $maturityBg = '#fee2e2';
        }

        $optionsText = [
            '10' => 'Sí',
            '5' => 'En proceso',
            '0' => 'No',
            'NA' => 'No aplica',
        ];

        $optionsBadgeClass = [
            '10' => 'badge-si',
            '5' => 'badge-proceso',
            '0' => 'badge-no',
            'NA' => 'badge-na',
        ];
    @endphp

    <!-- Fixed Header -->
    <div class="header">
        <table class="logo-table">
            <tr>
                <td style="text-align: left; width: 33%;">
                    @if(file_exists(public_path('assets/branding/logo-gobierno.svg')))
                        <img src="{{ public_path('assets/branding/logo-gobierno.svg') }}" style="height: 38px;" alt="Logo Gobierno" />
                    @else
                        <span style="font-weight: bold; color: #1e293b;">GOBIERNO</span>
                    @endif
                </td>
                <td style="text-align: center; width: 34%;">
                    @if(file_exists(public_path('assets/branding/logo-mas-feliz.svg')))
                        <img src="{{ public_path('assets/branding/logo-mas-feliz.svg') }}" style="height: 38px;" alt="Logo Más Feliz" />
                    @else
                        <span style="font-weight: bold; color: #0f766e; font-size: 16px;">+Feliz</span>
                    @endif
                </td>
                <td style="text-align: right; width: 33%;">
                    @if(file_exists(public_path('assets/branding/logo-inspira.svg')))
                        <img src="{{ public_path('assets/branding/logo-inspira.svg') }}" style="height: 38px;" alt="Logo Inspira" />
                    @else
                        <span style="font-weight: bold; color: #4f46e5;">Inspira</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Fixed Footer -->
    <div class="footer">
        Programa Estatal de Salud Mental y Entorno Psicosocial +Feliz &copy; 2026. Todos los derechos reservados.<br>
        Documento oficial digital generado de manera automática en el Portal de Autoevaluación.
    </div>

    <!-- Document Title -->
    <div class="title-container">
        <h1>ACUSE OFICIAL DE AUTOEVALUACIÓN</h1>
        <p>Distintivo de Salud Mental y Bienestar Organizacional</p>
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
                <!-- Enfoque 1 -->
                <tr>
                    <td colspan="4" class="enfoque-header">Enfoque 1. Liderazgo, Política y Prevención de Riesgos (+Prevención)</td>
                </tr>
                @for ($i = 1; $i <= 6; $i++)
                    @php $val = $autoevaluacion->{"criterio_{$i}"}; @endphp
                    <tr>
                        <td>Criterio {{ $i }}</td>
                        <td>Evaluación de políticas y medidas preventivas del ámbito organizacional.</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$val] ?? 'badge-na' }}">
                                {{ $optionsText[$val] ?? $val }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ is_numeric($val) ? $val : '-' }}</td>
                    </tr>
                @endfor

                <!-- Enfoque 2 -->
                <tr>
                    <td colspan="4" class="enfoque-header">Enfoque 2. Cuidado, Salud Emocional y Promoción del Bienestar (+Salud)</td>
                </tr>
                @for ($i = 7; $i <= 12; $i++)
                    @php $val = $autoevaluacion->{"criterio_{$i}"}; @endphp
                    <tr>
                        <td>Criterio {{ $i }}</td>
                        <td>Acciones de contención y fomento a la salud mental dentro de la organización.</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$val] ?? 'badge-na' }}">
                                {{ $optionsText[$val] ?? $val }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ is_numeric($val) ? $val : '-' }}</td>
                    </tr>
                @endfor

                <!-- Page break to keep the PDF structured nicely -->
                <tr style="border: none;"><td colspan="4" style="border: none; padding: 0;"><div class="page-break"></div></td></tr>

                <!-- Enfoque 3 -->
                <tr>
                    <td colspan="4" class="enfoque-header" style="border-top: none;">Enfoque 3. Desarrollo Humano, Formación y Comunicación (+Desarrollo)</td>
                </tr>
                @for ($i = 13; $i <= 18; $i++)
                    @php $val = $autoevaluacion->{"criterio_{$i}"}; @endphp
                    <tr>
                        <td>Criterio {{ $i }}</td>
                        <td>Capacitaciones y canales de retroalimentación para el desarrollo del personal.</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$val] ?? 'badge-na' }}">
                                {{ $optionsText[$val] ?? $val }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ is_numeric($val) ? $val : '-' }}</td>
                    </tr>
                @endfor

                <!-- Enfoque 4 -->
                <tr>
                    <td colspan="4" class="enfoque-header">Enfoque 4. Condiciones de trabajo, bienestar y entorno psicosocial (+Bienestar)</td>
                </tr>
                @for ($i = 19; $i <= 25; $i++)
                    @php $val = $autoevaluacion->{"criterio_{$i}"}; @endphp
                    <tr>
                        <td>Criterio {{ $i }}</td>
                        <td>Entorno organizacional favorable y balance entre vida laboral y familiar.</td>
                        <td style="text-align: center;">
                            <span class="badge {{ $optionsBadgeClass[$val] ?? 'badge-na' }}">
                                {{ $optionsText[$val] ?? $val }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ is_numeric($val) ? $val : '-' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-card">
            <div class="summary-title">RESULTADO DE EVALUACIÓN</div>
            <div class="score-display" style="color: {{ $maturityColor }};">{{ $sum }} Puntos</div>
            <div class="maturity-badge" style="background-color: {{ $maturityBg }}; color: {{ $maturityColor }};">
                {{ $level }}
            </div>
            <p style="text-align: center; margin: 15px 20px 0 20px; font-size: 10px; color: #475569; line-height: 1.4;">
                {{ $levelDesc }}
            </p>
        </div>
    </div>

</body>
</html>
