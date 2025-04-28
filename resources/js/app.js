import './bootstrap';

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

var dtUsuarios=new DataTable('#dtUsuarios')
//var tbhistoricoempresa=new DataTable('#tbhistoricoempresa');
if(document.getElementById('mensaje')){
    let mensaje=document.getElementById('mensaje')
    Swal.fire({
        title: "Ejecutado",
        text: mensaje.innerHTML,
        
        icon: "success"
      });
}

/*if(document.getElementById('mensajeerror')){
    let mensajeerror=document.getElementById('mensajeerror')
    Swal.fire({
        title: "Ejecutado",
        text: mensajeerror.innerHTML,
        html:`
        <table>
          <thead>
            <tr>
              <th>Columna 1</th>
              <th>Columna 2</th>
              <th>Columna 3</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Dato 1</td>
              <td>Dato 2</td>
              <td>Dato 3</td>
            </tr>
            <tr>
              <td>Dato 4</td>
              <td>Dato 5</td>
              <td>Dato 6</td>
            </tr>
            <!-- Puedes agregar más filas según tus datos -->
          </tbody>
        </table>
      `,
        icon: "warning"
      });
}*/



/*ClassicEditor
            .create( document.querySelector( '#comentarios' ) )
            .catch( error => {
            console.error( error );
            } );
*/
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

  //obtener menu
 /* fetch("/generar-menu")
  .then(response=>response.json())
.then(data=>{
    var areamenu=document.getElementById("menudinamico")
    /*for(let i=0;i<data.menu.length;i++){
        areamenu.innerHTML+='<a href="#" class="nav-item nav-link">'+data.menu[i].name+'</a>';
    }*/
    
//})

var btnshowElementImport=document.getElementById('showElementImport')
var elementImport=document.getElementsByClassName('d-none')
/*btnshowElementImport.addEventListener("click",function(e){
   if(elementImport){
    elementImport[0].classList.remove('d-none')
   }else{
    elementImport[0].classList.addClass('d-none')
   }
})*/

var cardInfoID=document.getElementsByClassName('card2');

/*for(let i=0;i<cardInfoID.length;i++){
    cardInfoID[i]
}*/

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






if(document.getElementById('btndni')!==null){
var btnDNI=document.getElementById('btndni')

btnDNI.addEventListener('click',function(e){
    e.preventDefault();
    var dni=document.getElementById('dni')
    var fichapersonal=document.getElementById('fichapersonal');

    $.ajax({
        url:'/infopersonal/'+dni.value,
        type:'GET',

        //data:{identidad:idElemento},
        success:function(res){
            console.log(res);
            if(res.code!=400){
                
                fichapersonal.innerHTML=res;
                    btnupdate=document.querySelectorAll('.btnupdate')
                    var tbhistoricoempresa=new DataTable('#tbhistoricoempresa')
                    btnupdate.forEach(function(button) {
                        button.addEventListener('click', function() {
                            // Obtener el ID del botón clicado
                            var telefono=document.getElementById('telefono')
                            var correo=document.getElementById('correo')
                            var direccion=document.getElementById('direccion')
                            var id = this.id;
                            ActualizaFicha(correo.value,telefono.value,direccion.value,id) 
                        });
                    });
                
            }else{
                Swal.fire({
                    title:res.response,
                    icon:'info',
                    showCancelButton:true,
                    confirmButtonText:'Desea agregar el registro?',
                }).then((result)=>{
                    if(result.isConfirmed){
                       // fichapersonal.innerHTML='<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerCandidate" >Crear nuevo registro</button>'
                        var modal=new Modal('#registerCandidate')
                        modal.show()
                    }
                })
            }
            
        }
        
    })

})

}

if(document.getElementById('dni')!==null){
var dni=document.getElementById('dni');

dni.addEventListener('keypress',function(e){
    var id=e.target.value
    
    if(e.key=='Enter')
    {
        $.ajax({
            url:'/infopersonal/'+id,
            type:'GET',
            //data:{identidad:idElemento},
            success:function(res){
                if(res.code!=400){
                    fichapersonal.innerHTML=res;
                    btnupdate=document.querySelectorAll('.btnupdate')
                    var tbhistoricoempresa=new DataTable('#tbhistoricoempresa')
                    btnupdate.forEach(function(button) {
                        button.addEventListener('click', function() {
                            // Obtener el ID del botón clicado
                            var telefono=document.getElementById('telefono')
                            var correo=document.getElementById('correo')
                            var direccion=document.getElementById('direccion')
                            var id = this.id;
                            ActualizaFicha(correo.value,telefono.value,direccion.value,id) 
                        });
                    });
                
                  
                }else{
                    Swal.fire({
                        title:res.response,
                        icon:'info',
                        showCancelButton:true,
                        confirmButtonText:'Desea agregar el registro?',
                    }).then((result)=>{
                        if(result.isConfirmed){
                           // fichapersonal.innerHTML='<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerCandidate" >Crear nuevo registro</button>'
                            var modal=new Modal('#registerCandidate')
                            modal.show()
                        }
                    })
                }
                
            }
        })
    }
})
}
//cuando se dispare el evento, se ejecutar la actualizacion del registro

    // Obtener todos los elementos con la clase "btnupdate"
    // Agregar un controlador de eventos de clic a cada botón
   

function ActualizaFicha(correo, telefono, direccion,id)
{
    $.ajax({
        url:'/actualizacion-ficha/',
        type:'POST',
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{id:id,correo:correo,telefono:telefono,direccion:direccion},
        success:function(res){
            console.log(res);
            Swal.fire({
                title:res.titulo,
                text:res.mensaje,
                icon:res.icon
            })
        },
        error:function(error){
            console.error(error);
        }
    })
}



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
const modalidentidad=document.getElementById('modalidentidad')
const lockidentidad=document.getElementById('lockidentidad')
    // Iterar sobre cada botón y agregar un evento de clic a cada uno
    document.addEventListener('click', function(event) {
        const identidad = event.target.value;
        
        if (event.target.classList.contains('btndesbloqueo')) {
            
            modalidentidad.value = identidad;
            console.log('Desbloquear candidato con identidad:', identidad);
        }else{
            if (event.target.classList.contains('btnbloqueo')) 
            {
                
                lockidentidad.value = identidad;
                console.log('Desbloquear candidato con identidad:', identidad);
            }
        }

        

    });

   /* var tooltipMsjRegNuevosCandidatos=$('.mensajeregistros')
    tooltipMsjRegNuevosCandidatos.each(function(){
        var tootltip=new Tooltip(this)
    })

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





   