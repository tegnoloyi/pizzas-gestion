@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Administración de Promociones</h1>

        @if (session('status'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario para agregar una nueva promoción -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Agregar Nueva Promoción</h2>
            <form action="{{ route('promociones.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Promoción</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: 2x1 en Pizzas" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                </div>

                <div>
                    <label for="clave" class="block text-sm font-medium text-gray-700">Clave Interna (Opcional)</label>
                    <input type="text" name="clave" id="clave" placeholder="Ej: promo_2x1" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Guardar Promoción
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Promociones -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Promociones Registradas</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clave</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($promociones as $promo)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $promo->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    {{ $promo->clave }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($promo->activa)
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activa
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <!-- Botón Encender / Apagar -->
                                    <form action="{{ route('promociones.toggle', $promo->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs rounded text-white {{ $promo->activa ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700' }}">
                                            {{ $promo->activa ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('promociones.destroy', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta promoción?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-xs rounded text-white bg-red-600 hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay promociones creadas. Agrega una desde el formulario de arriba.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection