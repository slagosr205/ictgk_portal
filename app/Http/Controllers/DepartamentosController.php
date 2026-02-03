<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepartamentosModel;
use App\Models\Empresas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class DepartamentosController extends Controller
{
    

    public function index(Request $request)
    {
        $isAdmin = Auth::user()->perfil_id === 1;
        $search = $request->input('search');
        $estadoEmpresa = $request->input('empresa_id');
        $perPage = (int) $request->input('per_page', 15);
        if ($perPage <= 0) {
            $perPage = 15;
        }

        $empresaId = $isAdmin ? $estadoEmpresa : Auth::user()->empresa_id;

        $departamentos = DepartamentosModel::query()
            ->join('empresas', 'departamentos.empresa_id', '=', 'empresas.id')
            ->select('departamentos.*', 'empresas.nombre as empresa_nombre')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('departamentos.nombredepartamento', 'like', "%{$search}%")
                        ->orWhere('empresas.nombre', 'like', "%{$search}%");
                });
            })
            ->when($empresaId, function ($query, $empresaId) {
                $query->where('departamentos.empresa_id', $empresaId);
            })
            ->orderBy('departamentos.nombredepartamento')
            ->paginate($perPage)
            ->appends($request->query());

        $empresas = $isAdmin
            ? Empresas::orderBy('nombre')->get()
            : Empresas::find(Auth::user()->empresa_id);

        return view('departamentv', [
            'departamentos' => $departamentos,
            'empresas' => $empresas,
            'isAdmin' => $isAdmin
        ]);
    }

    public function insertDepartament(Request $request)
    {

        try {
            //code...

        $isAdmin = Auth::user()->perfil_id === 1;
        $rules = [
            'nombredepartamento' => 'required|string',
        ];
        if ($isAdmin) {
            $rules['empresa_id'] = 'required|integer';
        }

        $request->validate($rules);

        $empresaId = $isAdmin ? $request->input('empresa_id') : Auth::user()->empresa_id;
        if ($isAdmin && !Empresas::where('id', $empresaId)->exists()) {
            return response()->json(['message' => 'Empresa inválida'], 422);
        }

        $data=[
            'nombredepartamento'=>$request->input('nombredepartamento'),
            'empresa_id'=>$empresaId,
        ];

        DepartamentosModel::create($data);
        $ultimoregistro=DepartamentosModel::latest()->get()->first();
        $ultimoregistro->empresa_nombre=Empresas::findOrFail($ultimoregistro->empresa_id)->nombre;
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

    public function insertDepartamentBulk(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $isAdmin = Auth::user()->perfil_id === 1;
        $rows = $request->input('rows', []);
        $csvText = $request->input('csv_text');

        if ($request->hasFile('csv_file')) {
            $csvText = file_get_contents($request->file('csv_file')->getRealPath());
        }

        $defaultEmpresaId = $isAdmin ? $request->input('empresa_id') : Auth::user()->empresa_id;
        if ($isAdmin && $defaultEmpresaId && !Empresas::where('id', $defaultEmpresaId)->exists()) {
            return response()->json(['message' => 'Empresa inválida'], 422);
        }

        $created = [];
        $invalidRows = [];

        foreach ($rows as $index => $row) {
            $empresaId = $isAdmin ? ($row['empresa_id'] ?? $defaultEmpresaId) : Auth::user()->empresa_id;
            $nombre = $row['nombredepartamento'] ?? null;

            if (!$nombre || !$empresaId) {
                $invalidRows[] = $index + 1;
                continue;
            }

            if ($isAdmin && !Empresas::where('id', $empresaId)->exists()) {
                $invalidRows[] = $index + 1;
                continue;
            }

            $created[] = DepartamentosModel::create([
                'nombredepartamento' => $nombre,
                'empresa_id' => $empresaId,
            ]);
        }

        if ($csvText) {
            $lines = preg_split('/\r\n|\r|\n/', trim($csvText));
            if (count($lines) < 2) {
                return response()->json(['message' => 'El CSV debe incluir encabezados y al menos una fila.'], 422);
            }

            $headers = str_getcsv(array_shift($lines));
            $headers = array_map('trim', $headers);

            $required = ['nombredepartamento'];
            if ($isAdmin) {
                $required[] = 'empresa_id';
            }
            foreach ($required as $field) {
                if (!in_array($field, $headers, true)) {
                    return response()->json(['message' => 'Falta el campo requerido: ' . $field], 422);
                }
            }

            foreach ($lines as $lineIndex => $line) {
                if (!trim($line)) {
                    continue;
                }
                $values = str_getcsv($line);
                $row = array_combine($headers, array_pad($values, count($headers), null));
                if (!$row) {
                    $invalidRows[] = $lineIndex + 1;
                    continue;
                }

                $empresaId = $isAdmin ? ($row['empresa_id'] ?? $defaultEmpresaId) : Auth::user()->empresa_id;
                $nombre = $row['nombredepartamento'] ?? null;

                if (!$nombre || !$empresaId) {
                    $invalidRows[] = $lineIndex + 1;
                    continue;
                }

                if ($isAdmin && !Empresas::where('id', $empresaId)->exists()) {
                    $invalidRows[] = $lineIndex + 1;
                    continue;
                }

                $created[] = DepartamentosModel::create([
                    'nombredepartamento' => $nombre,
                    'empresa_id' => $empresaId,
                ]);
            }
        }

        if (!empty($invalidRows)) {
            return response()->json([
                'message' => 'Hay filas inválidas.',
                'rows' => $invalidRows
            ], 422);
        }

        if (empty($created)) {
            return response()->json(['message' => 'Debe proporcionar filas válidas o un CSV con datos válidos.'], 422);
        }

        foreach ($created as $item) {
            $item->empresa_nombre = Empresas::findOrFail($item->empresa_id)->nombre;
        }

        return response()->json([
            'mensaje' => 'Se procesaron ' . count($created) . ' departamentos.',
            'code' => 202,
            'data' => $created
        ]);
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
            if (!$updateDepartament) {
                return response()->json(['errorUpdate'=>'no se encontró el departamento','status'=>404]);
            }

            if (Auth::user()->perfil_id !== 1 && $updateDepartament->empresa_id !== Auth::user()->empresa_id) {
                return response()->json(['errorUpdate'=>'no autorizado','status'=>403]);
            }
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
