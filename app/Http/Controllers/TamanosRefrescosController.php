<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TamanosRefrescosController extends Controller
{
    /**
     * Mostrar la lista de tamaños de refresco.
     * Tabla: 'tamanosrefrescos'.
     */
    public function index()
    {
        $tamanos = DB::table('tamanosrefrescos')->orderBy('id_tamano')->get();
        return view('TamanosRefrescos.index', compact('tamanos'));
    }

    /**
     * Formulario para crear un nuevo tamaño de refresco.
     */
    public function create()
    {
        return view('TamanosRefrescos.create');
    }

    /**
     * Guardar en la tabla 'tamanosrefrescos'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tamano' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
        ]);

        DB::table('tamanosrefrescos')->insert([
            'tamano' => $request->tamano,
            'precio' => $request->precio,
        ]);

        return redirect()->route('tamanos-refrescos.index')->with('success', 'Tamaño de refresco añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_tamano'.
     */
    public function edit($id)
    {
        $tamano = DB::table('tamanosrefrescos')->where('id_tamano', $id)->first();

        if (!$tamano) {
            return redirect()->route('tamanos-refrescos.index')->with('error', 'Tamaño no encontrado.');
        }

        return view('TamanosRefrescos.edit', compact('tamano'));
    }

    /**
     * Actualizar registro en 'tamanosrefrescos'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tamano' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
        ]);

        DB::table('tamanosrefrescos')->where('id_tamano', $id)->update([
            'tamano' => $request->tamano,
            'precio' => $request->precio,
        ]);

        return redirect()->route('tamanos-refrescos.index')->with('success', 'Tamaño de refresco actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'tamanosrefrescos'.
     */
    public function destroy($id)
    {
        DB::table('tamanosrefrescos')->where('id_tamano', $id)->delete();
        return redirect()->route('tamanos-refrescos.index')->with('success', 'Tamaño de refresco eliminado correctamente.');
    }
}
