<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PuestoEmpresaRule implements Rule
{
    protected $empresaId;

    public function __construct(?int $empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->empresaId || !$value) {
            return false;
        }

        return DB::table('puestos as p')
            ->join('departamentos as d', 'd.id', '=', 'p.departamento_id')
            ->where('p.id', $value)
            ->where('d.empresa_id', $this->empresaId)
            ->exists();
    }

    public function message(): string
    {
        return 'El puesto no pertenece a la empresa seleccionada';
    }
}