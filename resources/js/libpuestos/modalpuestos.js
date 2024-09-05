import jQuery, { data } from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';


var modalpuesto=new Modal('#modalagrPuesto')
var mensajeerrors=document.getElementById('errorsPositions').innerHTML;
var mensajeerrorspuesto=$('#mensajeerrorspuesto')
console.log(mensajeerrors)
mensajeerrorspuesto.html(mensajeerrors);
modalpuesto.show();