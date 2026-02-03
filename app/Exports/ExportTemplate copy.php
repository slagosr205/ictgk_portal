<?php

namespace App\Exports;

use App\Models\PuestosModel;
use App\Models\Ingresos;
use Carbon\Traits\ToStringFormat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithBackgroundColor;
class ExportTemplate implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // Hoja 1: Encabezados de las columnas
        $sheets[] = new ColumnHeadersSheet();

        // Hoja 2: Datos de los puestos
        $sheets[] = new PuestosDataSheet();

        return $sheets;
    }



    
}
// columnas del template
class ColumnHeadersSheet implements FromCollection, WithHeadings, ShouldAutoSize,WithColumnFormatting, WithEvents,WithStrictNullComparison,WithStyles
{
    
    public function collection(): Collection
    {
        $tableName = (new Ingresos())->getTable();
        $columnHeaders = Schema::getColumnListing($tableName);

        $numberPositions = PuestosModel::select('puestos.id','puestos.nombrepuesto','departamentos.nombredepartamento')
        ->join('departamentos','puestos.departamento_id','=','departamentos.id')
        ->join('empresas','departamentos.empresa_id','=','empresas.id')
        ->where('empresas.id',auth()->user()->empresa_id)
        ->get()->count()+1;

        // Simular datos para la hoja (puedes ajustar esto según tus necesidades)
        $formula=collect();

        for ($i=2; $i <= 10; $i++) { 
            # code...
            $formula->push('=VLOOKUP(E'.$i.', \'Worksheet 1\'!A1:B'.$numberPositions.', 2, 0)');
        }
       // dd($formula);
        // Obtener la cantidad de columnas
        $columnCount = count($columnHeaders);

        // Agregar los datos a la última columna en la hoja de encabezados
        foreach ($formula as $datum) {
            $rowData = array_fill(0, $columnCount, ''); // Crear un array vacío con la misma cantidad de columnas
            $rowData[$columnCount-11] = $datum; // Agregar el dato en la última columna
            $rows[] = $rowData;
        }

       

        return collect($rows);
    }

    public function headings(): array
    {
        $headerElementRemove=['id',
                                'fechaEgreso',
                                'activo',
                                'tipo_egreso',
                                'forma_egreso',
                                'recomendado',
                                'bloqueo_recomendado',
                                'prohibirIngreso',
                                'ComenProhibir',
                                'created_at',
                                'updated_at'
                            ];
        $tableName = (new Ingresos())->getTable();
        $columnHeaders = Schema::getColumnListing($tableName);
        $trimcolumnHeaders=array_diff($columnHeaders,$headerElementRemove);
        $trimcolumnHeaders['formula']='validacion';
        $trimcolumnHeaders['nombre']='nombre';
        $trimcolumnHeaders['apellido']='apellido';
        $trimcolumnHeaders['telefono']='telefono';
        $trimcolumnHeaders['correo']='correo';
        $trimcolumnHeaders['direccion']='direccion';
        $trimcolumnHeaders['generoM_F']='generoM_F';
        $trimcolumnHeaders['fecha_nacimiento']='fecha_nacimiento';
        return $trimcolumnHeaders;
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Formato de fecha para columna 'C'
            'A' =>NumberFormat::FORMAT_TEXT,
            
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1=>['font'=>['bold'=>true],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => [
                            'rgb' => '50195108'
                        ]
                    ],
                ]
        ],
            
        ];
    }
    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {
                $tableName = (new Ingresos())->getTable();
                $columnHeaders = Schema::getColumnListing($tableName);
                // get layout counts (add 1 to rows for heading row)
                $row_count = 200;
                $column_count = count($columnHeaders);

                // set dropdown column
                $drop_column = 'D';

                // set dropdown options
                $options = [
                    'administrativa',
                    'operativa',
                ];
                $id_empresa=auth()->user()->empresa_id;
                $columnEmpresa='B';
                $columanIndentidad='P';
                $drop_column_genero='M';
                
                $optionGenero=[
                    'm',
                    'f'
                ];
                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST );
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                $validation->setFormula1(sprintf('"%s"',implode(',',$options)));

                // set dropdown list for first data row
                $validation2 = $event->sheet->getCell("{$drop_column_genero}2")->getDataValidation();
                $validation2->setType(DataValidation::TYPE_LIST );
                $validation2->setErrorStyle(DataValidation::STYLE_INFORMATION );
                $validation2->setAllowBlank(false);
                $validation2->setShowInputMessage(true);
                $validation2->setShowErrorMessage(true);
                $validation2->setShowDropDown(true);
                $validation2->setErrorTitle('Input error');
                $validation2->setError('Value is not in list.');
                $validation2->setPromptTitle('Pick from list');
                $validation2->setPrompt('Please pick a value from the drop-down list.');
                $validation2->setFormula1(sprintf('"%s"',implode(',',$optionGenero)));
                for ($i = 2; $i <= 10; $i++) 
                {
                    $event->sheet->getCell("{$columnEmpresa}{$i}")->setValue($id_empresa);
                   // $event->sheet->getCell("{$columanIndentidad}{$i}")->setValue('=ISNUMBER(SEARCH("-", B{$i}))');
                    
                }
                
                // clone validation to remaining rows
                for ($i = 3; $i <= $row_count; $i++) {
                    
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    $event->sheet->getCell("{$drop_column_genero}{$i}")->setDataValidation(clone $validation2); // Clonar validación para la nueva columna
                
                }

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

}



// Hoja para los datos de los puestos
class PuestosDataSheet implements FromArray, WithHeadings
{
    public function array(): array
    {
        $puestos = PuestosModel::select('puestos.id','puestos.nombrepuesto','departamentos.nombredepartamento')
        ->join('departamentos','puestos.departamento_id','=','departamentos.id')
        ->join('empresas','departamentos.empresa_id','=','empresas.id')
        ->where('empresas.id',auth()->user()->empresa_id)
        ->get()
        ->toArray();
        
        return $puestos;
    }

    public function headings(): array
    {

        return ['ID PUESTO','NOMBRE PUESTO','DEPARTAMENTO']; 
    }
}


// Hoja para los datos de los puestos


