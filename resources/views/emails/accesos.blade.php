<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Accesos al Tablero +Feliz</title>
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
                    
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-top: 0;">
                                Estimado/a <strong>{{ $empresa->nombre_responsable }}</strong> de <strong>{{ $empresa->nombre_empresa }}</strong>,
                            </p>
                            <p style="font-size: 18px; line-height: 26px; color: #1e3a8a; font-weight: 700; margin-top: 20px; margin-bottom: 15px;">
                                ¡Gracias por registrarte al Distintivo +Feliz!
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                El Gobierno del Estado de Coahuila, a través de la Oficina Inspira Coahuila, la Secretaría de Economía y la Secretaría de Salud, agradece tu interés en formar parte de esta iniciativa.
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                El Distintivo +Feliz es un reconocimiento estatal que distingue a las organizaciones comprometidas con la prevención, cuidado y fortalecimiento de la salud mental de sus colaboradores.
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                Hemos recibido correctamente tu registro. Te compartimos tus datos para ingresar a la plataforma y dar seguimiento a tu postulación:
                            </p>
                            
                            <!-- Credentials Card -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin: 30px 0; text-align: left;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <td width="35%" style="font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase;">Folio:</td>
                                        <td style="font-size: 16px; color: #0f172a; font-weight: 700; font-family: monospace;">{{ $empresa->folio }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase;">Usuario:</td>
                                        <td style="font-size: 16px; color: #0f172a; font-weight: 600;">{{ $empresa->correo }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase;">Contraseña:</td>
                                        <td style="font-size: 16px; color: #2563eb; font-weight: 700; font-family: monospace; letter-spacing: 1px;">{{ $passwordTemporal }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase;">Liga de acceso:</td>
                                        <td style="font-size: 15px; color: #3b82f6;"><a href="{{ url('/tablero') }}" style="color: #2563eb; text-decoration: underline;">{{ url('/tablero') }}</a></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-bottom: 30px;">
                                Gracias por contribuir al fortalecimiento de espacios laborales comprometidos con la salud mental en nuestro Estado.
                            </p>
                            
                            <!-- CTA Button -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px; margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/tablero') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 6px; display: inline-block; box-shadow: 0 4px 6px rgba(37,99,235,0.2);">
                                            Acceder al Tablero
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8;">
                            Este es un correo automático, por favor no responda directamente a esta dirección.<br>
                            &copy; 2026 Gobierno del Estado - Iniciativa Inspira +Feliz.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
