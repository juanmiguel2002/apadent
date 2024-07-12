import './bootstrap';
import Swal from 'sweetalert2';

import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start();
// window.onload = function(){
//     Livewire.on('clinicaSaved', message => {
//         Swal.fire({
//             title: 'Éxito',
//             text: message,
//             icon: 'success',
//             confirmButtonText: 'Aceptar'
//         });
//     });
//     Livewire.on('deleteClinic', id => {
//         Swal.fire({
//             title: '¿Estás seguro?',
//             text: "¡No podrás revertir esto!",
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Sí'
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 Livewire.emit('deleteClinicConfirmed', id);
//             }
//         });
//     });

//     Livewire.on('clinicDeleted', () => {
//         Swal.fire(
//             'Eliminado!',
//             'La clínica ha sido eliminada.',
//             'success'
//         )
//     });

//     Livewire.on('nuevoPaciente',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Paciente añadido con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });


//     Livewire.on('factura',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Factura añadida con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('etapa',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Etapa añadida con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('etapadel',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Etapa borrada con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('imagen',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Imagen(es) añadida(s) con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('archivo',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Archivo(s) añadido(s) con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('mensaje',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Mensaje borrado con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('password',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Contraseña cambiada con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('cli_edit',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Clínica editada con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('revision',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Fecha editada con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('pacienteEdit',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Paciente editado con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('cbctborrar',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Archivo borrado con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });

//     Livewire.on('imagenborrar',() => {
//         Swal.fire({
//             position: 'top-end',
//             icon: 'success',
//             title: 'Imagen con éxito',
//             showConfirmButton: false,
//             timer: 2500
//         });
//     });
// }
