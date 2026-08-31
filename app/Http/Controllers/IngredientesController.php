<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientesController extends Controller
{
    /**
     * Mostrar la lista de ingredientes.
     * Tabla: 'Ingredientes' (usada también por el POS en pizzas "Por Ingrediente").
     */
    public function index()
    {
        $ingredientes = DB::table('ingredientes')->orderBy('id_ingrediente')->get();
        return view('Ingredientes.index', compact('ingredientes'));
    }

    /**
     * Formulario para crear un nuevo ingrediente.
     */
    public function create()
    {
        return view('Ingredientes.create');
    }

    /**
     * Guardar en la tabla 'Ingredientes'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ingrediente' => 'required|string|max:100',
        ]);

        DB::table('ingredientes')->insert([
            'ingrediente' => $request->ingrediente,
        ]);

        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_ingrediente'.
     */
    public function edit($id)
    {
        $ingrediente = DB::table('ingredientes')->where('id_ingrediente', $id)->first();

        if (!$ingrediente) {
            return redirect()->route('ingredientes.index')->with('error', 'Ingrediente no encontrado.');
        }

        return view('Ingredientes.edit', compact('ingrediente'));
    }

    /**
     * Actualizar registro en 'Ingredientes'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ingrediente' => 'required|string|max:100',
        ]);

        DB::table('ingredientes')->where('id_ingrediente', $id)->update([
            'ingrediente' => $request->ingrediente,
        ]);

        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'Ingredientes'.
     */
    public function destroy($id)
    {
        DB::table('ingredientes')->where('id_ingrediente', $id)->delete();
        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente eliminado correctamente.');
    }
}
