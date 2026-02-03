<?php

namespace App\Exports;

use App\Models\Empresas;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportTemplate implements WithMultipleSheets
{
    protected $cantidadFilas;

    public function __construct(int $cantidadFilas = 10)
    {
        $this->cantidadFilas = $cantidadFilas;
    }

    public function sheets(): array
    {
        return [
            new PlantillaIngresosSheet($this->cantidadFilas),  // ✅ PASAR EL PARÁMETRO
            new PuestosSheet(),
        ];
    }
}

class PlantillaIngresosSheet implements FromArray, WithHeadings, WithEvents, WithTitle
{
    private $empresa;
    private $puestos;
    private $cantidadFilas;

    public function __construct(int $cantidadFilas = 10)
    {
        $this->cantidadFilas = $cantidadFilas;
        
        $this->empresa = Empresas::select('id', 'nombre')
            ->find(auth()->user()->empresa_id);

        $this->puestos = DB::table('puestos')
            ->join('departamentos', 'puestos.departamento_id', '=', 'departamentos.id')
            ->where('departamentos.empresa_id', auth()->user()->empresa_id)
            ->select('puestos.id', 'puestos.nombrepuesto')
            ->orderBy('puestos.nombrepuesto')
            ->get();
    }

    public function title(): string
    {
        return 'Plantilla Ingresos';
    }

    public function array(): array
    {
        $nombreEmpresa = $this->empresa->nombre ?? '';
        $idEmpresa = $this->empresa->id ?? '';
        $rows = [];

        $today = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(new \DateTime());

        // ✅ USAR $this->cantidadFilas en lugar de 10
        for ($i = 0; $i < $this->cantidadFilas; $i++) {
            $rows[] = [
                '',                 // A: identidad
                $nombreEmpresa,     // B: nombre_empresa
                $idEmpresa,         // C: id_empresa
                $today,             // D: fechaIngreso
                '',                 // E: area
                '',                 // F: nombre_puesto
                '',                 // G: id_puesto
                '',                 // H: validacion
                's',                // I: recontrataria
                '',                 // J: comentarios
                '',                 // K: nombre
                '',                 // L: apellido
                '',                 // M: telefono
                '',                 // N: correo
                '',                 // O: direccion
                '',                 // P: generoM_F
                $today,             // Q: fecha_nacimiento
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'identidad',
            'empresa',
            'id_empresa',
            'fechaIngreso',
            'area',
            'puesto',
            'id_puesto',
            'validacion',
            'recontrataria',
            'Comentario',
            'nombre',
            'apellido',
            'telefono',
            'correo',
            'direccion',
            'generoM_F',
            'fecha_nacimiento',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->cantidadFilas + 1; // +1 por el header

                // ========================================
                // OCULTAR COLUMNAS
                // ========================================
                $sheet->getColumnDimension('C')->setVisible(false);
                $sheet->getColumnDimension('G')->setVisible(false);
                $sheet->getColumnDimension('H')->setVisible(false);

                // ========================================
                // PROTEGER COLUMNAS
                // ========================================
                $sheet->getStyle("B2:B{$lastRow}")->getProtection()->setLocked(true);
                $sheet->getStyle("C2:C{$lastRow}")->getProtection()->setLocked(true);
                $sheet->getStyle("G2:G{$lastRow}")->getProtection()->setLocked(true);

                // ========================================
                // FÓRMULAS VLOOKUP
                // ========================================
                $puestosCount = $this->puestos->count() + 1;
                
                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->setCellValue("G{$row}", "=IFERROR(VLOOKUP(F{$row},Puestos!\$B:\$C,2,FALSE),\"\")");
                    $sheet->setCellValue("H{$row}", "=IFERROR(VLOOKUP(F{$row},Puestos!\$B:\$B,1,FALSE),\"\")");
                }

                // ========================================
                // FORMATOS DE FECHA - ✅ USAR $lastRow
                // ========================================
                $sheet->getStyle("D2:D{$lastRow}")->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                
                $sheet->getStyle("Q2:Q{$lastRow}")->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

                // ========================================
                // VALIDACIÓN DE FECHAS - ✅ USAR $lastRow
                // ========================================
                $dvFechaIngreso = new DataValidation();
                $dvFechaIngreso->setType(DataValidation::TYPE_DATE);
                $dvFechaIngreso->setErrorStyle(DataValidation::STYLE_STOP);
                $dvFechaIngreso->setOperator(DataValidation::OPERATOR_BETWEEN);
                $dvFechaIngreso->setFormula1('01/01/1900');
                $dvFechaIngreso->setFormula2('31/12/2099');
                $dvFechaIngreso->setShowInputMessage(true);
                $dvFechaIngreso->setPromptTitle('Fecha de Ingreso');
                $dvFechaIngreso->setPrompt('📅 Doble click para abrir el calendario. Formato: DD/MM/AAAA');
                $sheet->setDataValidation("D2:D{$lastRow}", $dvFechaIngreso);

                $dvFechaNacimiento = new DataValidation();
                $dvFechaNacimiento->setType(DataValidation::TYPE_DATE);
                $dvFechaNacimiento->setErrorStyle(DataValidation::STYLE_STOP);
                $dvFechaNacimiento->setOperator(DataValidation::OPERATOR_BETWEEN);
                $dvFechaNacimiento->setFormula1('01/01/1940');
                $dvFechaNacimiento->setFormula2('31/12/2010');
                $dvFechaNacimiento->setShowInputMessage(true);
                $dvFechaNacimiento->setPromptTitle('Fecha de Nacimiento');
                $dvFechaNacimiento->setPrompt('📅 Doble click para abrir el calendario. Formato: DD/MM/AAAA');
                $sheet->setDataValidation("Q2:Q{$lastRow}", $dvFechaNacimiento);

                // Resaltar fechas - ✅ USAR $lastRow
                $sheet->getStyle("D2:D{$lastRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F3FF']]
                ]);
                $sheet->getStyle("Q2:Q{$lastRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F3FF']]
                ]);

