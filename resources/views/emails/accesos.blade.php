<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Accesos al Tablero +Feliz</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: 1px solid #000; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    
                    <!-- Top Section -->
                    <tr>
                        <td style="background-color: #1EBBA3; padding: 40px 30px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="60%" style="vertical-align: middle;">
                                        <h1 style="color: #ffffff; margin: 0; font-size: 38px; font-weight: 900; line-height: 1.1; letter-spacing: -1px;">
                                            Gracias por<br>registrarte<br>al Distintivo
                                        </h1>
                                    </td>
                                    <td width="40%" style="vertical-align: middle; text-align: right;">
                                        <!-- Logo -->
                                        <img src="{{ url('images/logo-mas-feliz.png') }}" alt="+ Feliz" style="max-width: 150px; height: auto;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Middle Section -->
                    <tr>
                        <td style="background-color: #FCF6E9; padding: 40px 30px; color: #000000;">
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-top: 0; margin-bottom: 25px;">
                                El Gobierno del Estado de Coahuila, a través de la Oficina Inspira Coahuila, la Secretaría de Economía y la Secretaría de Salud, agradece tu interés en formar parte de esta iniciativa.
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-bottom: 25px;">
                                El Distintivo <strong>+Feliz</strong> es un reconocimiento estatal que distingue a las organizaciones comprometidas con la prevención, cuidado y fortalecimiento de la salud mental de sus colaboradores.
                            </p>
                            <p style="font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; text-align: justify; color: #333333; margin-bottom: 35px;">
                                Hemos recibido correctamente tu registro. Te compartimos tus datos para ingresar a la plataforma y dar seguimiento a tu postulación:
                            </p>
                            
                            <table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin-bottom: 40px;">
                                <tr>
                                    <td width="30%" style="font-size: 16px; font-weight: 700; text-transform: uppercase;">USUARIO:</td>
                                    <td style="font-size: 16px;">{{ $empresa->correo }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 16px; font-weight: 700; text-transform: uppercase;">CONTRASEÑA:</td>
                                    <td style="font-size: 16px; font-family: monospace; letter-spacing: 1px;">{{ $passwordTemporal }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 16px; font-weight: 700;">Liga de acceso:</td>
                                    <td style="font-size: 16px;"><a href="{{ url('/tablero') }}" style="color: #000000; text-decoration: underline; font-weight: 700;">{{ url('/tablero') }}</a></td>
                                </tr>
                            </table>
                            
                            <p style="font-size: 20px; line-height: 30px; font-weight: 800; text-align: center; margin-bottom: 0; padding: 0 10px;">
                                Gracias por contribuir al fortalecimiento de espacios laborales comprometidos con la salud mental en nuestro Estado.
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
