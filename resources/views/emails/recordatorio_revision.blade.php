<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Revisión</title>
</head>
<body>
    <h1>Recordatorio de Revisión</h1>
    <p>Estimado equipo de la clínica,</p>
    <p>Este es un recordatorio de que el paciente <strong>{{ $paciente->name }}</strong> tiene una próxima revisión programada en <strong>{{ $diasRestantes }} días</strong>.</p>
    <p>Por favor, recuerden gestionar la revisión de acuerdo a la programación.</p>
    <p>Saludos cordiales,</p>
    <p>El equipo de gestión de pacientes.</p>
</body>
</html>
