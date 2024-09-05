import jQuery, { data } from 'jquery';
import DataTable from 'datatables.net-dt';
import Swal from 'sweetalert2';
var tbempresas=new DataTable('#tbempresas')
if($('#insertCompany')!=null)
{
    $('#insertCompany').on('submit',function(e){
        e.preventDefault();
        var formInsertCompany=new FormData($(this)[0])
//
        $.ajax({
            url:'/insert-company',
            type:'POST',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:formInsertCompany,
            processData:false,
            contentType:false,
            success:function(res){
                if(res.code===202)
                {
                    
                    let buttonUpdate=` <button type="button" class="btn btn-warning btn-consulta" data-id="`+res.data.id+`" data-bs-toggle="modal" data-bs-target="#modificarempresa" >Modificar</button>`
                    let empresaActiva=`<div class="form-check form-switch">
                    <input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                    <label class="form-check-label" for="flexSwitchCheckChecked">activo</label>
                  </div>`
                  let empresaInactiva=`<div class="form-check form-switch">
                  <input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="flexSwitchCheckChecked" >
                  <label class="form-check-label" for="flexSwitchCheckChecked">inactivo</label>
                </div>`

                let estado=res.data.estado=='a'?empresaActiva:empresaInactiva
                    tbempresas.row.add(
                        [
                            res.data.id,
                            res.data.nombre,
                            res.data.telefonos,
                            res.data.contacto,
                            res.data.correo,
                            estado,
                            buttonUpdate
                        ]//
                    ).draw(false)
                   
                }
            }
        })
    })
}


if(document.querySelectorAll('.chkactivoEmpresa')!==null && document.querySelectorAll('.form-check-label')!==null){
    const checkboxes = document.querySelectorAll('.chkactivoEmpresa');
    const labels = document.querySelectorAll('.form-check-label');
    
     // Iterar sobre cada checkbox
     checkboxes.forEach((checkbox, index) => {
        // Agregar un event listener para detectar cambios en el checkbox
        checkbox.addEventListener("change", function() {//
            const idrow=checkbox.closest('tr');
            let state='';
            let idCompany=0;
          // Verificar el estado del checkbox y cambiar el texto del label en consecuencia
          if (this.checked) {
            state='a';
            idCompany=this.id.split('-')[1];
            

            labels[index].textContent = "Activo";
          } else {
            state='n';
            idCompany=this.id.split('-')[1];
            labels[index].textContent = "Inactivo";
          }

          const data={
            state:state,
            idCompany:idCompany
        }

         console.log( updateStateCompany(data)) ;

        });
      });
    }


    async function updateStateCompany(data) {
        try {
            const response = await fetch('/update-company-state', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(data)
            });
    
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
    
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('There was a problem with the fetch operation:', error);
        }
    }

$(document).on('click','.btn-consulta',function() {
    var id = $(this).data('id');
    var nombre=$('#modnombre')
    var telefono=$('#modtelefonos')
    var correo=$('#modcorreo')
    var contacto=$('#modcontacto')
    var direccion=$('#moddireccion')
    var id_empresa=$('#id_empresa')
    console.log(id);
    $.ajax({
        url: '/consulting-company/'+id,
        type: 'GET',
        beforeSend:function(){
            $('#estado').html('')
        },
        //data: { id: id },
        success: function(response) {
            console.log(response)
            // Manejar la respuesta de la consulta AJAX aquí
            console.log(response.data[0].nombre);
            nombre.val(response.data[0].nombre)
            telefono.val(formatearNumero(response.data[0].telefonos))
            correo.val(response.data[0].correo)
            contacto.val(response.data[0].contacto)
            direccion.val(response.data[0].direccion)
            id_empresa.val(id)
            var checked=''
            if(response.data[0].estado==='a')
            {
                checked='checked';
            }
            $('#estado').html(' <div class="form-check form-switch">'+
                '<input class="form-check-input chkactivoEmpresa" type="checkbox" role="switch" id="estado" name="estado" '+checked+'>'+
                '<label class="form-check-label" for="flexSwitchCheckChecked">activo</label>'+
              '</div>')
        },
        error: function(xhr, status, error) {
            // Manejar errores aquí
            console.error(xhr.responseText);
        }
    });
});

function formatearNumero(numero)
{
    return numero.replace(/(\d{4})(\d{4})/, '$1-$2')
}




if($('#updateCompany')!=null)
{
    $('#updateCompany').on('submit',function(e){
        e.preventDefault();
        var formUpdateCompany=new FormData($(this)[0])

        $.ajax({
            url:'/update-company',
            type:'POST',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:formUpdateCompany,
            processData:false,
            contentType:false,
           
            success:function(res){
                switch (res.code) {
                    case 202:
                        Swal.fire({
                            title:'Registro actualizado!',
                            text:res.mensaje,
                            icon:'success'
                        })

                            // Actualizar fila en el DataTable
                        var idFila = res.data.id; // Supongamos que 'res.id' es el identificador único de la fila actualizada
                        var nuevaData = res.data; // Supongamos que 'res.nuevaData' contiene los nuevos datos de la fila

                        // Encuentra la fila correspondiente en el DataTable
                        var fila = $('#tbempresas').find('tr[data-id="' + idFila + '"]');
                        if (fila.length > 0) {
                            // Actualiza los datos de la fila
                            fila.find('td').each(function(index) {
                                var campo = $(this).data('campo'); // Supongamos que 'data-campo' contiene el nombre del campo en la fila
                                $(this).text(nuevaData[campo]); // Actualiza el contenido del campo en la fila
                            });
                        }
                                    
                                    
                        break;
                    case 500:
                        Swal.fire({
                            title:'Error en la actualizacion!',
                            text:res.mensaje,
                            icon:'success'
                        }) 
                    break;
                    default:
                        Swal.fire({
                            title:'Registro actualizado!',
                            text:res.mensaje,
                            icon:'success'
                        })
                        break;
                }
                    
                   
                
            }
        })
    })
}
