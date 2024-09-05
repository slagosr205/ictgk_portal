<?php

namespace App\Exports;

use App\Models\PuestosModel;
use App\Models\Candidatos;
use Carbon\Traits\ToStringFormat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
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

class ExportTemplateCandidate implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // Hoja 1: Encabezados de las columnas
        $sheets[] = new ColumnHeadersSheet();

        // Hoja 2: Datos de los puestos
       // $sheets[] = new PuestosDataSheet();

        return $sheets;
    }



    
}

// columnas del template
class ColumnHeadersSheet implements FromCollection, WithHeadings, ShouldAutoSize,WithColumnFormatting, WithEvents,WithStrictNullComparison,WithStyles,WithColumnWidths
{
    public $optionGenero;

    public function __construct()
    {
        $this->optionGenero=[
            'm',
            'f'
        ];
    }
    
   public function collection(): Collection
    {
        $tableName = (new Candidatos())->getTable();
        $columnHeaders = Schema::getColumnListing($tableName);

        // Simular datos para la hoja (puedes ajustar esto según tus necesidades)
        $formula=collect();

        for ($i=2; $i <= 10; $i++) { 
            # code...
            $formula->push('');
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
    {//
       $notAllowedColumns=['id','created_at','updated_at','activo'];

        $tableName = (new Candidatos())->getTable();
        $columnHeaders = Schema::getColumnListing($tableName);
        $trimColumnRemove=array_diff($columnHeaders,$notAllowedColumns);
        return $trimColumnRemove;
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Formato de fecha para columna 'C'
            'A' =>NumberFormat::FORMAT_TEXT,
            
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'=>55,
            'B'=>200,
            'C'=>45,
            'D'=>45,
            'E'=>45,
            'F'=>45,
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
                $tableName = (new Candidatos())->getTable();
                $columnHeaders = Schema::getColumnListing($tableName);
                // get layout counts (add 1 to rows for heading row)
                $row_count = 200;
                $column_count = count($columnHeaders);

              

                
                $id_empresa=auth()->user()->empresa_id;
                $columnEmpresa='B';
                $columanIndentidad='P';
                $drop_column_genero='G';
                
               
             

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
                
                $validation2->setFormula1(sprintf('"%s"',implode(',',$this->optionGenero)));
                for ($i = 2; $i <= 10; $i++) 
                {
                   // $event->sheet->getCell("{$columnEmpresa}{$i}")->setValue($id_empresa);
                    $event->sheet->getCell($columanIndentidad.$i)->setValue('=ISNUMBER(SEARCH("-", B{$i}))');
                    
                }
                
                // clone validation to remaining rows
                for ($i = 3; $i <= $row_count; $i++) {
                    
                   
                    $event->sheet->getCell($drop_column_genero.$i)->setDataValidation(clone $validation2); // Clonar validación para la nueva columna
                
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
