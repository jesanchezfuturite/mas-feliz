<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado Distintivo +Feliz - {{ $autoevaluacion->empresa->nombre_empresa }}</title>
    <style>
        @page {
            margin: 0;
            size: letter landscape;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .border-container {
            border: 15px solid #1e3a8a; /* Deep blue border */
            height: 100%;
            box-sizing: border-box;
            padding: 30px;
            position: relative;
        }

        .inner-border {
            border: 2px solid #3b82f6; /* Light blue accent border */
            height: 100%;
            box-sizing: border-box;
            padding: 40px;
            text-align: center;
        }

        .logos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .logos-table td {
            border: none;
            vertical-align: middle;
        }

        .title-pre {
            font-size: 14px;
            color: #4b5563;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .title-main {
            font-size: 32px;
            color: #1e3a8a;
            font-weight: 800;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
        }

        .title-sub {
            font-size: 16px;
            color: #0f766e;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        .award-text {
            font-size: 16px;
            font-style: italic;
            color: #4b5563;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 26px;
            color: #1e293b;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .recognition-text {
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            max-width: 700px;
            margin: 0 auto 20px auto;
        }

        /* Maturity badge */
        .maturity-container {
            margin: 25px auto;
            width: 320px;
        }

        .maturity-badge {
            font-size: 15px;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .badge-excelencia {
            background-color: #ecfdf5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }

        .badge-avanzado {
            background-color: #fffbeb;
            color: #92400e;
            border: 2px solid #fde68a;
        }

        .badge-inicial {
            background-color: #fef2f2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        .dictamen-box {
            font-size: 12px;
            color: #4b5563;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin: 20px auto;
            max-width: 750px;
            text-align: left;
            line-height: 1.5;
        }

        .dictamen-title {
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        .signatures-table td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            width: 250px;
            border-bottom: 1px solid #94a3b8;
            margin: 0 auto 8px auto;
        }

        .signature-name {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        .signature-title {
            font-size: 9px;
            color: #64748b;
        }

        .footer-info {
            position: absolute;
            bottom: 45px;
            left: 70px;
            right: 70px;
            display: table;
            width: calc(100% - 140px);
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    @php
        // Calculate maturity level to style the badge
        $nivel = $nivelMadurez ?? 'Inicial';
        $badgeClass = match($nivel) {
            'Excelencia' => 'badge-excelencia',
            'Avanzado' => 'badge-avanzado',
            default => 'badge-inicial',
        };
    @endphp

    <div class="border-container">
        <div class="inner-border">
            
            <!-- Logos -->
            <table class="logos-table">
                <tr>
                    <td style="text-align: left; width: 33%;">
                        @if(file_exists(public_path('assets/branding/logo-gobierno.svg')))
                            <img src="{{ public_path('assets/branding/logo-gobierno.svg') }}" style="height: 45px;" />
                        @else
                            <span style="font-weight: bold; color: #1e293b; font-size: 16px;">GOBIERNO</span>
                        @endif
                    </td>
                    <td style="text-align: center; width: 34%;">
                        @if(file_exists(public_path('assets/branding/logo-mas-feliz.svg')))
                            <img src="{{ public_path('assets/branding/logo-mas-feliz.svg') }}" style="height: 50px;" />
                        @else
                            <span style="font-weight: bold; color: #1e3a8a; font-size: 24px;">+Feliz</span>
                        @endif
                    </td>
                    <td style="text-align: right; width: 33%;">
                        @if(file_exists(public_path('assets/branding/logo-inspira.svg')))
                            <img src="{{ public_path('assets/branding/logo-inspira.svg') }}" style="height: 45px;" />
                        @else
                            <span style="font-weight: bold; color: #4f46e5; font-size: 16px;">Inspira</span>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Title block -->
            <div class="title-pre">Gobierno del Estado e Iniciativa Inspira</div>
            <div class="title-main">Reconocimiento Oficial de Salud Mental</div>
            <div class="title-sub">Distintivo +Feliz</div>

            <div class="award-text">Otorga el presente distintivo a:</div>
            <div class="company-name">{{ $autoevaluacion->empresa->nombre_empresa }}</div>

            <div class="recognition-text">
                Por haber completado exitosamente su proceso de autoevaluación y auditoría documental, demostrando la implementación y fomento de políticas para la prevención, cuidado y fortalecimiento de la salud mental y bienestar emocional de sus colaboradores.
            </div>

            <!-- Level Badge -->
            <div class="maturity-container">
                <span class="maturity-badge {{ $badgeClass }}">
                    Nivel de Madurez: {{ $nivel }}
                </span>
            </div>

            <!-- Dictamen/Conclusions -->
            @if(!empty($dictamenFinal))
                <div class="dictamen-box">
                    <div class="dictamen-title">Dictamen del Comité Evaluador</div>
                    <div>{{ $dictamenFinal }}</div>
                </div>
            @endif

            <!-- Signatures -->
            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">Comité de Evaluación +Feliz</div>
                        <div class="signature-title">Secretaría de Salud y Bienestar</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">Iniciativa Inspira Coahuila</div>
                        <div class="signature-title">Dirección de Salud Mental</div>
                    </td>
                </tr>
            </table>

            <!-- Footer Details -->
            <div class="footer-info">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left; border: none; padding: 0;">
                            Folio del Distintivo: <strong style="color: #1e3a8a;">DF-{{ $autoevaluacion->empresa->folio }}</strong>
                        </td>
                        <td style="text-align: right; border: none; padding: 0;">
                            Fecha de Emisión: <strong>{{ now()->format('d/m/Y') }}</strong>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

</body>
</html>
