@extends('layouts.app')

@section('content')
<div x-data="{ mostrarModalEliminar: false, formAccion: '', nombrePromo: '' }" class="max-w-7xl mx-auto space-y-6">

    <!-- Encabezado con gradiente -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 p-8 shadow-lg">
        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
        <div class="absolute -right-2 -bottom-10 w-32 h-32 rounded-full bg-white/10"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Promociones</h1>
                <p class="text-sm text-amber-50 mt-0.5">Enciende promociones a mano o prográmalas por día de la semana</p>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="flex items-center gap-3 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-semibold text-green-800">{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <ul class="text-sm font-semibold text-red-800 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tarjetas de resumen -->
    @php
        $totalPromos = $promociones->count();
        $activasHoy = $promociones->filter(fn($p) => $p->estaActivaHoy())->count();
        $porCalendario = $promociones->filter(fn($p) => $p->estaActivaHoy() && !$p->activa)->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-gray-800 leading-none">{{ $totalPromos }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">Registradas</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-emerald-600 leading-none">{{ $activasHoy }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">Activas hoy</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-amber-600 leading-none">{{ $porCalendario }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">Por calendario hoy</p>
            </div>
        </div>
    </div>

    <!-- Formulario para agregar una nueva promoción -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Agregar nueva promoción</h2>
        </div>
        <form action="{{ route('promociones.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="nombre" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nombre de la promoción</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: 2x1 en Pizzas"
                        class="block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-amber-400 focus:ring-amber-400 focus:bg-white sm:text-sm p-3 border transition-colors">
                </div>

                <div>
                    <label for="clave" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Clave interna (opcional)</label>
                    <input type="text" name="clave" id="clave" placeholder="Ej: pizza_2x1"
                        class="block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-amber-400 focus:ring-amber-400 focus:bg-white sm:text-sm p-3 border font-mono transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Días automáticos (opcional)</label>
                <p class="text-xs text-gray-400 mb-3">Si marcas días, la promo se activa sola esos días sin tocar el switch. El switch manual sigue funcionando para prenderla o apagarla cualquier otro día.</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (['1' => 'Lunes', '2' => 'Martes', '3' => 'Miércoles', '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sábado', '0' => 'Domingo'] as $valor => $etiqueta)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="dias_semana[]" value="{{ $valor }}" class="peer sr-only">
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold border-2 border-gray-200 text-gray-500 bg-gray-50 peer-checked:bg-gradient-to-br peer-checked:from-amber-500 peer-checked:to-orange-500 peer-checked:border-amber-500 peer-checked:text-white peer-checked:shadow-md transition-all select-none">
                                {{ $etiqueta }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <button type="submit" class="bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-6 py-3 rounded-lg text-sm font-bold flex items-center gap-2 transition-all shadow-md hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Guardar promoción
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Promociones -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
            </div>
            <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Promociones registradas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] tracking-widest font-bold border-b-2 border-gray-100">
                        <th class="px-6 py-4 font-semibold">Nombre</th>
                        <th class="px-6 py-4 font-semibold">Clave</th>
                        <th class="px-6 py-4 font-semibold">Días automáticos</th>
                        <th class="px-6 py-4 font-semibold">Estado hoy</th>
                        <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @php $nombresDias = ['0' => 'D', '1' => 'L', '2' => 'M', '3' => 'M', '4' => 'J', '5' => 'V', '6' => 'S']; @endphp
                    @forelse ($promociones as $promo)
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $promo->estaActivaHoy() ? 'from-amber-400 to-orange-500' : 'from-gray-200 to-gray-300' }} flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-900 font-bold text-sm">{{ $promo->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                                {{ $promo->clave }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('promociones.dias', $promo->id_promo) }}" method="POST" class="flex flex-wrap items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    @foreach ($nombresDias as $valor => $etiqueta)
                                        <label class="cursor-pointer" title="{{ ['0'=>'Domingo','1'=>'Lunes','2'=>'Martes','3'=>'Miércoles','4'=>'Jueves','5'=>'Viernes','6'=>'Sábado'][$valor] }}">
                                            <input type="checkbox" name="dias_semana[]" value="{{ $valor }}"
                                                {{ in_array((int) $valor, $promo->dias_semana ?? []) ? 'checked' : '' }}
                                                class="peer sr-only" onchange="this.closest('form').requestSubmit()">
                                            <span class="w-6 h-6 inline-flex items-center justify-center rounded-md text-[10px] font-black border-2 border-gray-200 text-gray-400 bg-gray-50 peer-checked:bg-gradient-to-br peer-checked:from-amber-500 peer-checked:to-orange-500 peer-checked:border-amber-500 peer-checked:text-white peer-checked:shadow-sm transition-all select-none">
                                                {{ $etiqueta }}
                                            </span>
                                        </label>
                                    @endforeach
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                @if ($promo->estaActivaHoy())
                                    <span class="px-3 py-1 inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest rounded-full bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Activa {{ $promo->activa ? '· switch' : '· calendario' }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest rounded-full bg-gray-100 text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                        Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <!-- Switch encender/apagar -->
                                    <form action="{{ route('promociones.toggle', $promo->id_promo) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="p-2.5 rounded-lg shadow-sm border transition-all flex items-center justify-center {{ $promo->activa ? 'bg-amber-50 border-amber-200 text-amber-600 hover:bg-amber-100' : 'bg-white border-gray-100 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}"
                                            title="{{ $promo->activa ? 'Apagar switch manual' : 'Encender switch manual' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                            </svg>
                                        </button>
                                    </form>

                                    <!-- Eliminar -->
                                    <button type="button"
                                        @click="formAccion = '{{ route('promociones.destroy', $promo->id_promo) }}'; nombrePromo = '{{ $promo->nombre }}'; mostrarModalEliminar = true"
                                        class="p-2.5 bg-white text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg shadow-sm border border-gray-100 transition-all flex items-center justify-center"
                                        title="Eliminar promoción">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="bg-gradient-to-br from-amber-100 to-orange-100 p-5 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </span>
                                    <p class="text-gray-500 text-sm font-semibold">Todavía no hay promociones creadas</p>
                                    <p class="text-gray-400 text-xs">Agrega una desde el formulario de arriba para empezar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div x-show="mostrarModalEliminar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 overflow-hidden text-center" @click.away="mostrarModalEliminar = false" x-transition.scale.origin.bottom>
            <div class="w-16 h-16 rounded-full bg-red-50 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">¿Eliminar <span x-text="nombrePromo"></span>?</h3>
            <p class="text-gray-500 text-sm mb-6">Esta acción no se puede deshacer.</p>
            <div class="flex gap-3">
                <button @click="mostrarModalEliminar = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition-colors text-sm">
                    Cancelar
                </button>
                <form :action="formAccion" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
