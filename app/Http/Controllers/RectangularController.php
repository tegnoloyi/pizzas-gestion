<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RectangularController extends Controller
{
    /**
     * Mostrar la lista de pizzas rectangulares.
     * Tablas corregidas: 'rectangular', 'especialidades' y 'categoriasprod'.
     */
    public function index()
    {
        $rectangulares = DB::table('rectangular')
            ->join('especialidades', 'rectangular.id_esp', '=', 'especialidades.id_esp')
            ->join('categoriasprod', 'rectangular.id_cat', '=', 'categoriasprod.id_cat')
            ->select(
                'rectangular.id_rec', 
                'especialidades.nombre as especialidad', 
                'rectangular.precio', 
                'categoriasprod.descripcion as categoria'
            )
            ->get();

        return view('Rectangular.index', compact('rectangulares'));
    }

    /**
     * Formulario para crear una nueva pizza rectangular.
     */
    public function create()
    {
        $especialidades = DB::table('especialidades')->get();
        $categorias = DB::table('categoriasprod')->get();
        
        return view('Rectangular.create', compact('especialidades', 'categorias'));
    }

    /**
     * Guardar en la tabla 'rectangular'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_esp' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('rectangular')->insert([
            'id_esp' => $request->id_esp,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('rectangular.index')->with('success', 'Pizza Rectangular añadida correctamente.');
    }

    /**
     * Editar registro usando 'id_rec'.
     */
    public function edit($id)
    {
        $rectangular = DB::table('rectangular')->where('id_rec', $id)->first();
        
        if (!$rectangular) {
            return redirect()->route('rectangular.index')->with('error', 'Registro no encontrado.');
        }

        $especialidades = DB::table('especialidades')->get();
        $categorias = DB::table('categoriasprod')->get();

        return view('Rectangular.edit', compact('rectangular', 'especialidades', 'categorias'));
    }

    /**
     * Actualizar registro en 'rectangular'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_esp' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('rectangular')->where('id_rec', $id)->update([
            'id_esp' => $request->id_esp,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('rectangular.index')->with('success', 'Pizza Rectangular actualizada correctamente.');
    }

    /**
     * Eliminar registro de 'rectangular'.
     */
    public function destroy($id)
    {
        DB::table('rectangular')->where('id_rec', $id)->delete();
        return redirect()->route('rectangular.index')->with('success', 'Pizza Rectangular eliminada correctamente.');
    }
}