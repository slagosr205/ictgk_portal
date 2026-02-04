import * as bootstrap from './bootstrap';

import jQuery, { data } from 'jquery';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons-dt';
import Swal from 'sweetalert2';
import 'remixicon/fonts/remixicon.css';
import datepicker from 'js-datepicker';
import { Modal, Toast, Tooltip } from 'bootstrap';
import flatpickr from 'flatpickr';


window.$ = jQuery;

window.jQuery = jQuery
var btnupdate;
var table=$('#tbcandidatos').DataTable({
   
    responsive:true,
    ordering:false,
    initComplete:function(){
        var searchInput = $('#dt-search-0');
            searchInput.addClass('border');
    }
});
var tbperfiles=new DataTable('#tbperfiles')

var dtUsuariosEl = document.getElementById('dtUsuarios');
if (dtUsuariosEl && dtUsuariosEl.dataset.datatable === 'true') {
    var dtUsuarios = new DataTable('#dtUsuarios');
}
//var tbhistoricoempresa=new DataTable('#tbhistoricoempresa');
// Verificación de librerías
console.log('Verificando librerías:', {
    Swal: typeof Swal,
    bootstrap: typeof bootstrap
});

// Función simple para mostrar notificaciones si hay elementos ocultos
document.addEventListener('DOMContentLoaded', function() {
    const alertElement = document.getElementById('notification-alert');
    if (alertElement) {
        console.log('Elemento de alerta encontrado:', alertElement.dataset);
    }
});



if(document.getElementById('error')){
    let error=document.getElementById('error')
    Swal.fire({
        title: "Error",
        text: error.innerHTML,
        
        icon: "error"
      });
}

 // Sticky Navbar
 $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.sticky-top').addClass('shadow-sm').css('top', '0px');
    } else {
        $('.sticky-top').removeClass('shadow-sm').css('top', '-100px');
    }
  });

   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
});
  $('.back-to-top').click(function () {
      $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
      return false;
  });



var btnshowElementImport=document.getElementById('showElementImport')
var elementImport=document.getElementsByClassName('d-none')


var cardInfoID=document.getElementsByClassName('card2');


$('.card2').click(function(){
    var idElemento = $(this).attr('id');
    var nombre=document.getElementById('nombre');
    var identidad=document.getElementById('identidad');
    var direccion=document.getElementById('direccion')
    $.ajax({
        url:'/obtenerficha/'+idElemento,
        type:'GET',
        //data:{identidad:idElemento},
        success:function(res){
            nombre.innerHTML=res.nombre+' '+res.apellido;
            identidad.innerHTML='Numero de Identidad: <p><strong>'+res.identidad+'<strong/></p>';
            direccion.innerHTML='Direccion: <p><strong> 3 calle 7 avenida Colonia Modelo, San Pedro Sula.</strong></p>'
        }
    })
})

// ============================================
// CONFIGURACIÓN Y UTILIDADES
// ============================================

/**
 * Valida formato de DNI/Identidad
 */
