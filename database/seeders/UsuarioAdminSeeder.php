<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cargo: Administrador
        DB::table('cargos')->insertOrIgnore([
            'id_ca'  => 1,
            'nombre' => 'Administrador',
        ]);

        // 2. Sucursal principal
        $id_suc = DB::table('sucursal')->insertGetId([
            'nombre'    => 'Miraflores',
            'direccion' => 'Dirección Conocida',
            'telefono'  => '5500000000',
        ]);

        // 3. Empleado administrador
        DB::table('empleados')->insert([
            'nombre'    => 'Administrador',
            'direccion' => 'Dirección de prueba',
            'telefono'  => '1234567890',
            'id_ca'     => 1,
            'id_suc'    => $id_suc,
            'nickName'  => 'admin',
            'password'  => Hash::make('admin123'),
            'status'    => 1,
        ]);

        $this->command->info('✅ Cargo, sucursal y administrador creados correctamente.');
    }
}