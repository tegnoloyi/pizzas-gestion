<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefrescosController extends Controller
{
    /**
     * Mostrar la lista de refrescos.
     * Tablas corregidas: 'Refrescos', 'TamanosRefrescos' y 'CategoriasProd'.
     */
    public function index()
    {
        $refrescos = DB::table('refrescos')
            ->join('tamanosrefrescos', 'refrescos.id_tamano', '=', 'tamanosrefrescos.id_tamano')
            ->join('categoriasprod', 'refrescos.id_cat', '=', 'categoriasprod.id_cat')
            ->select(
                'refrescos.id_refresco', 
                'refrescos.nombre', 
                'tamanosrefrescos.tamano', 
                'tamanosrefrescos.precio', 
                'categoriasprod.descripcion as categoria'
            )
            ->get();

        return view('Refrescos.index', compact('refrescos'));
    }

    /**
     * Formulario para crear un nuevo refresco.
     */
    public function create()
    {
        $tamanos = DB::table('tamanosrefrescos')->get();
        $categorias = DB::table('categoriasprod')->get();
        
        return view('Refrescos.create', compact('tamanos', 'categorias'));
    }

    /**
     * Guardar en la tabla 'Refrescos'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_tamano' => 'required|integer',
            'id_cat' => 'required|integer'
        ]);

        DB::table('refrescos')->insert([
            'nombre' => $request->nombre,
            'id_tamano' => $request->id_tamano,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('refrescos.index')->with('success', 'Refresco añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_refresco'.
     */
    public function edit($id)
    {
        $refresco = DB::table('refrescos')->where('id_refresco', $id)->first();
        
        if (!$refresco) {
            return redirect()->route('refrescos.index')->with('error', 'Registro no encontrado.');
        }

        $tamanos = DB::table('tamanosrefrescos')->get();
        $categorias = DB::table('categoriasprod')->get();

        return view('Refrescos.edit', compact('refresco', 'tamanos', 'categorias'));
    }

    /**
     * Actualizar registro en 'Refrescos'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_tamano' => 'required|integer',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('refrescos')->where('id_refresco', $id)->update([
            'nombre' => $request->nombre,
            'id_tamano' => $request->id_tamano,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('refrescos.index')->with('success', 'Refresco actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'Refrescos'.
     */
    public function destroy($id)
    {
        DB::table('refrescos')->where('id_refresco', $id)->delete();
        return redirect()->route('refrescos.index')->with('success', 'Refresco eliminado correctamente.');
    }
}