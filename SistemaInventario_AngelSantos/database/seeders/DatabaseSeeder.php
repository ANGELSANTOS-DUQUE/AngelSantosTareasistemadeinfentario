<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UnidadesMedidaSeeder::class,
            DemoDataSeeder::class,
        ]);

        // Crear usuario Super Administrador inicial si no existe
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@inventario.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'estado' => true,
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole('super_admin');
    }
}
