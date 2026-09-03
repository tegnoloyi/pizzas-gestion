<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::all();
        // Renderiza la vista dentro de la carpeta /promos
        return view('promos.index', compact('promociones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'clave'  => 'nullable|string|max:255|unique:promociones,clave',
        ]);

        // Si no se manda una clave manual, se genera una limpia basada en el nombre
        $clave = $request->filled('clave') 
            ? Str::slug($request->clave, '_') 
            : Str::slug($request->nombre, '_');

        Promocion::create([
            'nombre' => $request->nombre,
            'clave'  => $clave,
            'activa' => false, // Por defecto inicia desactivada
        ]);

        return redirect()->back()->with('status', 'Promoción agregada correctamente.');
    }

    public function toggle($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->activa = !$promocion->activa;
        $promocion->save();

        return redirect()->back()->with('status', "Estado de '{$promocion->nombre}' actualizado.");
    }

    public function destroy($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->delete();

        return redirect()->back()->with('status', 'Promoción eliminada correctamente.');
    }
}