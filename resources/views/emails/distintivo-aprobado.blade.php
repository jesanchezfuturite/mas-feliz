<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>¡Felicidades! Su Distintivo +Feliz ha sido Aprobado - Plataforma Más Feliz</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: 1px solid #000; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    
                    <!-- Top Section -->
                    <tr>
                        <td style="background-color: #fbe700; padding: 40px 30px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="60%" style="vertical-align: middle;">
                                        <h1 style="color: #000000; margin: 0; font-size: 38px; font-weight: 900; line-height: 1.1; letter-spacing: -1px;">
                                            Distintivo<br>Aprobado
                                        </h1>
                                    </td>
                                    <td width="40%" style="vertical-align: middle; text-align: right;">
                                        <!-- Logo -->
                                        <img src="{{ url('images/logo_mas_feliz_naranja.png') }}" alt="+ Feliz" style="max-width: 150px; height: auto;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Middle Section -->
                    <tr>
                        <td style="background-color: #FCF6E9; padding: 40px 30px; color: #000000;">
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-top: 0; margin-bottom: 25px;">
                                Estimado/a <strong>{{ $autoevaluacion->empresa->nombre_responsable }}</strong> de <strong>{{ $autoevaluacion->empresa->nombre_empresa }}</strong>,
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 18px; line-height: 26px; font-weight: 700; color: #e46c23; margin-bottom: 25px;">
                                ¡Felicidades! Su Distintivo +Feliz ha sido Aprobado
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-bottom: 25px;">
                                Nos complace informarle que el Comité Evaluador ha revisado de manera formal su autoevaluación e historial documental, determinando que su organización cumple con todos los requisitos necesarios para la acreditación.
                            </p>
                            
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: 1px solid #e46c23; margin: 20px 0; padding: 20px;">
                                <tr>
                                    <td style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 15px; line-height: 24px; color: #333333;">
                                        <strong>Nivel de Madurez Obtenido:</strong> <span style="color: #e46c23; font-weight: 700;">{{ $nivelMadurez }}</span><br>
                                        <strong>Folio de Registro:</strong> <span>{{ $autoevaluacion->empresa->folio }}</span><br>
                                        <strong>Dictamen del Comité:</strong><br>
                                        <div style="margin-top: 5px; font-style: italic; color: #555555;">"{{ $dictamenFinal }}"</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-bottom: 0;">
                                Adjunto a este correo electrónico encontrará su certificado digital oficial en formato PDF listo para su descarga e impresión. Agradecemos su gran compromiso y labor en el fomento del bienestar y la salud mental en su entorno laboral.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer Section -->
                    <tr>
                        <td style="background-color: #000000; padding: 30px 20px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="vertical-align: middle;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="20.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ url('images/coahuila-negativo.png') }}" alt="Coahuila" style="max-width: 100%; max-height: 55px; height: auto;">
                                                </td>
                                                <td width="12.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ url('images/pasos_blanco.png') }}" alt="A pasos de gigante" style="max-width: 100%; max-height: 45px; height: auto; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ url('images/inspira-blanco.png') }}" alt="Inspira Coahuila" style="max-width: 100%; max-height: 44px; height: auto;">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ url('images/sec-blanco.png') }}" alt="SEC" style="max-width: 100%; max-height: 40px; height: auto;">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ url('images/ss-blanco.png') }}" alt="SS" style="max-width: 100%; max-height: 35px; height: auto;">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ url('images/sm-blanco.png') }}" alt="Salud Mental" style="max-width: 100%; max-height: 55px; height: auto;">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
