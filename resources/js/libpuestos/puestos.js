import jQuery, { data } from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

var tbpuestos=$('#tbpuestos').DataTable({
   
})


var btnActPuestos=$('.btnActPuestos')



if(btnActPuestos!=null)
{
    $(document).on('click','.btnActPuestos',function(){
        let id=$(this).val();
        console.log('el valor es '+id)
        let puestonombre=document.getElementById('puestonombre')
        let departamentonombre=document.getElementById('departamentonombre')
        let puesto_id=document.getElementById('puesto_id')
        let departamento_id=document.getElementById('departamento_id')
        puestonombre.value='';
        departamentonombre.value='';
        fetch('/consulta-puesto/'+id)
        .then(response=>response.json())
        .then(
            data=>{
            console.log(data[0][0].nombrepuesto)

            puestonombre.value=data[0][0].nombrepuesto
            departamentonombre.value=data[0][0].nombredepartamento
            puesto_id.value=data[0][0].id
            departamento_id.value=data[0][0].departamento_id
        }
        )
    })
}


var notificationSuccess = $('#updatedPositions');
var notificationupdatedPositionserror=$('#updatedPositionserror');
// Verificar si el elemento existe y está visible
if (notificationSuccess.length > 0) {
    // Ocultar la notificación después de 5 segundos (5000 milisegundos)
    setTimeout(function() {
        notificationSuccess.fadeOut(); // Opción de animación de desvanecimiento
        // notification.hide(); // Otra opción de ocultar inmediatamente sin animación
    }, 3000); // Cambia el tiempo en milisegundos (ej. 5000 = 5 segundos)
}

if (notificationupdatedPositionserror.length > 0) {
    // Ocultar la notificación después de 5 segundos (5000 milisegundos)
    setTimeout(function() {
        notificationupdatedPositionserror.fadeOut(); // Opción de animación de desvanecimiento
        // notification.hide(); // Otra opción de ocultar inmediatamente sin animación
    }, 3000); // Cambia el tiempo en milisegundos (ej. 5000 = 5 segundos)
}






