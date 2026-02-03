<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PuestosModel;
use App\Models\DepartamentosModel;
use App\Models\Empresas;
use Illuminate\Support\Facades\Validator;

class PuestosController extends Controller
{
    //

    public function index(Request $request)
    {
        $isAdmin = auth()->user()->perfil_id === 1;
        $search = $request->input('search');
        $empresaId = $request->input('empresa_id');
        $departamentoId = $request->input('departamento_id');
        $perPage = (int) $request->input('per_page', 15);
        if ($perPage <= 0) {
            $perPage = 15;
        }

        $effectiveEmpresaId = $isAdmin ? $empresaId : auth()->user()->empresa_id;

        $puestos = PuestosModel::select('puestos.*', 'departamentos.nombredepartamento', 'empresas.nombre as empresa_nombre')
            ->join('departamentos', 'puestos.departamento_id', '=', 'departamentos.id')
            ->join('empresas', 'empresas.id', '=', 'departamentos.empresa_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('puestos.nombrepuesto', 'like', "%{$search}%")
                        ->orWhere('departamentos.nombredepartamento', 'like', "%{$search}%")
                        ->orWhere('empresas.nombre', 'like', "%{$search}%");
                });
            })
            ->when($effectiveEmpresaId, function ($query, $effectiveEmpresaId) {
                $query->where('empresas.id', $effectiveEmpresaId);
            })
            ->when($departamentoId, function ($query, $departamentoId) {
                $query->where('departamentos.id', $departamentoId);
            })
            ->orderBy('puestos.nombrepuesto')
            ->paginate($perPage)
            ->appends($request->query());

        $departamentos = $isAdmin
            ? DepartamentosModel::select('departamentos.*', 'empresas.nombre as empresa_nombre')
                ->join('empresas', 'empresas.id', '=', 'departamentos.empresa_id')
                ->orderBy('departamentos.nombredepartamento')
                ->get()
            : DepartamentosModel::where('empresa_id', auth()->user()->empresa_id)->orderBy('nombredepartamento')->get();

        $empresas = $isAdmin ? \App\Models\Empresas::orderBy('nombre')->get() : null;

        return view('puestos', [
            'puestos' => $puestos,
            'departamentos' => $departamentos,
            'empresas' => $empresas,
            'isAdmin' => $isAdmin
        ]);
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

        $departamentoId = $request->input('departamento_id');
        $departamento = DepartamentosModel::find($departamentoId);
        if (!$departamento) {
            return redirect()->back()->withErrors(['errorsPositions' => ['Departamento inválido']])->withInput();
        }
        if (auth()->user()->perfil_id !== 1 && $departamento->empresa_id !== auth()->user()->empresa_id) {
            return redirect()->back()->withErrors(['errorsPositions' => ['No autorizado para ese departamento']])->withInput();
        }

        $data=[
            'nombrepuesto'=>$request->input('nombrepuesto'),
            'departamento_id'=>$departamentoId
        ];

        PuestosModel::create($data);
        if ($request->expectsJson()) {
            $puesto = PuestosModel::latest()->first();
            $puesto->nombredepartamento = $departamento->nombredepartamento;
            $puesto->empresa_nombre = \App\Models\Empresas::find($departamento->empresa_id)->nombre;
            return response()->json(['mensaje' => 'se creo con exito', 'code' => 202, 'data' => $puesto]);
        }
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
        if (!$positionUpdate) {
            return redirect()->back()->with(['updatedPositionserror'=>'No se encontró el puesto']);
        }

        $departamentoId = $request->input('departamento_id');
        $departamento = DepartamentosModel::find($departamentoId);
        if (!$departamento) {
            return redirect()->back()->with(['updatedPositionserror'=>'Departamento inválido']);
        }
        if (auth()->user()->perfil_id !== 1 && $departamento->empresa_id !== auth()->user()->empresa_id) {
            return redirect()->back()->with(['updatedPositionserror'=>'No autorizado para ese departamento']);
        }

        $positionUpdate->nombrepuesto=$request->input('puestonombre');
        $positionUpdate->departamento_id=$departamentoId;
        $positionUpdate->save();
        if ($request->expectsJson()) {
            $positionUpdate->nombredepartamento = $departamento->nombredepartamento;
            $positionUpdate->empresa_nombre = \App\Models\Empresas::find($departamento->empresa_id)->nombre;
            return response()->json(['success' => 'se actualizado con exito el puesto', 'status' => 200, 'data' => $positionUpdate]);
        }
        return redirect()->back()->with(['updatedPositions'=>'se actualizado con exito el puesto']);
        
        
    }catch(\Exception $e)
    {
        return redirect()->back()->with(['updatedPositionserror'=>$e->getMessage()]);
    }
        
    }

    public function puestosxEmpresas($idEmpresas)
    {
        $puesto=PuestosModel::select('puestos.id','puestos.nombrepuesto','departamentos.id as departamento_id','departamentos.nombredepartamento')
        ->join('departamentos','departamentos.id','=','puestos.departamento_id')
        ->join('empresas','empresas.id','=','departamentos.empresa_id')
        ->where('empresas.id',$idEmpresas)->get();

        return view('components.selected-positions',['puestos'=>$puesto]);
    }

    public function insertPositionsBulk(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $isAdmin = auth()->user()->perfil_id === 1;
        $rows = $request->input('rows', []);
        $csvText = $request->input('csv_text');

        if ($request->hasFile('csv_file')) {
            $csvText = file_get_contents($request->file('csv_file')->getRealPath());
        }

        $created = [];
        $invalidRows = [];

        foreach ($rows as $index => $row) {
            $nombre = $row['nombrepuesto'] ?? null;
            $departamentoId = $row['departamento_id'] ?? null;
            if (!$nombre || !$departamentoId) {
                $invalidRows[] = $index + 1;
                continue;
            }

            $departamento = DepartamentosModel::find($departamentoId);
            if (!$departamento) {
                $invalidRows[] = $index + 1;
                continue;
            }
            if (!$isAdmin && $departamento->empresa_id !== auth()->user()->empresa_id) {
                $invalidRows[] = $index + 1;
                continue;
            }

            $created[] = PuestosModel::create([
                'nombrepuesto' => $nombre,
                'departamento_id' => $departamentoId
            ]);
        }

        if ($csvText) {
            $lines = preg_split('/\r\n|\r|\n/', trim($csvText));
            if (count($lines) < 2) {
                return response()->json(['message' => 'El CSV debe incluir encabezados y al menos una fila.'], 422);
            }

            $headers = str_getcsv(array_shift($lines));
            $headers = array_map('trim', $headers);
            $required = ['nombrepuesto', 'departamento_id'];
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

                $nombre = $row['nombrepuesto'] ?? null;
                $departamentoId = $row['departamento_id'] ?? null;
                if (!$nombre || !$departamentoId) {
                    $invalidRows[] = $lineIndex + 1;
                    continue;
                }

                $departamento = DepartamentosModel::find($departamentoId);
                if (!$departamento) {
                    $invalidRows[] = $lineIndex + 1;
                    continue;
                }
                if (!$isAdmin && $departamento->empresa_id !== auth()->user()->empresa_id) {
                    $invalidRows[] = $lineIndex + 1;
                    continue;
                }

                $created[] = PuestosModel::create([
                    'nombrepuesto' => $nombre,
                    'departamento_id' => $departamentoId
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
            $departamento = DepartamentosModel::find($item->departamento_id);
            $item->nombredepartamento = $departamento?->nombredepartamento;
            $item->empresa_nombre = $departamento ? \App\Models\Empresas::find($departamento->empresa_id)->nombre : '';
        }

        return response()->json([
            'mensaje' => 'Se procesaron ' . count($created) . ' puestos.',
            'code' => 202,
            'data' => $created
        ]);
    }
}
