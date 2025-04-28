<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
class Candidatos extends Model
{
    use HasFactory;
    protected $fillable = [
        'identidad',
        'nombre',
        'apellido',
        'telefono',
        'correo',
        'direccion',
        'generoM_F',
        'fecha_nacimiento',
        'activo',
        'comentarios',
    ];


    public function ingresos()
    {
        return $this->hasMany(Ingresos::class,'identidad','identidad');
    }

    public function getFechaNacimientoAttribute($date)
    {
        // Crea un objeto Date a partir del valor de la fecha
        $fecha = new Date($date);

        // Establece el idioma de formato a español
        $fecha->setLocale('es');

        // Formatea la fecha en el formato deseado
        return $fecha->format('l j F Y'); // Formato personalizado en español
    }

    

    public function getCreatedAtAttribute($date)
    {
        // Crea un objeto Date a partir del valor de la fecha
        $fecha = new Date($date);

        // Establece el idioma de formato a español
        $fecha->setLocale('es');

        // Formatea la fecha en el formato deseado
        return $fecha->format('l j F Y'); // Formato personalizado en español
    }

    public function getUpdatedAtAttribute($date)
    {
        // Crea un objeto Date a partir del valor de la fecha
        $fecha = new Date($date);

        // Establece el idioma de formato a español
        $fecha->setLocale('es');

        // Formatea la fecha en el formato deseado
        return $fecha->format('l j F Y'); // Formato personalizado en español
    }
    
}
