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
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="font-size: 16px; line-height: 24px; color: #334155; margin-top: 0;">
                                Estimado/a <strong>{{ $empresa->nombre_responsable }}</strong>,
                            </p>
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                Le informamos que el registro de la empresa <strong>{{ $empresa->nombre_empresa }}</strong> ha sido procesado exitosamente en nuestra plataforma.
                            </p>
                            
                            <!-- Credentials Card -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin: 30px 0; text-align: left;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <td width="35%" style="font-size: 14px; color: #64748b; font-weight: 600;">Folio Asignado:</td>
                                        <td style="font-size: 16px; color: #0f172a; font-weight: 700; font-family: monospace;">{{ $empresa->folio }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 14px; color: #64748b; font-weight: 600;">Correo Registrado:</td>
                                        <td style="font-size: 16px; color: #0f172a;">{{ $empresa->correo }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 14px; color: #64748b; font-weight: 600;">Contraseña Temporal:</td>
                                        <td style="font-size: 16px; color: #2563eb; font-weight: 700; font-family: monospace; letter-spacing: 1px;">{{ $passwordTemporal }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <p style="font-size: 16px; line-height: 24px; color: #334155;">
                                Con estas credenciales podrá acceder al tablero del programa a través de nuestro portal y dar seguimiento al estado de su distintivo. Le recomendamos cambiar su contraseña en su primer inicio de sesión.
                            </p>
                            
                            <!-- CTA Button -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 30px; margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <a href="http://localhost:8080/admin" style="background-color: #2563eb; color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 6px; display: inline-block; box-shadow: 0 4px 6px rgba(37,99,235,0.2);">
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
