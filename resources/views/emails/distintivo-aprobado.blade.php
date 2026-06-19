<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>¡Felicidades! Su Distintivo +Feliz ha sido Aprobado - Plataforma Más Feliz</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">+Feliz</h1>
                            <p style="color: #bfdbfe; margin: 10px 0 0 0; font-size: 16px;">Reconocimiento de Acciones en Salud Mental</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-top: 0;">
                                Estimado/a <strong>{{ $autoevaluacion->empresa->nombre_responsable }}</strong> de <strong>{{ $autoevaluacion->empresa->nombre_empresa }}</strong>,
                            </p>
                            <p style="font-size: 18px; line-height: 26px; color: #059669; font-weight: 700; margin-top: 20px; margin-bottom: 15px;">
                                ¡Felicidades! Su Distintivo +Feliz ha sido Aprobado
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                Nos complace informarle que el Comité Evaluador ha revisado de manera formal su autoevaluación e historial documental, determinando que su organización cumple con todos los requisitos necesarios para la acreditación.
                            </p>
                            
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 20px 0; padding: 15px;">
                                <tr>
                                    <td style="font-size: 14px; line-height: 22px; color: #475569;">
                                        <strong>Nivel de Madurez Obtenido:</strong> <span style="color: #059669; font-weight: 700;">{{ $nivelMadurez }}</span><br>
                                        <strong>Folio de Registro:</strong> <span>{{ $autoevaluacion->empresa->folio }}</span><br>
                                        <strong>Dictamen del Comité:</strong><br>
                                        <div style="margin-top: 5px; font-style: italic; color: #0f172a;">"{{ $dictamenFinal }}"</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-bottom: 30px;">
                                Adjunto a este correo electrónico encontrará su certificado digital oficial en formato PDF listo para su descarga e impresión. Agradecemos su gran compromiso y labor en el fomento del bienestar y la salud mental en su entorno laboral.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            &copy; 2026 Gobierno del Estado - Iniciativa Inspira +Feliz.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
