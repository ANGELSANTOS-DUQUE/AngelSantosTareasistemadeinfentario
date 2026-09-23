<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Empresa $empresa;
    protected UnidadMedida $unidad;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\UnidadesMedidaSeeder::class);

        $this->empresa = Empresa::create([
            'nombre' => 'Empresa Test Catalogo',
            'identificacion_fiscal' => '08011999887766',
            'estado' => true,
        ]);

        $this->user = User::create([
            'empresa_id' => $this->empresa->id,
            'name' => 'Admin Empresa',
            'email' => 'admin@empresa.com',
            'password' => bcrypt('password'),
            'estado' => true,
        ]);
        $this->user->assignRole('admin_empresa');

        $this->unidad = UnidadMedida::first();
    }

    public function test_can_create_categoria_and_proveedor(): void
    {
        $categoria = Categoria::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Abarrotes',
            'descripcion' => 'Productos de consumo masivo',
        ]);

        $this->assertDatabaseHas('categorias', ['nombre' => 'Abarrotes', 'empresa_id' => $this->empresa->id]);

        $proveedor = Proveedor::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Distribuidora del Norte',
            'contacto' => 'Juan Pérez',
            'telefono' => '+504 9900-1122',
        ]);

        $this->assertDatabaseHas('proveedores', ['nombre' => 'Distribuidora del Norte', 'empresa_id' => $this->empresa->id]);
    }

    public function test_can_create_item_and_access_catalog(): void
    {
        $categoria = Categoria::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Lácteos',
        ]);

        $item = Item::create([
            'empresa_id' => $this->empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $this->unidad->id,
            'nombre' => 'Leche Descremada 1L',
            'sku' => 'SKU-LECHE-01',
            'costo_unitario' => 28.50,
            'stock_minimo' => 10,
            'estado' => true,
        ]);

        $this->assertDatabaseHas('items', ['sku' => 'SKU-LECHE-01', 'empresa_id' => $this->empresa->id]);

        $response = $this->actingAs($this->user)->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('Leche Descremada 1L');
        $response->assertSee('SKU-LECHE-01');

        $responseInv = $this->actingAs($this->user)->get(route('items.inventario', $item));
        $responseInv->assertStatus(200);
    }
}
