@component('mail::message')
    # Hola, {{ $nombre }}

    Se ha registrado un nuevo paciente con la siguiente información:

    - **Nombre:** {{ $paciente->name . " ". $paciente->apellidos }}
    - **Diagnóstico:** {{ $paciente->tratamientos[0]->name }}
    - **Registrado:** {{ $fechaRegistro }}
    - **Clínica:** {{ $clinicaName }}

    La clínica {{ $clinicaName }} ha asignado un nuevo paciente al laboratorio.
    Haz clic en el botón de abajo para ver su perfil.

    @component('mail::button', ['url' => $perfilPacienteUrl])
        Ver Perfil del Paciente
    @endcomponent

    Gracias,
    {{ config('app.name') }}
@endcomponent
