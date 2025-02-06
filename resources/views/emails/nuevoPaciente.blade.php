@component('mail::message')
# Hola, {{ $nombre }}

Se ha registrado un nuevo paciente con la siguiente información:

- **Nombre:** {{ $paciente->nombre }}
- **Edad:** {{ $paciente->edad }} años
- **Diagnóstico:** {{ $paciente->diagnostico }}
- **Registrado:** {{$fechaRegistro}}

@if($perfilPacienteUrl)

La clínica {{ $clinicaName }} ha asignado un nuevo paciente al laboratorio.
Haz clic en el botón de abajo para ver su perfil.

@component('mail::button', ['url' => $perfilPacienteUrl])
    Ver Perfil del Paciente
@endcomponent

@endif

Gracias,
{{ config('app.name') }}
@endcomponent
