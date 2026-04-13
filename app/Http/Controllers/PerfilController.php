<?php

namespace App\Http\Controllers;

use App\Events\RegistroActualizado;
use App\Models\PerfilModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerfilController extends Controller
{
    //

    public function create(Request $request)
    {
        $dataperfil = [
            'perfilesdescrip' => $request->input('perfilesdescrip'),
            'ingreso' => $request->input('ingreso'),
            'egreso' => $request->input('egreso'),
            'bloqueocolaborador' => $request->input('bloqueocolaborador'),
            'gestiontablas' => $request->input('gestiontablas'),
            'visualizarinformes' => $request->input('visualizarinformes'),
            'seguridad' => $request->input('seguridad', 0),

        ];
        $addperfil = PerfilModel::create($dataperfil);

        return redirect()->back()->with(['mensaje' => 'se creado con exito el perfil']);
    }

    public function show()
    {
        $allperfils = PerfilModel::all();

        return view('perfiles', compact('allperfils'));
    }

    public function update(Request $request)
    {

        try {
            $updateperfil = PerfilModel::find($request->input('idrole'));

            $updateperfil[$request->input('dataField')] = $request->input('valAuth');
            Log::info('enviando para actualizar');
            // event(new RegistroActualizado($updateperfil));
            $updateperfil->save();

            return response()->json(['mensaje' => 'se ha actualizado con exito']);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => $e->getMessage()]);
        }

    }
}
