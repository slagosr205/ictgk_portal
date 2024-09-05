<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PuestosModel;
use App\Models\DepartamentosModel;
use Validator;
class PuestosController extends Controller
{
    //

    public function index()
    {
        $puestos=PuestosModel::select('puestos.*','departamentos.nombredepartamento')
        ->join('departamentos','puestos.departamento_id','=','departamentos.id')
        ->join('empresas','empresas.id','=','departamentos.empresa_id')
        ->where('empresas.id',auth()->user()->empresa_id)
        ->get();
        $departamentos=DepartamentosModel::where('empresa_id',auth()->user()->empresa_id)->get();
        return view('puestos')->with(['puestos'=>$puestos,'departamentos'=>$departamentos]);
    }

    public function create(Request $request)
    {
        $rule=[
            'nombrepuesto' => 'required|string|max:255',
            'departamento_id' => 'required|integer'
        ];

        $validatorFieldsPositions=Validator::make($request->all(),$rule);

        if($validatorFieldsPositions->fails())
        {
            return redirect()->back()->withErrors([
                'errorsPositions'=>$validatorFieldsPositions->errors()->all()
            ])->withInput();
        }

        $data=[
            'nombrepuesto'=>$request->input('nombrepuesto'),
            'departamento_id'=>$request->input('departamento_id')
        ];

        PuestosModel::create($data);
        return redirect()->back()->with(['successPositions'=>'se creado con exito el puesto']);
    }

    public function dataPuestos($id)
    {
        $puesto=PuestosModel::select('puestos.id','puestos.nombrepuesto','departamentos.id as departamento_id','departamentos.nombredepartamento')
        ->join('departamentos','departamentos.id','=','puestos.departamento_id')
        ->where('puestos.id',$id)->get();
        
        return response()->json([$puesto]);
    }

    public function updatePosition(Request $request)
    {
        try{
        $positionUpdate=PuestosModel::find($request->input('puesto_id'));


        $positionUpdate->nombrepuesto=$request->input('puestonombre');
        $positionUpdate->departamento_id=$request->input('departamento_id');
        $positionUpdate->save();
       
        return redirect()->back()->with(['updatedPositions'=>'se actualizado con exito el puesto']);
        
        
    }catch(\Exception $e)
    {
        return redirect()->back()->with(['updatedPositionserror'=>$e->getMessage()]);
    }
        
    }
}
