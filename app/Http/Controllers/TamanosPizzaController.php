<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TamanosPizzaController extends Controller
{
    /**
     * Mostrar la lista de tamaños de pizza.
     * Tabla: 'TamanosPizza' (compartida también por Mariscos).
     */
    public function index()
    {
        $tamanos = DB::table('tamanospizza')->orderBy('id_tamañop')->get();
        return view('TamanosPizza.index', compact('tamanos'));
    }

    /**
     * Formulario para crear un nuevo tamaño de pizza.
     */
    public function create()
    {
        return view('TamanosPizza.create');
    }

    /**
     * Guardar en la tabla 'TamanosPizza'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tamano' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
        ]);

        DB::table('tamanospizza')->insert([
            'tamano' => $request->tamano,
            'precio' => $request->precio,
        ]);

        return redirect()->route('tamanos-pizza.index')->with('success', 'Tamaño de pizza añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_tamañop'.
     */
    public function edit($id)
    {
        $tamano = DB::table('tamanospizza')->where('id_tamañop', $id)->first();

        if (!$tamano) {
            return redirect()->route('tamanos-pizza.index')->with('error', 'Tamaño no encontrado.');
        }

        return view('TamanosPizza.edit', compact('tamano'));
    }

    /**
     * Actualizar registro en 'TamanosPizza'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tamano' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
        ]);

        DB::table('tamanospizza')->where('id_tamañop', $id)->update([
            'tamano' => $request->tamano,
            'precio' => $request->precio,
        ]);

        return redirect()->route('tamanos-pizza.index')->with('success', 'Tamaño de pizza actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'TamanosPizza'.
     */
    public function destroy($id)
    {
        DB::table('tamanospizza')->where('id_tamañop', $id)->delete();
        return redirect()->route('tamanos-pizza.index')->with('success', 'Tamaño de pizza eliminado correctamente.');
    }
}
