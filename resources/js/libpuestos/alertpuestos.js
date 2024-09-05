import jQuery, { data } from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';


  
    // Ocultar la alerta después de 3 segundos (3000 milisegundos)
    /*setTimeout(function() {
        $('#successPositions').fadeOut();
    }, 1500); // 3000 milisegundos = 3 segundos*/

    Swal.fire({
        title:'Registro agregado!',
        text: $('#successPositions').text(),
        icon:'success'
    })