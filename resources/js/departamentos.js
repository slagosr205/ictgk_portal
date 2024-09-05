import jQuery, { data } from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons-dt';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';



var tbdepartamento=$('#tbdepartamentos').DataTable({});
if($('#insertDepartamento')!=null)
{
    var insertDepartament=$('#insertDepartamento')

    insertDepartament.on('submit',function(e){
        e.preventDefault()
        var formInsertDepartament=new FormData($(this)[0])
        formInsertDepartament.append('empresa_id',$('[name="empresa_id"]').attr('id'))
        $.ajax({
            url:'/insert-departament',
            type:'POST',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:formInsertDepartament,
            processData:false,
            contentType:false,
            success:function(res){
                if(res.code===202)
                {
                    
                   
                    tbdepartamento.row.add(
                        [
                            res.data.id,
                            res.data.nombredepartamento,
                            res.data.empresa_nombre,
                            res.data.created_at,
                            res.data.updated_at,
                            '',
                        ]
                    ).draw(false)
                   Swal.fire({
                    title:'Estado de la creación',
                    html:'<p>Se ha creado el departamento</p> <strong>'+res.data.nombredepartamento+'</strong>',
                    icon:'success',
                    allowOutsideClick:false,
                   }).then((resultado)=>{
                    if(resultado.isConfirmed)
                    {
                        location.reload();
                    }
                })
                }
            }
        })
    })
    
}


$(document).on('click','.btnInfoDepto',function(e){

    var actualizardepartamento=new Modal('#actualizardepartamento')
    var nombredepartamentoactual=$('#nombredepartamentoactual')
    var updatedepartamento_id=$('#updatedepartamento_id')
    var departamento_id=$(this).attr('id')
    $.ajax({
        url:'/consulting-departament/'+departamento_id,
        type:'GET',
       
        success:function(res){
            console.log(res)
            updatedepartamento_id.val(departamento_id);
            nombredepartamentoactual.val(res.departamento[0].nombredepartamento)
            actualizardepartamento.show();

        }
    })
})


$(document).on('submit','#updateDepartamento',function(e){

    e.preventDefault();

    var nombredepartamentoactual2=$('#nombredepartamentoactual')
    var updatedepartamento_id2=$('#updatedepartamento_id')
    $.ajax({
        url:'/update-departament',
        type:'POST',
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{departamento_id:updatedepartamento_id2.val(),nombredepartamento:nombredepartamentoactual2.val()},
        
        success:function(res){
            
            if(res.status===200)
            {
                Swal.fire({
                    title:'Estado de actualización',
                    text:res.success,
                    icon:'success',
                    allowOutsideClick:false
                }).then((resultado)=>{
                    if(resultado.isConfirmed)
                    {
                        location.reload();
                    }
                })

            }
            console.log(res);
        }
    })

})
