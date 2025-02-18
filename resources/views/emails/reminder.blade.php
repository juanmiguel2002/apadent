<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
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

<body style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation"
        style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;">
        <tr>
            <td align="center"
                style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation"
                    style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; width: 100%;">
                    <tr>
                        <td class="header"
                            style="box-sizing: border-box; padding: 25px 0; text-align: center;">
                            <a href="#" style="box-sizing: border-box; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none; display: inline-block;">
                                Laboratorio Strat
                            </a>
                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0"
                            style="box-sizing: border-box; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation"
                                style="box-sizing: border-box; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell" style="box-sizing: border-box; max-width: 100vw; padding: 32px;">
                                        <p style="box-sizing: border-box; font-size: 16px; line-height: 1.5em; margin-top: 0;">
                                            Hola {{ $clinica->name }},
                                        </p>
                                        <p style="box-sizing: border-box; font-size: 16px; line-height: 1.5em; margin-top: 0;">
                                            Te recordamos que el paciente <strong>{{ $paciente->name }}</strong> tiene una revisión programada para la fecha <strong>{{ $etapa->revision }}</strong> de la etapa {{$etapa->name}}.</p>
                                        <p style="box-sizing: border-box; font-size: 16px; line-height: 1.5em; margin-top: 0;">Por favor, asegúrate de prepararte para esta revisión.</p>
                                        <p style="box-sizing: border-box; font-size: 16px; line-height: 1.5em; margin-top: 0;">Saludos,</p>
                                        <p style="box-sizing: border-box; font-size: 16px; line-height: 1.5em; margin-top: 0;">Equipo de la Clínica</p>

                                        {{-- <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                            style="box-sizing: border-box; margin: 30px auto; padding: 0; text-align: center; width: 100%;">
                                            <tr>
                                                <td align="center">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                        <tr>
                                                            <td align="center">
                                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                                    <tr>
                                                                        <td>
                                                                            <a href="{{ env('APP_URL')}}" class="button button-primary" target="_blank" rel="noopener"
                                                                                style="box-sizing: border-box; border-radius: 4px; color: #fff; display: inline-block; text-decoration: none; background-color: #2d3748; border: 8px solid #2d3748; font-size: 16px; font-weight: bold;">
                                                                                Acceso a clientes
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table> --}}

                                        {{-- <table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                            style="box-sizing: border-box; border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;">
                                            <tr>
                                                <td>
                                                    <p style="box-sizing: border-box; font-size: 14px; line-height: 1.5em; margin-top: 0;">
                                                        Si no puedes hacer clic en el botón "Acceso a clientes", copia y pega este enlace en tu navegador:
                                                        <a href="{{ env('APP_URL')}}" style="color: #3869d4;">{{ env('APP_URL')}}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table> --}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation"
                                style="box-sizing: border-box; text-align: center; width: 570px;">
                                <tr>
                                    <td class="content-cell" align="center" style="box-sizing: border-box; max-width: 100vw; padding: 32px;">
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
</body>
</html>
