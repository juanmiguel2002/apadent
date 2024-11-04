import './bootstrap';
import Swal from 'sweetalert2';

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();
window.onload = function(){
    Livewire.on('clinicaSaved', message => {
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

    // Livewire.on('confirmDelete', ({ userId }) => {
    //     Swal.fire({
    //         title: '¿Estás seguro?',
    //         text: "¡No podrás revertir esto!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Sí, eliminar',
    //         cancelButtonText: 'Cancelar'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             Livewire.dispatch('deleteUserConfirmed', userId);
    //         } else {
    //             Swal.fire(
    //                 'Cancelado',
    //                 'El usuario no fue eliminado.',
    //                 'info'
    //             );
    //         }
    //     });
    // });

    Livewire.on('cli_edit',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Clínica editada con éxito',
            showConfirmButton: false,
            timer: 2500
        });
    });

    // Livewire.on('deleteClinic', ({ clinicaId }) => {
    //     Swal.fire({
    //         title: '¿Estás seguro?',
    //         text: "¡No podrás revertir esto!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Sí',
    //         cancelButtonText: 'Cancelar'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             this.emit('deleteClinicConfirmed', clinicaId); // Cambiado a window.Livewire.emit
    //         } else {
    //             Swal.fire(
    //                 'Cancelado',
    //                 'La Clínica no fue eliminada.',
    //                 'info'
    //             );
    //         }
    //     });
    // });

    Livewire.on('clinicaEliminada', message => {
        Swal.fire(
            'Eliminado',
            message,
            'success'
        );
    });

    // Livewire.on('deletePaciente', id => {
    //     Swal.fire({
    //         title: '¿Estás seguro?',
    //         text: "¡No podrás revertir esto!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Sí'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             Livewire.emit('deletePacienteConfirmed', id);
    //         }else {
    //             Swal.fire(
    //                 'Cancelado',
    //                 'El/la Paciente no fue eliminad@.',
    //                 'info'
    //             );
    //         }
    //     });
    // });

    // Livewire.on('deletedPaciente', () => {
    //     Swal.fire({
    //         position: 'top-end',
    //         icon: 'success',
    //         title: 'Paciente Eliminado correctamente',
    //         showConfirmButton: false,
    //         timer: 2500
    //     });
    // });

    Livewire.on('pacienteError', () => {
        Swal.fire(
            'Error!',
            'En la clínica no hay pacientes',
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

    Livewire.on('PacienteActivo',() => {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Paciente desactivado',
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

    Livewire.on('tratamientoEliminado', message => {
        Swal.fire(
            'Eliminado!',
            message,
            'success'
        );
    });
}
