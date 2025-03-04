<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('storage/recursos/imagenes/favicon.png') }}" type="image/x-icon">
</head>
<body>
    <iframe src="{{ route('ver.pdf', ['ruta' => $ruta]) }}" width="100%" height="100%"></iframe>
</body>
</html>

