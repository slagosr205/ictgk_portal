<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartamentosModel extends Model
{
    use HasFactory;
    protected $table='departamentos';
    protected $fillable = [
        'nombredepartamento',
        'empresa_id',
        
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresas::class,'empresa_id','id');
    }
}
