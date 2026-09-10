<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentasController extends Controller
{
    /**
     * Muestra el historial de pedidos con Folios Virtuales (Sin fecha).
     * ACTUALIZADO: Solo muestra las ventas pertenecientes a la CAJA ABIERTA.
     */
   public function resume(Request $request)
{
    $id_sucursal = 1; // SUCURSAL MIRAFLORES

    // Manejo de Filtros
    $filtroFecha = $request->input('fecha', 'hoy');
    $filtroEstado = $request->input('estado', 'todos');

    // 1. Buscar si hay una caja abierta
    $cajaAbierta = DB::table('caja')
        ->where('status', 1)
        ->where('id_suc', $id_sucursal)
        ->first();

    // 2. Si no hay caja abierta y el filtro es "hoy", regresamos vacío.
    // (Opcional: Si quieres que puedan ver el historial de la semana aunque la caja esté cerrada, 
    // podrías quitar esta validación o ajustarla solo para cuando $filtroFecha == 'hoy')
    if (!$cajaAbierta && $filtroFecha == 'hoy') {
        return view('Ventas.resume', [
            'ventas' => collect([]), 
            'filtroFecha' => $filtroFecha, 
            'filtroEstado' => $filtroEstado
        ]);
    }

    // 3. Construimos la consulta base SIN el filtro de id_caja todavía
    $query = DB::table('venta')
        ->leftJoin('pdomicilio', 'venta.id_venta', '=', 'pdomicilio.id_venta')
        ->leftJoin('clientes', 'pdomicilio.id_clie', '=', 'clientes.id_clie')
        ->where('venta.id_suc', $id_sucursal)
        ->select(
            'venta.*', 
            'clientes.nombre as cnombre', 
            'clientes.apellido as capellido'
        )
        ->orderBy('venta.fecha_hora', 'desc');

    // 4. Aplicamos los filtros de fecha y turno condicionalmente
    if ($filtroFecha == 'hoy') {
        // Si es hoy, limitamos al turno actual (Caja abierta)
        if ($cajaAbierta) {
            $query->where('venta.id_caja', $cajaAbierta->id_caja);
        } else {
            // Fallback por si acaso (aunque el if de arriba lo previene)
            $query->whereDate('venta.fecha_hora', Carbon::today());
        }
    } elseif ($filtroFecha == 'semana') {
        // Aseguramos que tome desde el inicio del día 1 hasta el final del último día
        $inicioSemana = Carbon::now()->startOfWeek()->format('Y-m-d 00:00:00');
        $finSemana = Carbon::now()->endOfWeek()->format('Y-m-d 23:59:59');
        
        $query->whereBetween('venta.fecha_hora', [$inicioSemana, $finSemana]);
    } elseif ($filtroFecha == 'mes') {
        $query->whereMonth('venta.fecha_hora', Carbon::now()->month)
              ->whereYear('venta.fecha_hora', Carbon::now()->year);
    }

    // 5. Filtro de Estado
    if ($filtroEstado !== 'todos') {
        $query->where('venta.status', $filtroEstado);
    }

    $ventas = $query->get();

    // 6. Formateo de datos (N+1 evitado en la medida de lo posible)
    foreach ($ventas as $v) {
        $v->folio_virtual = str_pad($v->id_venta, 5, '0', STR_PAD_LEFT);

        $v->total_productos = DB::table('detalleventa')
            ->where('id_venta', $v->id_venta)
            ->sum('cantidad');
        
        if ($v->tipo_servicio == 1) {
            $v->cliente_display = "Mesa " . ($v->mesa ?? '?') . " - " . ($v->nombreClie ?? 'Sin Nombre');
        } elseif ($v->tipo_servicio == 2) {
            $v->cliente_display = "Mostrador (Para Llevar)";
        } else {
            $v->cliente_display = trim(($v->cnombre ?? '') . ' ' . ($v->capellido ?? ''));
            if (empty($v->cliente_display)) $v->cliente_display = "Pedido a Domicilio";
        }
    }

    return view('Ventas.resume', compact('ventas', 'filtroFecha', 'filtroEstado'));
}
}