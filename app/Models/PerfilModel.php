<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilModel extends Model
{
    use HasFactory;
    protected $table='perfiles';
    protected $fillable = [
        'perfilesdescrip',
        'ingreso',
        'egreso',
        'bloqueocolaborador',
        'gestiontablas',
        'visualizarinformes'

    ];
}
