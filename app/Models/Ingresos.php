<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingresos extends Model
{
    use HasFactory;
    
    protected $table = 'egresos_ingresos';

    protected $fillable = [
        'identidad',
        'id_empresa',
        'fechaIngreso',
        'fechaEgreso',
        'area',
        'id_puesto',
        'activo',
        'Comentario',
        'validacion',
        'recomendado',
        'tipo_egreso',
        'forma_egreso',
        // Agregar otros campos si existen en la tabla
    ];
     
    public function candidato()
    {
        return $this->belongsTo(Candidatos::class, 'identidad', 'identidad');
    }

    public function puesto()
    {
        return $this->belongsTo(PuestosModel::class, 'id_puesto', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresas::class, 'id_empresa', 'id');
    }
}