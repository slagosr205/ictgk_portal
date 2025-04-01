<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class CsvImport implements ToArray
{
    public function array(array $rows)
    {
        $datos = [];
        $headers = array_map('trim', $rows[0]); // Obtenemos los encabezados

        // Procesamos cada fila de datos
        foreach (array_slice($rows, 1) as $row) {
            // Solo procesamos filas no vacías
            if (!empty(array_filter($row))) {
                $fila = [];

                // Asociamos manualmente las columnas a los encabezados
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $fila[$header] = trim($row[$index]);
                    }
                }

                // Elimina la columna 'validacion' y valores vacíos
                $fila = array_diff_key($fila, array_flip(['validacion']));
                $fila = array_filter($fila);

                // Añadir la fila procesada a los datos
                $datos[] = $fila;
            }
        }

        return $datos;
    }
}
