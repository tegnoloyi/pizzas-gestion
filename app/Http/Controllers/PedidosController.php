<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidosController extends Controller
{
    public function index()
    {
        $id_sucursal = 1; 
        
        // 1. Buscar la caja que esté abierta actualmente
        $cajaAbierta = DB::table('caja')
            ->where('status', 1)
            ->where('id_suc', $id_sucursal)
            ->first();

        // 2. Si no hay caja abierta (se cerró turno), regresamos la vista vacía para limpiar el monitor
        if (!$cajaAbierta) {
            return view('Ventas.pedidos', ['pedidos' => []]);
        }

        // 3. Traemos TODOS los pedidos (Mesa, Mostrador, Domicilio) que pertenezcan ÚNICAMENTE a la caja abierta
        $pedidosRaw = DB::table('venta')
            ->leftJoin('pdomicilio', 'venta.id_venta', '=', 'pdomicilio.id_venta')
            ->leftJoin('clientes', 'pdomicilio.id_clie', '=', 'clientes.id_clie')
            ->leftJoin('direcciones', 'pdomicilio.id_dir', '=', 'direcciones.id_dir')
            ->where('venta.id_suc', $id_sucursal)
            ->where('venta.id_caja', $cajaAbierta->id_caja) 
            ->where('venta.status', '!=', 3) 
            ->select(
                'Venta.*', 
                'clientes.nombre as cnombre', 
                'clientes.apellido as capellido', 
                'clientes.telefono', 
                'direcciones.calle', 
                'direcciones.manzana', 
                'direcciones.lote', 
                'direcciones.colonia', 
                'direcciones.referencia'
            )
            ->orderBy('venta.fecha_hora', 'asc') 
            ->get();

        $pedidos = [];
        foreach($pedidosRaw as $p) {
            if ($p->status == 2 || str_contains($p->comentarios ?? '', 'ENTREGADO')) {
                continue;
            }
            $pedidos[] = $p;
        }

        return view('Ventas.pedidos', compact('pedidos'));
    }

    public function cambiarStatus(Request $request, $id)
    {
        $venta = DB::table('venta')->where('id_venta', $id)->first();
        if(!$venta) return back()->with('error', 'Pedido no encontrado');

        $nuevoComentario = $venta->comentarios;
        $hora = Carbon::now()->format('h:i A');

        // ACCIÓN: ENVIAR CON REPARTIDOR (Solo Domicilio)
        if ($request->accion === 'en_camino') {
            $repartidor = $request->repartidor ?? 'No asignado';
            $nuevoComentario .= " | EN CAMINO ($hora) - Repartidor: $repartidor";
            
            DB::table('venta')->where('id_venta', $id)->update([
                'comentarios' => $nuevoComentario
            ]);
            return back()->with('success', "Pedido enviado en ruta con: $repartidor");
        }

        // ACCIÓN: MARCAR COMO ENTREGADO / SERVIDO
        if ($request->accion === 'entregado') {
            $nuevoComentario .= " | ENTREGADO ($hora)";
            
            DB::table('venta')->where('id_venta', $id)->update([
                'comentarios' => $nuevoComentario
            ]);
            return back()->with('success', 'Pedido marcado como completado y retirado del monitor.');
        }

        return back();
    }
}