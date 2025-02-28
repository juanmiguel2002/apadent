import './bootstrap';
import Swal from 'sweetalert2';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
window.onload = function(){
    Livewire.on('clinicaSaved', message => {
        Swal.fire({
            title: 'Éxito',
            text: message,
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    });

    // GESTOR DE ARCHIVOS
    Livewire.on('carpetaCreated', message => {
        Swal.fire({
            title: 'Éxito',
            text: message,
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    });

    Livewire.on('usuarioSaved', message => {
        Swal.fire({
            title: 'Éxito',
            text: message,
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    });
    Livewire.on('pathChanged', (currentPath) => {
        console.log('Current Path:', currentPath);
    });

    Livewire.on('cli_edit',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Clínica editada con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('clinicaEliminada', message => {
        Swal.fire(
            'Eliminado',
            message,
            'success'
        );
    });

    Livewire.on('error', message => {
        Swal.fire(
            'Error!',
            message,
            'warning'
        )
    });

    Livewire.on('estadoActualizado', () => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Estado modificado',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('nuevoPaciente',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Paciente añadido con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('PacienteActivo',message => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('pacienteEdit',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Paciente editado con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('stripping',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Striping añadido',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('revision',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Fecha editada con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('factura',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Factura añadida con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('etapa',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Etapa añadida con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('imagen',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Imagen(es) añadida(s) con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('archivo',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Archivo(s) añadido(s) con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('archivoComple', message => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('mensaje',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Mensaje enviado',
            showConfirmButton: false,
            timer: 2500
        });
    });

    Livewire.on('password',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Contraseña cambiada con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    // Mensaje en la pag tratamientos
    Livewire.on('tratamiento', message => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    });
    Livewire.on('tratamientoAsignado', message=> {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    });
}
window.addEventListener('recargar-pagina', event => {
    window.location.reload(); // Recargar la página completa
});
