<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Candidatos;
class CandidateImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        
       foreach($collection as $data)
       {
        dd($data[1]);
        //Candidatos::create([$data]);
       }
        
    }
}
