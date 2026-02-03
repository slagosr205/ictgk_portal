<?php
// app/Rules/DniUnicoRule.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Candidatos;

class DniUnicoRule implements Rule
{
    protected $identidadExcluida;
    protected $soloAdvertencia;

    /**
     * Create a new rule instance.
     *
     * @param string|null $identidadExcluida Identidad a excluir de la validación
     * @param bool $soloAdvertencia Si es true, no falla la validación
     */
    public function __construct(?string $identidadExcluida = null, bool $soloAdvertencia = false)
    {
        // Normalizar la identidad excluida (sin guiones)
        $this->identidadExcluida = $identidadExcluida 
            ? preg_replace('/[^0-9]/', '', $identidadExcluida) 
            : null;
        $this->soloAdvertencia = $soloAdvertencia;
    }

    public function passes($attribute, $value): bool
    {
        // Si está vacío, dejar que required lo maneje
        if (empty($value)) {
            return true;
        }

        // Normalizar el valor (sin guiones)
        $valorNormalizado = preg_replace('/[^0-9]/', '', $value);

        $query = Candidatos::where('identidad', $valorNormalizado);

        // Excluir una identidad específica (útil para edición)
        if ($this->identidadExcluida) {
            $query->where('identidad', '!=', $this->identidadExcluida);
        }

        $existe = $query->exists();

        // Si es solo advertencia, siempre pasa
        if ($this->soloAdvertencia) {
            return true;
        }

        // Si existe, la validación falla
        return !$existe;
    }

    public function message(): string
    {
        return 'La identidad ya está registrada en el sistema';
    }
}