<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaguettyController extends Controller
{
    /**
     * Mostrar la lista de spaguetty.
     * Tablas corregidas a 'spaguetty' y 'categoriasprod'.
     */
    public function index()
    {
        $spaguettis = DB::table('spaguetty')
            ->join('categoriasprod', 'spaguetty.id_cat', '=', 'categoriasprod.id_cat')
            ->select(
                'spaguetty.id_spag', 
                'spaguetty.orden', 
                'spaguetty.precio', 
                'categoriasprod.descripcion as categoria'
            )
            ->get();

        return view('Spaguetty.index', compact('spaguettis'));
    }

    /**
     * Formulario para crear una nueva orden.
     */
    public function create()
    {
        $categorias = DB::table('categoriasprod')->get();
        return view('Spaguetty.create', compact('categorias'));
    }

    /**
     * Guardar en la tabla 'spaguetty'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('spaguetty')->insert([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('spaguetty.index')->with('success', 'Spaguetty añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_spag'.
     */
    public function edit($id)
    {
        $spaguetty = DB::table('spaguetty')->where('id_spag', $id)->first();
        
        if (!$spaguetty) {
            return redirect()->route('spaguetty.index')->with('error', 'Registro no encontrado.');
        }

        $categorias = DB::table('categoriasprod')->get();

        return view('Spaguetty.edit', compact('spaguetty', 'categorias'));
    }

    /**
     * Actualizar registro en 'spaguetty'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orden' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('spaguetty')->where('id_spag', $id)->update([
            'orden' => $request->orden,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('spaguetty.index')->with('success', 'Spaguetty actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'spaguetty'.
     */
    public function destroy($id)
    {
        DB::table('spaguetty')->where('id_spag', $id)->delete();
        return redirect()->route('spaguetty.index')->with('success', 'Spaguetty eliminado correctamente.');
    }
}