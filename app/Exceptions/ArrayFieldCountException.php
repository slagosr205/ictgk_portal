<?php

namespace App\Exceptions;

use Exception;

class ArrayFieldCountException extends Exception
{
    //
    protected $indices;

    public function __construct(array $indices)
    {
        $this->indices = $indices;
        parent::__construct("Los siguientes subarrays tienen menos de 13 campos: " . implode(', ', $indices));
    }

    public function getIndices()
    {
        return $this->indices;
    }
}
