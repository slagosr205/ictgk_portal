<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Egresos extends Model
{
    use HasFactory;
    protected $table='egresos_ingresos';

    protected $fillable=[
        'identidad',
        'id_empresa',
        /*'fechaIngreso',*/
        'fechaEgreso',
        'area',
        /*'id_puesto',*/
        'activo',
        'tipo_egreso',
        'forma_egreso',
        'Comentario',
        'recomendado',
        'recontrataria',
        'prohibirIngreso',
        'ComenProhibir',
        
    ];

    public function candidatos()
    {
        return $this->belongsTo(Candidatos::class,'identidad','identidad');
    }

    public  function puesto()
    {
        return $this->belongsTo(PuestosModel::class,'id_puesto','id');
    }
}
