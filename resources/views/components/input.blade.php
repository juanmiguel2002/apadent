@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-blue-500 focus:border-blue-500 rounded-3xl shadow-sm text-azul']) !!}>
