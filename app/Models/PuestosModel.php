<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuestosModel extends Model
{
    use HasFactory;
    protected $table='puestos';
    protected $fillable = [
        'nombrepuesto',
        'departamento_id',
        
    ];

    public function departamento()
    {
        return $this->belongsTo(DepartamentosModel::class,'departamento_id','id');
    }
}
