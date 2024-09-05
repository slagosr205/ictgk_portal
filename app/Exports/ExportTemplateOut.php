<?php

namespace App\Exports;

use App\Models\Egresos;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Schema;
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
use App\Models\Candidatos;


class ExportTemplateOut implements FromArray,WithHeadings, ShouldAutoSize,WithColumnFormatting, WithEvents,WithStrictNullComparison,WithStyles,WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $egresos;
    protected $formaEgreso;

    protected $tipoEgreso;

    protected $recomendado;
    public function __construct(array $egresos)
    {
        $this->egresos=$egresos;
        $this->formaEgreso=[
            'voluntario',
            'involuntario'
        ];

        $this->tipoEgreso=[
            'Abandono de Labores',
            'Conflictos de Horarios',
            'Motivo de estudios',
            'Nueva oportunidad laboral',
            'Enfermedad',
            'Bajo rendimiento',
            'otros'
        ];

        $this->recomendado=[
            'si',
            'no'
        ];


    }
    public function array(): array
    {
        $formattedData = array_map(function($row) {
            if (isset($row['identidad'])) {
                $row['identidad'] = utf8_encode(' '.$row['identidad']); // Convertir a cadena de texto
            }
            return $row;
        }, $this->egresos);

        return $formattedData;
    }
//
    public function headings(): array
    {
        $headerSheet=['id','nombre','identidad','fechaIngreso','fechaEgreso','forma_egreso','tipo_egreso','recomendado','comentario'];
        
        
        return $headerSheet;
    }

    public function columnWidths():array
    {
        return [
            'B'=>200,
            'C'=>45,
            'D'=>45,
            'E'=>45,
            'F'=>45,
            'G'=>45,
            'H'=>45,
            'I'=>200,
            

        ];
    }

    public function columnHeight():array
    {
        return [
            'I'=>60,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Formato de fecha para columna 'C'
            'A' =>NumberFormat::FORMAT_TEXT,
            'C'=>NumberFormat::FORMAT_TEXT,
        ];
    }

    public function map($row):array
    {
        return [
            'A' => "'".$row->column_a, // Convertir el número a texto agregando una comilla simple
            'C' => "'".$row->column_c, // Convertir el número a texto explícitamente
            'H' => Date::dateTimeToExcel($row->column_h), // Formatear la fecha correctamente
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

              

                $drop_column_tipoEgreso='G';
                $drop_column_formEgreso='F';
                $drop_column_recomendado='H';
                 
                
               
             

                // set dropdown list for first data row
                $validation2 = $event->sheet->getCell("{$drop_column_formEgreso}2")->getDataValidation();
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
                $validation2->setFormula1(sprintf('"%s"',implode(',',$this->formaEgreso)));
              /*  for ($i = 2; $i <= 10; $i++) 
                {
                   // $event->sheet->getCell("{$columnEmpresa}{$i}")->setValue($id_empresa);
                    $event->sheet->getCell($columanIndentidad.$i)->setValue('=ISNUMBER(SEARCH("-", B{$i}))');
                    
                }*/

                 // set dropdown list for first data row
                 $validation = $event->sheet->getCell("{$drop_column_tipoEgreso}2")->getDataValidation();
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
                 $validation->setFormula1(sprintf('"%s"',implode(',',$this->tipoEgreso)));

                 $validation3 = $event->sheet->getCell("{$drop_column_recomendado}2")->getDataValidation();
                $validation3->setType(DataValidation::TYPE_LIST );
                $validation3->setErrorStyle(DataValidation::STYLE_INFORMATION );
                $validation3->setAllowBlank(false);
                $validation3->setShowInputMessage(true);
                $validation3->setShowErrorMessage(true);
                $validation3->setShowDropDown(true);
                $validation3->setErrorTitle('Input error');
                $validation3->setError('Value is not in list.');
                $validation3->setPromptTitle('Pick from list');
                $validation3->setPrompt('Please pick a value from the drop-down list.');
                $validation3->setFormula1(sprintf('"%s"',implode(',',$this->recomendado)));
                
                // clone validation to remaining rows
                for ($i = 3; $i <= $row_count; $i++) {
                    
                   
                    $event->sheet->getCell($drop_column_formEgreso.$i)->setDataValidation(clone $validation2); // Clonar validación para la nueva 
                    $event->sheet->getCell($drop_column_tipoEgreso.$i)->setDataValidation(clone $validation); // Clonar validación para la nueva columna
                    $event->sheet->getCell($drop_column_recomendado.$i)->setDataValidation(clone $validation3); // Clonar validación para la nueva columna
                
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
