<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarraController extends Controller
{
    /**
     * Mostrar la lista de productos de la barra.
     * Tablas corregidas: 'barra', 'especialidades' y 'categoriasprod'.
     */
    public function index()
    {
        $barras = DB::table('barra')
            ->join('especialidades', 'barra.id_especialidad', '=', 'especialidades.id_esp')
            ->join('categoriasprod', 'barra.id_cat', '=', 'categoriasprod.id_cat')
            ->select(
                'barra.id_barr', 
                'especialidades.nombre as especialidad', 
                'barra.precio', 
                'categoriasprod.descripcion as categoria'
            )
            ->get();

        return view('Barra.index', compact('barras'));
    }

    /**
     * Formulario para añadir un nuevo producto.
     */
    public function create()
    {
        $especialidades = DB::table('especialidades')->get();
        $categorias = DB::table('categoriasprod')->get();
        
        return view('Barra.create', compact('especialidades', 'categorias'));
    }

    /**
     * Guardar en la tabla 'barra'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_especialidad' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);

        DB::table('barra')->insert([
            'id_especialidad' => $request->id_especialidad,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);

        return redirect()->route('barra.index')->with('success', 'Producto de Barra añadido correctamente.');
    }

    /**
     * Editar registro usando 'id_barr'.
     */
    public function edit($id)
    {
        $barra = DB::table('barra')->where('id_barr', $id)->first();
        
        if (!$barra) {
            return redirect()->route('barra.index')->with('error', 'Registro no encontrado.');
        }

        $especialidades = DB::table('especialidades')->get();
        $categorias = DB::table('categoriasprod')->get();

        return view('Barra.edit', compact('barra', 'especialidades', 'categorias'));
    }

    /**
     * Actualizar registro en 'barra'.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_especialidad' => 'required|integer',
            'precio' => 'required|numeric',
            'id_cat' => 'required|integer'
        ]);
        
        DB::table('barra')->where('id_barr', $id)->update([
            'id_especialidad' => $request->id_especialidad,
            'precio' => $request->precio,
            'id_cat' => $request->id_cat
        ]);
        
        return redirect()->route('barra.index')->with('success', 'Producto de Barra actualizado correctamente.');
    }

    /**
     * Eliminar registro de 'barra'.
     */
    public function destroy($id)
    {
        DB::table('barra')->where('id_barr', $id)->delete();
        return redirect()->route('barra.index')->with('success', 'Producto de Barra eliminado correctamente.');
    }
}