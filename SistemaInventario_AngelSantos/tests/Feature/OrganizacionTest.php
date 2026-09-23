<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_superadmin_can_access_empresas_index(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@inventario.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'estado' => true]
        );
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('empresas.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_empresa_sucursal_and_area(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@inventario.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'estado' => true]
        );

        $empresa = Empresa::create([
            'nombre' => 'Test Empresa Mipyme',
            'identificacion_fiscal' => '0801199000123',
            'direccion' => 'Colonia Palmira, Tegucigalpa',
            'estado' => true,
        ]);

        $this->assertDatabaseHas('empresas', ['identificacion_fiscal' => '0801199000123']);

        $sucursal = Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sucursal Principal',
            'direccion' => 'Local 101',
            'estado' => true,
        ]);

        $this->assertDatabaseHas('sucursales', ['nombre' => 'Sucursal Principal', 'empresa_id' => $empresa->id]);

        $area = Area::create([
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Bodega Central',
            'encargado_id' => $superAdmin->id,
            'estado' => true,
        ]);

        $this->assertDatabaseHas('areas', ['nombre' => 'Bodega Central', 'encargado_id' => $superAdmin->id]);
        $this->assertEquals($superAdmin->id, $area->encargado->id);
    }
}
