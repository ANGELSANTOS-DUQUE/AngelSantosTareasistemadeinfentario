<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos del sistema
        $permissions = [
            // Empresas
            'empresas.view',
            'empresas.create',
            'empresas.edit',
            'empresas.delete',

            // Sucursales
            'sucursales.view',
            'sucursales.create',
            'sucursales.edit',
            'sucursales.delete',

            // Áreas
            'areas.view',
            'areas.create',
            'areas.edit',
            'areas.delete',

            // Catálogo (Categorías, Unidades, Proveedores, Ítems)
            'catalogo.view',
            'catalogo.create',
            'catalogo.edit',
            'catalogo.delete',

            // Inventario y Movimientos
            'inventario.view',
            'inventario.entrada',
            'inventario.salida',
            'inventario.traslado',
            'inventario.ajuste',

            // Reportes y Dashboard
            'dashboard.view',
            'reportes.view',
            'reportes.export',

            // Usuarios de empresa
            'usuarios.view',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 1. Super Admin (acceso total)
        $superAdminRole = Role::findOrCreate('super_admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // 2. Administrador de Empresa
        $adminEmpresaRole = Role::findOrCreate('admin_empresa');
        $adminEmpresaRole->givePermissionTo([
            'sucursales.view', 'sucursales.create', 'sucursales.edit', 'sucursales.delete',
            'areas.view', 'areas.create', 'areas.edit', 'areas.delete',
            'catalogo.view', 'catalogo.create', 'catalogo.edit', 'catalogo.delete',
            'inventario.view', 'inventario.entrada', 'inventario.salida', 'inventario.traslado', 'inventario.ajuste',
            'dashboard.view', 'reportes.view', 'reportes.export',
            'usuarios.view', 'usuarios.create', 'usuarios.edit', 'usuarios.delete',
        ]);

        // 3. Encargado de Área
        $encargadoRole = Role::findOrCreate('encargado_area');
        $encargadoRole->givePermissionTo([
            'areas.view',
            'catalogo.view',
            'inventario.view', 'inventario.entrada', 'inventario.salida', 'inventario.traslado',
            'dashboard.view', 'reportes.view',
        ]);

        // 4. Consulta / Solo Lectura
        $consultaRole = Role::findOrCreate('consulta');
        $consultaRole->givePermissionTo([
            'dashboard.view',
            'reportes.view',
            'reportes.export',
            'catalogo.view',
            'inventario.view',
        ]);
    }
}
