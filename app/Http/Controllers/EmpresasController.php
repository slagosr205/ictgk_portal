<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresas;
use Illuminate\Validation\ValidationException;
class EmpresasController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');
        $perPage = (int) $request->input('per_page', 15);
        if ($perPage <= 0) {
            $perPage = 15;
        }

        $empresas = Empresas::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('contacto', 'like', "%{$search}%")
                        ->orWhere('correo', 'like', "%{$search}%")
                        ->orWhere('telefonos', 'like', "%{$search}%");
                });
            })
            ->when($estado !== null && $estado !== '', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->paginate($perPage)
            ->appends($request->query());

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
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->validator->getMessageBag()
            ], 422);
        }
        return redirect()->back()->withErrors($e->validator->getMessageBag())->withInput();
    }

} 

public function insertCompanyBulk(Request $request)
{
    if (!auth()->check() || auth()->user()->perfil_id !== 1) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    $csvText = $request->input('csv_text');
    $rows = $request->input('rows', []);

    if ($request->hasFile('csv_file')) {
        $file = $request->file('csv_file');
        $csvText = file_get_contents($file->getRealPath());
    }

    $created = [];

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $data = [
                'nombre' => $row['nombre'] ?? null,
                'direccion' => $row['direccion'] ?? null,
                'telefonos' => $row['telefonos'] ?? null,
                'contacto' => $row['contacto'] ?? null,
                'correo' => $row['correo'] ?? null,
                'estado' => in_array(($row['estado'] ?? 'a'), ['a','n'], true) ? $row['estado'] : 'a',
                'logo' => $row['logo'] ?? null,
            ];

            if (!$data['nombre'] || !$data['direccion'] || !$data['telefonos'] || !$data['contacto'] || !$data['correo']) {
                continue;
            }

            $created[] = Empresas::create($data);
        }
    }

    if ($csvText) {
        $lines = preg_split('/\r\n|\r|\n/', trim($csvText));
        if (count($lines) < 2) {
            return response()->json(['message' => 'El CSV debe incluir encabezados y al menos una fila.'], 422);
        }

        $headers = str_getcsv(array_shift($lines));
        $headers = array_map('trim', $headers);

        $required = ['nombre','direccion','telefonos','contacto','correo'];
        foreach ($required as $field) {
            if (!in_array($field, $headers, true)) {
                return response()->json(['message' => 'Falta el campo requerido: ' . $field], 422);
            }
        }

        foreach ($lines as $line) {
            if (!trim($line)) {
                continue;
            }
            $values = str_getcsv($line);
            $row = array_combine($headers, array_pad($values, count($headers), null));
            if (!$row) {
                continue;
            }

            $data = [
                'nombre' => $row['nombre'] ?? null,
                'direccion' => $row['direccion'] ?? null,
                'telefonos' => $row['telefonos'] ?? null,
                'contacto' => $row['contacto'] ?? null,
                'correo' => $row['correo'] ?? null,
                'estado' => in_array(($row['estado'] ?? 'a'), ['a','n'], true) ? $row['estado'] : 'a',
                'logo' => $row['logo'] ?? null,
            ];

            if (!$data['nombre'] || !$data['direccion'] || !$data['telefonos'] || !$data['contacto'] || !$data['correo']) {
                continue;
            }

            $created[] = Empresas::create($data);
        }
    }

    if (empty($created)) {
        return response()->json(['message' => 'Debe proporcionar filas válidas o un CSV con datos válidos.'], 422);
    }

    return response()->json([
        'mensaje' => 'Se procesaron ' . count($created) . ' empresas.',
        'code' => 202,
        'data' => $created
    ]);
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
        $estado = $request->has('estado') ? 'a' : 'n';
        $data=[
            'nombre'=>$request->input('nombre'),
            'direccion'=>$request->input('direccion'),
            'telefonos'=>$request->input('telefonos'),
            'contacto'=>$request->input('contacto'),
            'correo'=>$request->input('correo'),
            'estado'=>$estado,
        ];
        if (!empty($imageUrl)) {
            $data['logo'] = $imageUrl;
        }

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
