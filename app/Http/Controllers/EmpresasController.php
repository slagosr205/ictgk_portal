<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresas;
use Illuminate\Validation\ValidationException;
class EmpresasController extends Controller
{
    //

    public function index()
    {
        $empresas=Empresas::all();
        return view('empresas')->with(['empresas'=>$empresas]);
    }

    public function insertCompany(Request $request)
    {

        try {
            //code...
        
        $request->validate([
            'nombre'=>'required|string',
        'direccion'=>'required|string',
        'telefonos'=>'required|string',
        'contacto'=>'required|string',
        'correo'=>'required|string',
        'logo'=> 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $imageUrl='';
        if($request->hasFile('logo'))
        {
            $imagePath=$request->file('logo')->store('public/logos');
            $imageUrl=asset(str_replace('public','storage',$imagePath));
        }
        $data=[
            'nombre'=>$request->input('nombre'),
            'direccion'=>$request->input('direccion'),
            'telefonos'=>$request->input('telefonos'),
            'contacto'=>$request->input('contacto'),
            'correo'=>$request->input('correo'),
            'estado'=>'a',
            'logo'=>$imageUrl??null,
        ];

        Empresas::create($data);
        $ultimoregistro=Empresas::latest()->get()->first();
        
        return response()->json(['mensaje'=>'se creo con exito','code'=>202,'data'=>$ultimoregistro]);
   }catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->validator->getMessageBag())->withInput();
    }

} 

public function consultingCompany($request )
{
    
    $id=$request;

    $dataEmpresa=Empresas::where('id',$id)->get();
    $data = ['data'=>$dataEmpresa];
    return response()->json($data);
}

public function updateCompany(Request $request)
{
    //dd($request);

    try {
        //code...
        $empresa=Empresas::find($request->input('id'));
        $imageUrl='';
        if($request->hasFile('logo'))
        {
            $imagePath=$request->file('logo')->store('public/logos');
            $imageUrl=asset(str_replace('public','storage',$imagePath));
        }
        $data=[
            'nombre'=>$request->input('nombre'),
            'direccion'=>$request->input('direccion'),
            'telefonos'=>$request->input('telefonos'),
            'contacto'=>$request->input('contacto'),
            'correo'=>$request->input('correo'),
            'estado'=>'a',
            'logo'=>$imageUrl??null,
        ];

        if($empresa->update($data)){
        return response()->json(['mensaje'=>'se ha actualizado correctamente el registro solicitado','code'=>202,'data'=>$empresa]);
    }else{
        return response()->json(['mensaje'=>'No se pudo actualizar el registro.','code'=>500]);
    }
    } catch (\Exception $e) {
        //throw $th;

        return response()->json([
            'error'=>$e->getMessage(),
            'code'=>404
        ]);
    }    
}


public function updateCompanyState(Request $request)
{
   // dd($request);

    try {
        //code...
        $idCompany=$request->input('idCompany');
        $state=$request->input('state');
        $empresa=Empresas::find($idCompany);
       
        $data=[
            
            'estado'=>$state,
            
        ];

        if($empresa->update($data)){
            return response()->json(['mensaje'=>'se ha actualizado correctamente el registro solicitado','code'=>202,'data'=>$empresa]);
        }else{
            return response()->json(['mensaje'=>'No se pudo actualizar el registro.','code'=>500]);
        }
    } catch (\Exception $e) {
        //throw $th;

        return response()->json([
            'error'=>$e->getMessage(),
            'code'=>404
        ]);
    }    
}


}