const validarDNI = (dni) => {
    if (!dni || dni.trim() === '') {
        Swal.fire({
            title: 'Error de validación',
            text: 'Por favor ingrese un DNI válido',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }
    return true;
};

/**
 * Muestra un loader mientras se carga la información
 */
const mostrarLoader = (elemento) => {
    elemento.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Buscando información...</p>
        </div>
    `;
};

/**
 * Muestra mensaje de búsqueda vacía
 */
const mostrarMensajeInicial = (elemento) => {
    elemento.innerHTML = `
        <div class="text-center py-5">
            <i class="ri-search-line" style="font-size: 64px; color: #6c757d;"></i>
            <p class="mt-3 text-muted">Ingrese un DNI para buscar información</p>
        </div>
    `;
};

/**
 * Inicializa DataTable de forma segura
 */
let dataTableInstance = null;
const inicializarDataTable = () => {
    // Destruir instancia previa si existe
    if (dataTableInstance) {
        try {
            dataTableInstance.destroy();
        } catch (error) {
            console.warn('Error al destruir DataTable:', error);
        }
    }
    
    // Crear nueva instancia
    const tabla = document.getElementById('tbhistoricoempresa');
    if (tabla) {
        dataTableInstance = new DataTable('#tbhistoricoempresa', {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            responsive: true,
            order: [[3, 'desc']] // Ordenar por fecha de ingreso descendente
        });
    }
};

/**
 * Configura los event listeners para botones de actualización
 */
const configurarBotonesActualizacion = () => {
    const btnUpdate = document.querySelectorAll('.btnupdate');
    
    btnUpdate.forEach(button => {
        // Remover listeners previos para evitar duplicados
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        newButton.addEventListener('click', function() {
            const telefono = document.getElementById('telefono');
            const correo = document.getElementById('correo');
            const direccion = document.getElementById('direccion');
            
            if (!telefono || !correo || !direccion) {
                console.error('Elementos del formulario no encontrados');
                return;
            }
            
            const id = this.id;
            ActualizaFicha(
                correo.value.trim(),
                telefono.value.trim(),
                direccion.value.trim(),
                id
            );
        });
    });
};

const ActualizaFicha = (correo, telefono, direccion, id) => {   
    const formData = new FormData();
    formData.append('correo', correo);
    formData.append('telefono', telefono);
    formData.append('direccion', direccion);
    formData.append('id', id);

    // Convertir a mayúsculas automáticamente en nombre completo
        const nombreInput = document.getElementById('nombre');
        const apellidoInput = document.getElementById('apellido');

        if (nombreInput) {
            nombreInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
            
            // Validar que solo contenga letras y espacios
            nombreInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.keyCode);
                if (!/[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(char)) {
                    e.preventDefault();
                }
            });
        }

        if (apellidoInput) {
            apellidoInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
            
            // Validar que solo contenga letras y espacios
            apellidoInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.keyCode);
                if (!/[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(char)) {
                    e.preventDefault();
                }
            });
        }

        formData.append('nombre', nombreInput ? nombreInput.value.trim() : '');
        formData.append('apellido', apellidoInput ? apellidoInput.value.trim() : '');

    fetch('/actualizacion-ficha', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.icon === 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Información actualizada correctamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            throw new Error(data.message || 'Error al actualizar la información');
        }
    })
    .catch(error => {
        console.error('Error al actualizar la información:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'No se pudo actualizar la información. Intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    });
};


/**
 * Muestra modal para crear nuevo candidato
 */
const mostrarModalNuevoCandidato = () => {
    const modalElement = document.getElementById('registerCandidate');
    if (modalElement) {
        const modal = new Modal(modalElement);
        modal.show();
        
        // Escuchar cuando se cierra el modal
        modalElement.addEventListener('hidden.bs.modal', () => {
            const fichapersonal = document.getElementById('fichapersonal');
            if (fichapersonal) {
                mostrarMensajeInicial(fichapersonal);
            }
        }, { once: true }); // Solo ejecutar una vez
        
    } else {
        console.error('Modal #registerCandidate no encontrado');
    }
};

// ============================================
// FUNCIÓN PRINCIPAL DE BÚSQUEDA
// ============================================

/**
 * Busca información personal por DNI
 * @param {string} dni - Número de identidad a buscar
 */
const buscarInformacionPersonal = async (dni) => {
    // Validar DNI
    if (!validarDNI(dni)) {
        return;
    }
    
    const fichapersonal = document.getElementById('fichapersonal');
    if (!fichapersonal) {
        console.error('Elemento #fichapersonal no encontrado');
        return;
    }
    
    // Mostrar loader
    mostrarLoader(fichapersonal);
    
    try {
        // Realizar petición
        const response = await fetch(`/infopersonal/${dni.trim()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });
        
        // Obtener el content-type antes de procesar
        const contentType = response.headers.get('content-type');
        
        // Caso especial: 404 es un caso de negocio, no un error
        if (response.status === 404) {
            let mensaje = 'No se encontró información del candidato';
            
            // Intentar obtener el mensaje del servidor
            if (contentType && contentType.includes('application/json')) {
                try {
                    const data = await response.json();
                    mensaje = data.response || data.message || mensaje;
                } catch (e) {
                    console.warn('No se pudo parsear JSON del 404');
                }
            }
            
            await manejarCandidatoNoEncontrado(mensaje);
            return;
        }
        
        // Para otros errores HTTP (500, 503, etc.) sí lanzar error
        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
        }
        
        // Procesar respuesta exitosa
        let data;
        
        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
            
            // Verificar si hay un código de error en el JSON
            if (data.code === 400 || data.code === 404) {
                await manejarCandidatoNoEncontrado(data.response || 'No se encontró información');
                return;
            }
            
            // Si el JSON tiene un mensaje de error pero código 200
            if (data.error || data.message) {
                throw new Error(data.error || data.message);
            }
        } else {
            // Es HTML directo (caso normal de éxito)
            data = await response.text();
        }
        
        // Renderizar información encontrada
        fichapersonal.innerHTML = data;
        
        // Inicializar componentes
        await inicializarComponentes();
        
        // Notificación de éxito
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Información cargada correctamente',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        
    } catch (error) {
        console.error('Error al buscar información:', error);
        
        fichapersonal.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="ri-error-warning-line me-2"></i>
                <strong>Error de conexión:</strong> No se pudo cargar la información. 
                Por favor, verifique su conexión e intente nuevamente.
            </div>
        `;
        
        Swal.fire({
            title: 'Error de conexión',
            text: error.message || 'No se pudo conectar con el servidor. Verifique su conexión e intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
};

/**
 * Inicializa todos los componentes después de cargar la información
 */
const inicializarComponentes = async () => {
    // Esperar un tick para que el DOM se actualice
    await new Promise(resolve => setTimeout(resolve, 100));
    
    // Inicializar DataTable
    inicializarDataTable();
    
    // Configurar botones de actualización
    configurarBotonesActualizacion();
    
    // Inicializar tooltips si existen
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
};

/**
 * Maneja el caso cuando no se encuentra un candidato
 */
const manejarCandidatoNoEncontrado = async (mensaje) => {
    const fichapersonal = document.getElementById('fichapersonal');
    
    const result = await Swal.fire({
        title: mensaje || 'Candidato no encontrado',
        text: '¿Desea agregar un nuevo registro?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Sí, crear registro',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    });
    
    if (result.isConfirmed) {
        mostrarModalNuevoCandidato();
    } else {
        // Si cancela, mostrar mensaje inicial
        if (fichapersonal) {
            mostrarMensajeInicial(fichapersonal);
        }
    }
};

// ============================================
// EVENT LISTENERS
// ============================================

/**
 * Inicializar todo cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', () => {
    // Mostrar mensaje inicial
    const fichapersonal = document.getElementById('fichapersonal');
    if (fichapersonal && fichapersonal.innerHTML.trim() === '') {
        mostrarMensajeInicial(fichapersonal);
    }
    
    // Event listener para botón de búsqueda
    const btnDNI = document.getElementById('btndni');
    if (btnDNI) {
        btnDNI.addEventListener('click', (e) => {
            e.preventDefault();
            const dniInput = document.getElementById('dni');
            if (dniInput) {
                buscarInformacionPersonal(dniInput.value);
            }
        });
    }
    
    // Event listener para Enter en input DNI
   /* const dniInput = document.getElementById('dni');
    if (dniInput) {
        dniInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarInformacionPersonal(e.target.value.replace);
            }
        });
        
        // Validación en tiempo real (opcional)
        dniInput.addEventListener('input', (e) => {
            // Remover caracteres no numéricos excepto guiones
            e.target.value = e.target.value.replace(/[^\d-]/g, '');
        });
    }*/
    
    // Event listener global para el modal (por si se abre desde otro lugar)
    const modalElement = document.getElementById('registerCandidate');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', () => {
            const fichapersonal = document.getElementById('fichapersonal');
            // Solo limpiar si está mostrando el loader
            if (fichapersonal && fichapersonal.innerHTML.includes('Buscando información')) {
                mostrarMensajeInicial(fichapersonal);
            }
        });
    }
});

// ============================================
// LIMPIEZA AL SALIR (Prevenir memory leaks)
// ============================================
window.addEventListener('beforeunload', () => {
    if (dataTableInstance) {
        try {
            dataTableInstance.destroy();
        } catch (error) {
            console.warn('Error al limpiar DataTable:', error);
        }
    }
});


var success=document.getElementById('success')
var warning=document.getElementById('warning')
var dargen=document.getElementById('danger')
if(success!==null){
    
    Swal.fire({
        title:'Estado de Registro',
        text:success.innerHTML,
        icon:'success'
    })
}

if(warning!==null){
    
    Swal.fire({
        title:'Estado de Registro',
        text:warning.innerHTML,
        icon:'warning'
    })
}

if(dargen!==null){
    
    Swal.fire({
        title:'Estado de Registro',
        text:dargen.innerHTML,
        icon:'dargen'
    })
}

if(document.querySelectorAll('.chkpermiso')!==null && document.querySelectorAll('.form-check-label')!==null){
const checkboxes = document.querySelectorAll('.chkpermiso');
const labels = document.querySelectorAll('.form-check-label');

 // Iterar sobre cada checkbox
 checkboxes.forEach((checkbox, index) => {
    // Agregar un event listener para detectar cambios en el checkbox
    checkbox.addEventListener("change", function() {//
        const idrow=checkbox.closest('tr');
        let idRole=idrow.querySelector('.idRole').innerHTML;
        let idField=this.id;
        let valRole=0;
        //prueba
      // Verificar el estado del checkbox y cambiar el texto del label en consecuencia
      if (this.checked) {
        valRole=1;
        ajaxUpdateRole(idRole,idField,valRole)
        labels[index].textContent = "Permitido";
      } else {
        ajaxUpdateRole(idRole,idField,valRole)
        labels[index].textContent = "No permitido";
      }
    });
  });
}


function ajaxUpdateRole(idrole, fieldAuth,valAuth)
{
    let dataField=fieldAuth.split('_')[1];
    let formPerfil=new FormData();
    formPerfil.append('idrole',idrole)
    formPerfil.append('dataField',dataField)
    formPerfil.append('valAuth',valAuth)
    $.ajax({
        url: '/update-role', // Cambiar esto por la ruta en tu aplicación Laravel
                type: 'POST',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formPerfil,
                processData: false, // Evitar el procesamiento del formData
                contentType: false, // Evitar el encabezado Content-Type
                success: function(response) {
                // Manejar la respuesta del servidor si es necesario
                console.log(response);
                },
                error: function(xhr, status, error) {
                // Manejar errores si es necesario
                console.error(error);
                }
    })
}

if(document.querySelectorAll('.update')!=null)
{
    const btnupdateCAndidate=document.querySelectorAll('.update')

    btnupdateCAndidate.forEach((btn, index)=>{
        btn.addEventListener('click',function(e){
            console.log(btn.id)
        })
    })
}

const toastTrigger = document.getElementById('liveToastBtn')
const toastLiveExample = document.getElementById('liveToast')

if (toastTrigger) {
  const toastBootstrap = Toast.getOrCreateInstance(toastLiveExample)
  toastTrigger.addEventListener('click', () => {
    toastBootstrap.show()
  })
}




document.addEventListener('click',function(event){
    if(event.target.classList.contains('btndesbloqueoRecomendacion'))
    {
        event.preventDefault();
        console.log('prueba')
        var identidadVal=event.target.getAttribute('data-identidad')
        var empresaID=event.target.getAttribute('data-empresaID')
        var frmDesbloqueo=new FormData();
        frmDesbloqueo.append('identidad',identidadVal)
        frmDesbloqueo.append('empresa_id',empresaID)
       $.ajax({
            url: '/desbloquear-recomendacion', // Cambiar esto por la ruta en tu aplicación Laravel
            type: 'POST',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: frmDesbloqueo,
            processData: false, // Evitar el procesamiento del formData
            contentType: false, // Evitar el encabezado Content-Type
            success: function(response) {
            // Manejar la respuesta del servidor si es necesario
                if(response.status===200)
                {
                    Swal.fire({
                        title:'Estado de Actualizacion',
                        text:response.success,
                        icon:'success'
                    })

                    const tdElement = document.querySelector('.cambiarestado');
                    const modalElement = document.getElementById('modaldebloqueoRecomendacion');

                    


                    if (tdElement && tdElement.querySelector('button')) {
                        // Obtener el botón dentro del <td>
                        const buttonElement = tdElement.querySelector('button');
                    
                        // Crear un nuevo elemento <i> para reemplazar el <button>
                        const iconElement = document.createElement('i');
                    
                        // Configurar las clases y estilos para el icono
                        iconElement.className = 'ri-information-line'; // Clase CSS para el icono
                        iconElement.style.fontSize = '32px'; // Tamaño del icono
                        iconElement.style.color = '#d6c73d'; // Color del icono
                    
                        // Reemplazar el <button> con el nuevo <i>
                        tdElement.replaceChild(iconElement, buttonElement);
                    }else{
                        if(response.status===404)
                        {
                            Swal.fire({
                                title:'Estado de Actualizacion',
                                text:response.fail,
                                icon:'error'
                            })
                        }
                    }


                }
            },
            error: function(xhr, status, error) {
            // Manejar errores si es necesario
            console.error(error);
            }
       })
    }
})


const botonesDesbloquear = document.querySelectorAll('.btnbloqueo');
const modalidentidad = document.getElementById('modalidentidad');
const lockidentidad = document.getElementById('lockidentidad');
    // Iterar sobre cada botón y agregar un evento de clic a cada uno
    document.addEventListener('click', function(event) {
        const unlockBtn = event.target.closest('.btndesbloqueo');
        const lockBtn = event.target.closest('.btnbloqueo');
        const identidad = unlockBtn
            ? (unlockBtn.value || unlockBtn.getAttribute('value') || '')
            : (lockBtn ? (lockBtn.value || lockBtn.getAttribute('value') || '') : '');
        
        if (unlockBtn && modalidentidad) {
            
            modalidentidad.value = identidad;
            console.log('Desbloquear candidato con identidad:', identidad);
        }else{
            if (lockBtn && lockidentidad) 
            {
                
                lockidentidad.value = identidad;
                console.log('Bloquear candidato con identidad:', identidad);
            }
        }

        

    });

 

    /**Capturar el submit para procesar todos los ingresos masivos */

    if(document.getElementById('frmimportInputCandidate') != null) {
        var archivo_csv = $('#archivo_csv');
        var importInputCandidate = new Modal('#importInputCandidate');
        var importacionPersonal = $('#importacionPersonal');
        
        $(document).on('submit', '#frmimportInputCandidate', function(e) {
            e.preventDefault();
            var inputCSVimport = $('#archivo_csv')[0].files[0];
            var formCSV = new FormData();
            
            console.log('Archivo subido');
            formCSV.append('archivo_csv', inputCSVimport);
            
            $.ajax({
                url: '/ingresos-masivos',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formCSV,
                processData: false,
                contentType: false,
                
                success: function(response) {
                    if (response.status === 202) {
                        var tablaHTML = '<table id="dtEstadoRegistros">' +
                            '<thead>' +
                                '<tr>' +
                                    '<th>Identidad</th>' +
                                    '<th>Nombre Completo</th>' +
                                    '<th>Estado Ingreso</th>' +
                                '</tr>' +
                            '</thead>' +
                            '<tbody>';
                        
                        // Recorriendo el array 'estados' del JSON recibido
                        $.each(response.ingresos.estados, function(index, ingresos) {
                            var estadoIngresoIcono = '';
                            
                            // Asignando íconos dependiendo del estadoIngreso
                            switch (ingresos.estadoIngreso) {
                                case 'Ya existe en la misma empresa':
                                    estadoIngresoIcono = '<i class="ri-arrow-right-up-fill py-4"></i>';
                                    break;
                                case 'Ingreso nuevo':
                                    estadoIngresoIcono = '<i class="ri-arrow-up-fill py-4"></i>';
                                    break;
                                case 'Registro en otra compañia':
                                    estadoIngresoIcono = '<i class="ri-arrow-right-fill py-4"></i>';
                                    break;
                                default:
                                    estadoIngresoIcono = '';
                            }
                
                            // Generando las filas de la tabla con los datos
                            tablaHTML += '<tr>' +
                                '<td>' + ingresos.identidad + '</td>' +
                                '<td>' + ingresos.nombre + '</td>' +
                                '<td>' + estadoIngresoIcono + ingresos.estadoIngreso + '</td>' +
                            '</tr>';
                        });
                
                        tablaHTML += '</tbody></table>';
                
                        importInputCandidate.hide();
                        Swal.fire({
                            title: 'Estado de Ingresos',
                            html: tablaHTML,
                            didOpen: () => {
                                // Inicializando DataTable en el contenido de la tabla
                                $('#dtEstadoRegistros').DataTable();
                            },
                            width: '80%',
                            allowOutsideClick: false
                        });
                    }
                },
                
                error: function(xhr, status, error) {
                    var tipoError = xhr.responseJSON.tipoError;
                    if(tipoError==='exception')
                    {
                        Swal.fire(
                            {
                                title:'Error en el registro',
                                text:xhr.responseJSON.error,
                                icon:'error'
                            }
                        )
                    }else{


                    var tablaHTML = '<table id="dtEstadoRegistros" class="display">' +
                        '<thead>' +
                            '<tr>' +
                                '<th># Linea</th>' +
                                '<th>Identidad</th>' +
                                '<th>Nombre</th>' +
                                (tipoError === 'datos' ? '<th>Campos Faltantes</th>' : '<th>Observación</th>') +
                            '</tr>' +
                        '</thead>' +
                        '<tbody>';
    
                    if (tipoError === 'datos') {
                        // Errores de campos insuficientes
                        $.each(xhr.responseJSON.indices, function(index, ingreso) {
                            var camposFaltantes = '';
                            $.each(ingreso.campos_faltantes, function(key, value) {
                                camposFaltantes += '<li>' + value + '</li>';
                            });
                            camposFaltantes = '<ul>' + camposFaltantes + '</ul>';
    
                            tablaHTML += '<tr>' +
                                '<td>' + ingreso.LineNumber + '</td>' +
                                '<td>' + ingreso.identidad + '</td>' +
                                '<td>' + ingreso.nombre + '</td>' +
                                '<td>' + camposFaltantes + '</td>' +
                            '</tr>';
                        });
                    } else if (tipoError === 'fecha') {
                        // Errores de formato de fecha incorrecto
                        $.each(xhr.responseJSON.indice, function(index, registro) {
                            tablaHTML += '<tr>' +
                            '<td>' + registro.LineNumber + '</td>' +
                                '<td>' + registro.identidad + '</td>' +
                                '<td>' + registro.nombre + '</td>' +
                                '<td>' + registro.mensaje + '</td>' +
                            '</tr>';
                        });
                    }

                    
    
                    tablaHTML += '</tbody></table>';
    
                    $('#frmimportInputCandidate').hide();
    
                    Swal.fire({
                        title: tipoError === 'datos' ? 'Campos Faltantes' : 'Formato de Fecha Incorrecto',
                        html: tablaHTML,
                        didOpen: () => {
                            $('#dtEstadoRegistros').DataTable();
                        },
                        width: '80%',
                        allowOutsideClick: false
                    }).then((isOk) => {
                        if (isOk) {
                            location.reload();
                        }
                    });

                    }//final else
                }
            });
        });
    }
    


//seleccion de colaboradores que tendra un egreso en la empresa.
var egresosNuevos=[];
var selectOutput=$('.selectOutput')



if(selectOutput!=null)
{
    $(document).on('change','.selectOutput',function(){
        $(this).each(function(){
            var identidad = $(this).val();  // Obtener el valor del checkbox seleccionado

            if ($(this).is(':checked')) {
                // Checkbox seleccionado, agregar a egresosNuevos si está inactivo
                egresosNuevos.push(identidad);
                console.log(egresosNuevos);
            } else {
                // Checkbox deseleccionado, remover de egresosNuevos si existe
                var index = egresosNuevos.indexOf(identidad);
                if (index !== -1) {
                    egresosNuevos.splice(index, 1);
                }
            }
        })
       
    })

    var btnEgresoMasivo=$('#btnEgresoMasivo')
    $(document).on('click','#btnEgresoMasivo',function(e){
        e.preventDefault()
        
        /**
         * se cambio la ruta de import-data-output por export-data-output 19/05/2024
         */
        $.ajax({
            url:'/export-data-output',
            type:'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF en los headers si es necesario
            },
            contentType: 'application/json',
            data:JSON.stringify({ 'egresosNuevos': egresosNuevos }),
            xhrFields:{
                responseType: 'blob'
            },
            success:function(res,status,xhr){
               var contentType=xhr.getResponseHeader('content-type')
               if (contentType && contentType.indexOf('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') !== -1)
                 {
                // Crear un objeto URL para el blob y crear un enlace de descarga
                var blob = new Blob([res], { type: contentType });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'exportacion_egresos.xlsx';
                a.click();

                // Liberar el objeto URL después de la descarga
                URL.revokeObjectURL(url);
            } else {
                console.log(res.status)
                if(xhr.status==200)
                {
                    Swal.fire({
                        title:'Estado de autorizacion',
                        text:res.error,
                        icon:'warning'
                    })
                }
                console.error('Error: Tipo de contenido no compatible para descarga');
            }
            }
        })
        
    })
    
}

if(document.getElementById("importOut"))
{
var modalimportOut=new Modal('#importOut')
$(document).on('submit','#frmImportOut',function(e){
    e.preventDefault()
    var inputCSVimport=$('#egresos_csv')[0].files[0]
    var formCSV=new FormData();
   
    
    formCSV.append('archivo_csv',inputCSVimport);
    $.ajax({
        url:'/import-data-output',
        type:'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF en los headers si es necesario
        },
        data:formCSV,
        processData: false,
        contentType: false,
        
        success: function(response){
            if(response.status===202)
                {
                    modalimportOut.hide()
                    Swal.fire({
                        title:'Estado de actualizacion',
                        icon:response.icon,
                        text:response.success
                    }).then((resultado)=>{
                        if(resultado.isConfirmed)
                        {
                            location.reload();
                        }
                    })
                    
                }else{
                    modalimportOut.hide()
                    Swal.fire({
                        title:'Estado de actualizacion',
                        icon:response.icon,
                        text:response.error
                    })
                    
                }
        },
    })
})
}





   
