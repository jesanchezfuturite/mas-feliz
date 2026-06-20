<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recuperar Contraseña - Plataforma Más Feliz</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: 1px solid #000; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    
                    <!-- Top Section -->
                    <tr>
                        <td style="background-color: #f1cb5a; padding: 40px 30px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="60%" style="vertical-align: middle;">
                                        <h1 style="color: #ffffff; margin: 0; font-size: 34px; font-weight: 900; line-height: 1.1; letter-spacing: -1px;">
                                            Restablecer<br>Contraseña
                                        </h1>
                                    </td>
                                    <td width="40%" style="vertical-align: middle; text-align: right;">
                                        <!-- Logo -->
                                        <img src="{{ $message->embed(public_path('images/logo-mas-feliz.png')) }}" alt="+ Feliz" style="max-width: 150px; height: auto;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Middle Section -->
                    <tr>
                        <td style="background-color: #FCF6E9; padding: 40px 30px; color: #000000;">
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-top: 0; margin-bottom: 25px;">
                                Hola <strong>{{ $notifiable->name ?? $notifiable->nombres ?? $notifiable->nombre_responsable ?? 'Usuario' }}</strong>,
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-bottom: 25px;">
                                Recibiste este correo porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta de acceso al tablero del <strong>Distintivo +Feliz</strong>.
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-bottom: 30px;">
                                Si solicitaste este cambio, haz clic en el siguiente botón para definir una nueva contraseña:
                            </p>
                            
                            <!-- CTA Button -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px; margin-bottom: 40px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" style="background-color: #000000; color: #ffffff; padding: 14px 35px; text-decoration: none; font-size: 16px; font-weight: 700; display: inline-block;">
                                            Restablecer Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #555555; margin-bottom: 10px;">
                                Este enlace de restablecimiento de contraseña expirará en 60 minutos.
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #555555; margin-bottom: 30px;">
                                Si no realizaste esta solicitud, puedes ignorar este correo de forma segura; tu contraseña actual no sufrirá cambios.
                            </p>
                            
                            <hr style="border: none; border-top: 1px solid #d1d5db; margin-bottom: 20px;">
                            
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 18px; color: #6b7280; margin-bottom: 0;">
                                Si tienes problemas para hacer clic en el botón, copia y pega la siguiente dirección en tu navegador:<br>
                                <a href="{{ $url }}" style="color: #000000; word-break: break-all;">{{ $url }}</a>
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
                                                    <img src="{{ $message->embed(public_path('images/coahuila-negativo.png')) }}" alt="Coahuila" style="max-width: 100%; max-height: 55px; height: auto;">
                                                </td>
                                                <td width="12.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ $message->embed(public_path('images/pasos.png')) }}" alt="A pasos de gigante" style="max-width: 100%; max-height: 45px; height: auto; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ $message->embed(public_path('images/inspira-blanco.png')) }}" alt="Inspira Coahuila" style="max-width: 100%; max-height: 44px; height: auto;">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ $message->embed(public_path('images/sec-blanco.png')) }}" alt="SEC" style="max-width: 100%; max-height: 40px; height: auto;">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ $message->embed(public_path('images/ss-blanco.png')) }}" alt="SS" style="max-width: 100%; max-height: 35px; height: auto;">
                                                </td>
                                                <td width="16.66%" align="center" valign="middle" style="padding: 0 4px;">
                                                    <img src="{{ $message->embed(public_path('images/sm-blanco.png')) }}" alt="Salud Mental" style="max-width: 100%; max-height: 55px; height: auto;">
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
