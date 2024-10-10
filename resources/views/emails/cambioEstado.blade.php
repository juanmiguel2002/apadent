<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Actualización de Estado del Paciente</title>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">

    <!-- Wrapper -->
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #edf2f7; width: 100%; margin: 0; padding: 0;">
        <tr>
            <td align="center">
                <!-- Contenido principal del correo -->
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; margin: 0; padding: 0;">

                    <!-- Encabezado -->
                    <tr>
                        <td class="header" style="padding: 25px 0; text-align: center;">
                            <a href="{{ env('APP_URL') }}" style="color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
                                Laboratorio Strat
                            </a>
                        </td>
                    </tr>

                    <!-- Cuerpo del correo -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="background-color: #edf2f7; border-top: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff; border-radius: 2px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 32px; width: 570px;">

                                <!-- Contenido del cuerpo -->
                                <tr>
                                    <td class="content-cell" style="padding: 32px;">
                                        <p style="font-size: 16px; line-height: 1.5em; margin-top: 0;">
                                            ¡Hola!
                                        </p>
                                        <p style="color: #4a5568; font-size: 16px;">
                                            Nos ponemos en contacto para informarle que se ha registrado un cambio en el tratamiento del paciente <strong>{{ $paciente->name }} {{ $paciente->apellidos }}</strong>.
                                        </p>
                                        <ul style="color: #4a5568; font-size: 16px; list-style: none; padding: 0;">
                                            <li><strong>Tratamiento:</strong> {{ $trat->name }}</li>
                                            <li><strong>Etapa:</strong> {{ $etapa->name }}</li>
                                            <li><strong>Nuevo estado:</strong> {{ $estado }}</li>
                                        </ul>

                                        <!-- Botón de acción -->
                                        <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 30px auto; text-align: center; width: 100%;">
                                            <tr>
                                                <td align="center">
                                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                        <tr>
                                                            <td>
                                                                <a href="{{ env('APP_URL') }}" class="button button-primary" target="_blank" rel="noopener" style="background-color: #2d3748; border-radius: 4px; color: #fff; display: inline-block; font-size: 16px; font-weight: bold; padding: 8px 16px; text-decoration: none;">
                                                                    Acceso a clientes
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Fin botón de acción -->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Pie de página -->
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="width: 570px; text-align: center;">
                                <tr>
                                    <td class="content-cell" align="center" style="padding: 32px;">
                                        <p style="font-size: 12px; color: #b0adc5;">
                                            © {{ date('Y') }} Laboratorio Strat. Todos los derechos reservados.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <!-- Fin del Wrapper -->

</body>
</html>
