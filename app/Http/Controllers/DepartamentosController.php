<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepartamentosModel;
use App\Models\Empresas;
use Illuminate\Validation\ValidationException;
class DepartamentosController extends Controller
{
    

    public function index()
    {
        
        $empresas=Empresas::find(\Auth::user()->empresa_id);
        $departamentos=DepartamentosModel::where('empresa_id',auth()->user()->empresa_id)->get();
        $empresa_nombre=Empresas::findOrFail(auth()->user()->empresa_id)->nombre;
        foreach ($departamentos as $departamento) {
            $departamento->empresa_nombre = $empresa_nombre;
        }
        //dd($departamentos);
        return view('departamentv')->with(['departamentos'=>$departamentos])->with(['empresas'=>$empresas]); 
    }

    public function insertDepartament(Request $request)
    {

        try {
            //code...
        
        $request->validate([
            'nombredepartamento'=>'required|string',
            'empresa_id'=>'required|string',
        
        ]);
        
        $data=[
            'nombredepartamento'=>$request->input('nombredepartamento'),
            'empresa_id'=>$request->input('empresa_id'),
            
        ];

        DepartamentosModel::create($data);
        $ultimoregistro=DepartamentosModel::latest()->get()->first();
        $ultimoregistro->empresa_nombre=Empresas::findOrFail($ultimoregistro->empresa_id)->nombre;
        return response()->json(['mensaje'=>'se creo con exito','code'=>202,'data'=>$ultimoregistro]);
   }catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->validator->getMessageBag())->withInput();
    }

} 

    public function show($id)
    {
        $departamento=DepartamentosModel::select('nombredepartamento')->where('id',$id)->get();

        if(!is_null($departamento))
        {
            return response()->json(['departamento'=>$departamento,'status'=>200]);
        }else{
            return response()->json(['message'=>'no se encontro información']);
        }
    }

    public function updateDepartament(Request $request)
    {
        try {
            //code...
            $request->validate([
                'nombredepartamento'=>'required|string',
                'departamento_id'=>'required|string',
            
            ]);

            $departamento_id=$request->input('departamento_id');
            $nombredepartamento=$request->input('nombredepartamento');

            $updateDepartament=DepartamentosModel::find($departamento_id);
            $updateDepartament->nombredepartamento=$nombredepartamento;
            if($updateDepartament->save())
            {
                return response()->json(['success'=>'el departamento fue actualizado','status'=>200]);
            }else{
                return response()->json(['errorUpdate'=>'no se pudo actualizar','status'=>404]);
            }
        } catch (\Exception $e) 
        {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }
}
