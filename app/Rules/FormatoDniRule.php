<?php
// app/Rules/FormatoDniRule.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FormatoDniRule implements Rule
{
    protected $mensaje = 'El formato de la identidad no es válido';

    public function passes($attribute, $value): bool
    {
        // Verificar que no esté vacío
        if (empty($value)) {
            return false;
        }

        // Limpiar cualquier caracter no numérico
        $soloNumeros = preg_replace('/[^0-9]/', '', $value);
        
        // Debe tener exactamente 13 dígitos
        if (strlen($soloNumeros) !== 13) {
            $this->mensaje = 'La identidad debe tener 13 dígitos';
            return false;
        }

        // Validar que todos sean números
        if (!ctype_digit($soloNumeros)) {
            $this->mensaje = 'La identidad solo puede contener números';
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->mensaje;
    }
}