                // ========================================
                // ESTILOS
                // ========================================
                $sheet->getStyle('A1:Q1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                ]);

                $sheet->getStyle("A1:Q{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]]
                ]);

                // ========================================
                // DROPDOWNS - ✅ USAR $lastRow
                // ========================================
                
                // Dropdown de Puestos
                $dvPuesto = new DataValidation();
                $dvPuesto->setType(DataValidation::TYPE_LIST);
                $dvPuesto->setErrorStyle(DataValidation::STYLE_STOP);
                $dvPuesto->setFormula1("Puestos!\$B\$2:\$B\${$puestosCount}");
                $dvPuesto->setShowDropDown(true);
                $dvPuesto->setShowInputMessage(true);
                $dvPuesto->setPromptTitle('Seleccionar Puesto');
                $dvPuesto->setPrompt('El ID se asigna automáticamente');
                $sheet->setDataValidation("F2:F{$lastRow}", $dvPuesto);

                // Dropdown de Área
                $dvArea = new DataValidation();
                $dvArea->setType(DataValidation::TYPE_LIST);
                $dvArea->setFormula1('"administrativa,operativa"');
                $dvArea->setShowDropDown(true);
                $sheet->setDataValidation("E2:E{$lastRow}", $dvArea);

                // Dropdown de Género
                $dvGenero = new DataValidation();
                $dvGenero->setType(DataValidation::TYPE_LIST);
                $dvGenero->setFormula1('"M,F"');
                $dvGenero->setShowDropDown(true);
                $sheet->setDataValidation("P2:P{$lastRow}", $dvGenero);

                // Dropdown de Recontrataria
                $dvRecontrataria = new DataValidation();
                $dvRecontrataria->setType(DataValidation::TYPE_LIST);
                $dvRecontrataria->setFormula1('"s,n"');
                $dvRecontrataria->setShowDropDown(true);
                $sheet->setDataValidation("I2:I{$lastRow}", $dvRecontrataria);

                // ========================================
                // ANCHO DE COLUMNAS
                // ========================================
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(30);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(30);
                $sheet->getColumnDimension('K')->setWidth(20);
                $sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(15);
                $sheet->getColumnDimension('N')->setWidth(25);
                $sheet->getColumnDimension('O')->setWidth(30);
                $sheet->getColumnDimension('P')->setWidth(10);
                $sheet->getColumnDimension('Q')->setWidth(18);

                // ========================================
                // INSTRUCCIONES
                // ========================================
                $instrRow = $lastRow + 2;
                $sheet->setCellValue("A{$instrRow}", '📋 INSTRUCCIONES:');
                $sheet->getStyle("A{$instrRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '4472C4']],
                ]);
                
                $instrucciones = [
                    '1. Complete todos los campos requeridos',
                    '2. Empresa e ID de puesto se calculan automáticamente',
                    '3. Para guardar: Archivo → Guardar como → CSV (delimitado por comas)',
                    '4. IMPORTANTE: Las columnas ocultas se incluirán en el CSV',
                    '📅 FECHAS: Doble click en celdas azules para abrir el calendario',
                    '✓ Identidad: 0000-0000-00000 | Género: M/F | Recontrataria: s/n',
                ];

                $row = $instrRow + 1;
                foreach ($instrucciones as $inst) {
                    $sheet->setCellValue("A{$row}", $inst);
                    $sheet->getStyle("A{$row}")->getFont()->setItalic(true);
                    $row++;
                }
            },
        ];
    }
}

class PuestosSheet implements FromArray, WithHeadings, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Puestos';
    }

    public function array(): array
    {
        return DB::table('puestos')
            ->join('departamentos', 'puestos.departamento_id', '=', 'departamentos.id')
            ->where('departamentos.empresa_id', auth()->user()->empresa_id)
            ->select('puestos.id', 'puestos.nombrepuesto', 'puestos.id')
            ->orderBy('puestos.nombrepuesto')
            ->get()
            ->map(fn($p) => [$p->id, $p->nombrepuesto, $p->id])
            ->toArray();
    }

    public function headings(): array
    {
        return ['ID', 'NOMBRE_PUESTO', 'ID'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                $sheet->getStyle('A1:C1')->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
            },
        ];
    }
}