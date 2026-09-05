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
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'integer|min:0|max:6',
        ]);

        // Si no se manda una clave manual, se genera una limpia basada en el nombre
        $clave = $request->filled('clave') 
            ? Str::slug($request->clave, '_') 
            : Str::slug($request->nombre, '_');

        Promocion::create([
            'nombre' => $request->nombre,
            'clave'  => $clave,
            'activa' => false, // Por defecto inicia desactivada (el switch manual)
            'dias_semana' => $request->input('dias_semana', []),
        ]);

        return redirect()->back()->with('status', 'Promoción agregada correctamente.');
    }

    /**
     * Actualiza solo los días de la semana en que la promo se activa sola,
     * sin tocar el switch manual (activa).
     */
    public function actualizarDias(Request $request, $id)
    {
        $request->validate([
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'integer|min:0|max:6',
        ]);

        $promocion = Promocion::findOrFail($id);
        $promocion->dias_semana = $request->input('dias_semana', []);
        $promocion->save();

        return redirect()->back()->with('status', "Días de '{$promocion->nombre}' actualizados.");
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