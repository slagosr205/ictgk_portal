<?php

namespace App\Http\Controllers;

use App\Models\Candidatos;
use App\Models\PerfilModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $perfil = PerfilModel::select('perfilesdescrip', 'ingreso', 'egreso', 'bloqueocolaborador')
            ->where('id', auth()->user()->perfil_id)
            ->get();

        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 15);
        $estadoFiltro = $request->input('estado');

        // Validar estado
        $estadoFiltro = in_array($estadoFiltro, ['s', 'n', 'x']) ? $estadoFiltro : null;

        if ($perfil[0]->perfilesdescrip === 'admin') {
            $page = LengthAwarePaginator::resolveCurrentPage();
            $offset = ($page - 1) * $perPage;

            // IMPORTANTE: Usar try-catch para debugging
            try {
                // Llamar al stored procedure
                $result = DB::select(
                    'CALL sp_listar_candidatos_ingresos(?, ?, ?, ?)',
                    [
                        $search ?: null,
                        $estadoFiltro,
                        $perPage,
                        $offset
                    ]
                );

                // Obtener el total usando una nueva conexión
                DB::reconnect();
                $totalResult = DB::select('SELECT FOUND_ROWS() as total');
                $total = $totalResult[0]->total ?? 0;

                // Procesar solo los items de la página actual
                $items = collect($result)->map(function ($item) {
                    $raw = $item->comentarios ?? null;
                    if (is_string($raw)) {
                        $decoded = json_decode($raw, true);
                        $item->comentarios = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($raw)) {
                        $item->comentarios = $raw;
                    } elseif (is_object($raw)) {
                        $item->comentarios = (array) $raw;
                    } else {
                        $item->comentarios = [];
                    }
                    return $item;
                });

                // Crear paginador
                $data = new LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]
                );

            } catch (\Exception $e) {
                Log::error('Error en stored procedure: ' . $e->getMessage());
                
                // Fallback a consulta normal si falla el SP
                $data = Candidatos::select('candidatos.*', DB::raw('egresos_ingresos.activo as activo_ingreso'))
                    ->leftJoin('egresos_ingresos', function ($join) {
                        $join->on('egresos_ingresos.identidad', '=', 'candidatos.identidad')
                            ->where('egresos_ingresos.activo', 's');
                    })
                    ->when($search, function ($query, $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('candidatos.identidad', 'like', "%{$search}%")
                                ->orWhere('candidatos.nombre', 'like', "%{$search}%")
                                ->orWhere('candidatos.apellido', 'like', "%{$search}%")
                                ->orWhere('candidatos.telefono', 'like', "%{$search}%")
                                ->orWhere('candidatos.correo', 'like', "%{$search}%");
                        });
                    })
                    ->when($estadoFiltro, function ($query, $estadoFiltro) {
                        $query->where('candidatos.activo', $estadoFiltro);
                    })
                    ->paginate($perPage)
                    ->appends($request->query());
            }

        } else {
            // Para usuarios no admin
            $data = Candidatos::select('candidatos.*', DB::raw('egresos_ingresos.activo as activo_ingreso'))
                ->join('egresos_ingresos', 'egresos_ingresos.identidad', '=', 'candidatos.identidad')
                ->where('egresos_ingresos.id_empresa', auth()->user()->empresa_id)
                ->where('egresos_ingresos.activo', 's')
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('candidatos.identidad', 'like', "%{$search}%")
                            ->orWhere('candidatos.nombre', 'like', "%{$search}%")
                            ->orWhere('candidatos.apellido', 'like', "%{$search}%")
                            ->orWhere('candidatos.telefono', 'like', "%{$search}%")
                            ->orWhere('candidatos.correo', 'like', "%{$search}%");
                    });
                })
                ->when($estadoFiltro, function ($query, $estadoFiltro) {
                    $query->where('egresos_ingresos.activo', $estadoFiltro);
                })
                ->paginate($perPage)
                ->appends($request->query());
        }

        // CRÍTICO: Verificar que es AJAX antes de retornar partial
        if ($request->ajax() || $request->wantsJson()) {
            return response()->view('components.dmtables', [
                'candidatos' => $data,
                'perfil' => $perfil
            ]);
        }

        return view('Table-inf', compact('data', 'perfil'));
    }
}
