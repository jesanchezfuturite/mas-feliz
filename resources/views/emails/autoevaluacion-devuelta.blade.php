<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acción Requerida: Observaciones en su Autoevaluación - Plataforma Más Feliz</title>
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
                            <p style="font-size: 18px; line-height: 26px; color: #1e3a8a; font-weight: 700; margin-top: 20px; margin-bottom: 15px;">
                                Observaciones en su Cédula de Autoevaluación
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                Le informamos que un evaluador de la plataforma ha revisado su autoevaluación y ha detectado criterios o políticas que requieren corrección, mayor claridad o evidencia documental adicional.
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-top: 15px;">
                                Su autoevaluación ha sido devuelta al estado de <strong>Borrador</strong> para que pueda realizar las modificaciones necesarias y corregir los puntos señalados.
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-top: 15px; margin-bottom: 30px;">
                                Por favor, haga clic en el siguiente botón para acceder al tablero de su organización y revisar las observaciones específicas:
                            </p>
                            
                            <!-- CTA Button -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $panelUrl }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 6px; display: inline-block; box-shadow: 0 4px 6px rgba(37,99,235,0.2);">
                                            Ver Observaciones y Corregir
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 14px; line-height: 20px; color: #64748b;">
                                Una vez que haya completado las correcciones y subido la nueva evidencia, recuerde hacer clic en el botón de solicitar revisión en su tablero para que el equipo evaluador pueda calificar su avance.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            Si tiene problemas para hacer clic en el botón, copie y pega la siguiente dirección en su navegador:<br>
                            <a href="{{ $panelUrl }}" style="color: #3b82f6; word-break: break-all;">{{ $panelUrl }}</a>
                            <br><br>
                            &copy; 2026 Gobierno del Estado - Iniciativa Inspira +Feliz.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
