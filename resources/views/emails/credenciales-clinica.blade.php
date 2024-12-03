@component('mail::message')
    # Bienvenido, {{ $name }}

    La clínica ha sido creada y ahora tienes acceso con los siguientes datos:

    - <strong>Email</strong> {{ $email }}
    - <strong>Contraseña:</strong> {{ $password }}

    Te recomendamos cambiar tu contraseña tras acceder por primera vez.

    {{-- @component('mail::button', ['url' => url('/login')])
        Acceder
    @endcomponent --}}
@endcomponent
