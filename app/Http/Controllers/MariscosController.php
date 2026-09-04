<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MariscosController extends Controller
{
    /**
     * Mostrar la lista de pizzas de mariscos.
     * Tablas corregidas: 'pizzasmariscos', 'categoriasprod' y 'tamanospizza'.
     */
    public function index()
    {
        $mariscos = DB::table('pizzasmariscos')
            ->join('categoriasprod', 'pizzasmariscos.id_cat', '=', 'categoriasprod.id_cat')
            ->join('tamanospizza', 'pizzasmariscos.id_tamañop', '=', 'tamanospizza.id_tamañop')
            ->select(
                'pizzasmariscos.id_maris', 
                'pizzasmariscos.nombre', 
                'pizzasmariscos.descripcion', 
                'tamanospizza.tamano', 
                'categoriasprod.descripcion as categoria'
            )
            ->get();

        return view('Mariscos.index', compact('mariscos'));
    }

    /**
     * Formulario para crear una nueva pizza de mariscos.
     */
    public function create()
    {
        $categorias = DB::table('categoriasprod')->get();
        $tamanos = DB::table('tamanospizza')->get();
        
        return view('Mariscos.create', compact('categorias', 'tamanos'));
    }

    /**
     * Guardar en la tabla 'pizzasmariscos'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'id_tamañop' => 'required|integer',
            'id_cat' => 'required|integer'
        ]);

        DB::table('pizzasmariscos')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_tamañop' => $request->id_tamañop,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('mariscos.index')->with('success', 'Pizza de Mariscos añadida correctamente.');
    }

    /**
     * Editar registro usando 'id_maris'.
     */
    public function edit($id)
    {
        $marisco = DB::table('pizzasmariscos')->where('id_maris', $id)->first();
        
        if (!$marisco) {
            return redirect()->route('mariscos.index')->with('error', 'Registro no encontrado.');
        }

        $categorias = DB::table('categoriasprod')->get();
        $tamanos = DB::table('tamanospizza')->get();

        return view('Mariscos.edit', compact('marisco', 'categorias', 'tamanos'));
    }

    /**
     * Actualizar registro en 'pizzasmariscos'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'id_tamañop' => 'required|integer',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('pizzasmariscos')->where('id_maris', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_tamañop' => $request->id_tamañop,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('mariscos.index')->with('success', 'Pizza de Mariscos actualizada correctamente.');
    }

    /**
     * Eliminar registro de 'pizzasmariscos'.
     */
    public function destroy($id)
    {
        DB::table('pizzasmariscos')->where('id_maris', $id)->delete();
        return redirect()->route('mariscos.index')->with('success', 'Pizza de Mariscos eliminada correctamente.');
    }
}