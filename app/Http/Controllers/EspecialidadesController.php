<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EspecialidadesController extends Controller
{
    public function index()
    {
        $especialidades = DB::table('especialidades')->get();
        return view('Especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('Especialidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string'
        ]);

        DB::table('especialidades')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('especialidades.index')->with('success', 'Especialidad añadida correctamente.');
    }

    public function edit($id)
    {
        $especialidad = DB::table('especialidades')->where('id_esp', $id)->first();
        return view('Especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string'
        ]);
        
        DB::table('especialidades')->where('id_esp', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);
        
        return redirect()->route('especialidades.index')->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy($id)
    {
        DB::table('especialidades')->where('id_esp', $id)->delete();
        return redirect()->route('especialidades.index')->with('success', 'Especialidad eliminada correctamente.');
    }
}