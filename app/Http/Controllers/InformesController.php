<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidatos;
use App\Models\Ingresos;
use Illuminate\Support\Facades\DB;
class InformesController extends Controller
{
    //

    public function GetInformes()
    {   
        $conteoPoblacion=DB::select('select count(ei.activo) as poblacionActiva from egresos_ingresos ei
            inner join  empresas em on em.id=ei.id_empresa where ei.activo="s" group by ei.activo');
        $conteoEgresos=DB::select('select count(ei.activo) as egresos from egresos_ingresos ei
        inner join  empresas em on em.id=ei.id_empresa where ei.activo="n" group by ei.activo');
        $promedioAntiguedad=DB::select('SELECT TRUNCATE(AVG(DATEDIFF(CURDATE(), fechaIngreso) / 12),2) AS promedio_antiguedad
        FROM egresos_ingresos;');

        $promedioIngreso=DB::select('select TRUNCATE(count(month(fechaIngreso))/12,2) as promedioIngreso from egresos_ingresos');

       

        return view('informes')->with(['poblacionTotal'=>$conteoPoblacion,'egresosTotales'=>$conteoEgresos,'promedioAntiguedad'=>$promedioAntiguedad,'promedioIngresos'=>$promedioIngreso]);

    }

    public function GetData()
    {
        $ingresosActivoxEmpresas=DB::select('select em.nombre, count(em.nombre) as cant from egresos_ingresos ei
                                    inner join  empresas em on em.id=ei.id_empresa where ei.activo="s" group by em.nombre');

        return response()->json($ingresosActivoxEmpresas);
    }

    public function GetDataOut()
    {
        $egresosActivoxEmpresas=DB::select('select em.nombre, count(em.nombre) as cant from egresos_ingresos ei
                                    inner join  empresas em on em.id=ei.id_empresa where ei.activo="n" group by em.nombre');

        return response()->json($egresosActivoxEmpresas);
    }

    public function GetDataState()
    {
        $edadxEstado= DB::select("SELECT 
        CASE
        WHEN ei.activo='n' THEN 'inactivo'
        ELSE 'activo'
        END AS 'estado',
        CASE
        WHEN ca.generoM_F ='F' OR ca.generoM_F ='f' THEN 'Mujer'
        ELSE 'Hombre'
        END AS genero
        ,
         CASE
            WHEN TIMESTAMPDIFF(YEAR, ca.fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN '18-25'
            WHEN TIMESTAMPDIFF(YEAR, ca.fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN '26-35'
            WHEN TIMESTAMPDIFF(YEAR, ca.fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN '36-45'
            WHEN TIMESTAMPDIFF(YEAR, ca.fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN '46-55'
            WHEN TIMESTAMPDIFF(YEAR, ca.fecha_nacimiento, CURDATE()) > 55 THEN '55+'
            ELSE 'Unknown'
        END AS rango_edad,
        COUNT(ei.activo) AS poblacionActiva
       
    FROM 
        egresos_ingresos ei
    INNER JOIN 
        empresas em ON em.id = ei.id_empresa 
    INNER JOIN 
        candidatos ca ON ca.identidad = ei.identidad
    GROUP BY 
        ei.activo, 
        ca.generoM_F,
        rango_edad");

        return response()->json($edadxEstado);
    }


    public function IngresosxEgresos()
    {
        $egresosxIngresos = DB::table('egresos_ingresos as ei')
        ->select(
                'ei.fechaIngreso as fecha',
                DB::raw('SUM(CASE WHEN ei.activo = "s" THEN 1 ELSE 0 END) AS ingresos'),
                DB::raw('SUM(CASE WHEN ei.activo = "n" or  ei.fechaEgreso<>null THEN 1 ELSE 0 END) AS egresos')
                )
            ->join('empresas as em', 'em.id', '=', 'ei.id_empresa')
            ->groupBy('ei.fechaIngreso')
            ->get();

            return response()->json($egresosxIngresos);
    }


    public function RenunciasxGenero()
    {
        $renunciasxgenero=DB::select("  SELECT 

    CASE
        WHEN ca.generoM_F = 'F' OR ca.generoM_F = 'f' THEN 'Mujer'
        ELSE 'Hombre'
        END AS genero,
            COUNT(ca.generoM_F) AS poblacionActiva
        FROM 
            egresos_ingresos ei
        INNER JOIN 
            empresas em ON em.id = ei.id_empresa 
        INNER JOIN 
            candidatos ca ON ca.identidad = ei.identidad
        WHERE 
            ei.activo = 'n'
        GROUP BY 
            estado, 
            genero;");


        return response()->json($renunciasxgenero);
    }
}
