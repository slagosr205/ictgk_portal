<?php

namespace App\Http\Controllers;

use App\Models\Candidatos;
use App\Models\PerfilModel;
use Illuminate\Http\Request;

class SeguridadController extends Controller
{
    public function index(Request $request)
    {
        $perfil = PerfilModel::select('perfilesdescrip', 'ingreso', 'egreso', 'bloqueocolaborador')
            ->where('id', auth()->user()->perfil_id)
            ->get();

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) $request->input('per_page', 15);
        if (! in_array($perPage, [15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        // Filtros específicos (por columna)
        $fIdentidad = trim((string) $request->input('identidad', ''));
        $fNombre = trim((string) $request->input('nombre', ''));
        $fApellido = trim((string) $request->input('apellido', ''));
        $fTelefono = trim((string) $request->input('telefono', ''));
        $fCorreo = trim((string) $request->input('correo', ''));
        $fComentario = trim((string) $request->input('comentario', ''));
        $fGenero = $request->input('genero');
        $fNacDesde = $request->input('nac_desde');
        $fNacHasta = $request->input('nac_hasta');

        $orderBy = $request->input('order_by', 'updated_at');
        $orderDir = strtolower((string) $request->input('order_dir', 'desc'));
        $allowedOrderBy = ['updated_at', 'identidad', 'nombre', 'apellido', 'fecha_nacimiento'];
        if (! in_array($orderBy, $allowedOrderBy, true)) {
            $orderBy = 'updated_at';
        }
        if (! in_array($orderDir, ['asc', 'desc'], true)) {
            $orderDir = 'desc';
        }

        $query = Candidatos::select('candidatos.*')
            ->where('candidatos.activo', 'x');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('candidatos.identidad', 'like', "%{$search}%")
                    ->orWhere('candidatos.nombre', 'like', "%{$search}%")
                    ->orWhere('candidatos.apellido', 'like', "%{$search}%")
                    ->orWhere('candidatos.telefono', 'like', "%{$search}%")
                    ->orWhere('candidatos.correo', 'like', "%{$search}%")
                    ->orWhere('candidatos.comentarios', 'like', "%{$search}%");
            });
        }

        if ($fIdentidad !== '') {
            $query->where('candidatos.identidad', 'like', "%{$fIdentidad}%");
        }
        if ($fNombre !== '') {
            $query->where('candidatos.nombre', 'like', "%{$fNombre}%");
        }
        if ($fApellido !== '') {
            $query->where('candidatos.apellido', 'like', "%{$fApellido}%");
        }
        if ($fTelefono !== '') {
            $query->where('candidatos.telefono', 'like', "%{$fTelefono}%");
        }
        if ($fCorreo !== '') {
            $query->where('candidatos.correo', 'like', "%{$fCorreo}%");
        }
        if ($fComentario !== '') {
            $query->where('candidatos.comentarios', 'like', "%{$fComentario}%");
        }

        if ($fGenero) {
            $query->where('candidatos.generoM_F', $fGenero);
        }

        if (! empty($fNacDesde)) {
            $query->whereDate('candidatos.fecha_nacimiento', '>=', $fNacDesde);
        }
        if (! empty($fNacHasta)) {
            $query->whereDate('candidatos.fecha_nacimiento', '<=', $fNacHasta);
        }

        $data = $query->orderBy("candidatos.{$orderBy}", $orderDir)
            ->paginate($perPage)
            ->appends($request->query());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->view('components.seguridad-fragment', [
                'candidatos' => $data,
                'perfil' => $perfil,
            ]);
        }

        return view('seguridad.index', compact('data', 'perfil'));
    }
}
