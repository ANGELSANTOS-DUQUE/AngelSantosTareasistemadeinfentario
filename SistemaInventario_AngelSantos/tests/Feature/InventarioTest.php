<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\InventarioService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected Empresa $empresa;
    protected Sucursal $sucursal;
    protected Area $areaOrigen;
    protected Area $areaDestino;
    protected Item $item;
    protected InventarioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\UnidadesMedidaSeeder::class);

        $this->empresa = Empresa::create([
            'nombre' => 'Empresa Inventario Test',
            'identificacion_fiscal' => '08011999123456',
            'estado' => true,
        ]);

        $this->user1 = User::create([
            'empresa_id' => $this->empresa->id,
            'name' => 'Encargado Origen',
            'email' => 'origen@empresa.com',
            'password' => bcrypt('password'),
            'estado' => true,
        ]);
        $this->user1->assignRole('encargado_area');

        $this->user2 = User::create([
            'empresa_id' => $this->empresa->id,
            'name' => 'Encargado Destino',
            'email' => 'destino@empresa.com',
            'password' => bcrypt('password'),
            'estado' => true,
        ]);
        $this->user2->assignRole('encargado_area');

        $this->sucursal = Sucursal::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Sucursal Principal',
            'estado' => true,
        ]);

        $this->areaOrigen = Area::create([
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Bodega Central',
            'encargado_id' => $this->user1->id,
            'estado' => true,
        ]);

        $this->areaDestino = Area::create([
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Mostrador Ventas',
            'encargado_id' => $this->user2->id,
            'estado' => true,
        ]);

        $categoria = Categoria::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Bebidas',
        ]);

        $unidad = UnidadMedida::first();

        $this->item = Item::create([
            'empresa_id' => $this->empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'nombre' => 'Jugo de Naranja 500ml',
            'sku' => 'JUGO-500ML',
            'costo_unitario' => 15.00,
            'stock_minimo' => 5,
            'estado' => true,
        ]);

        $this->service = app(InventarioService::class);
    }

    public function test_entrada_de_inventario_incrementa_stock_y_crea_bitacora(): void
    {
        $movimiento = $this->service->registrarEntrada(
            $this->item->id,
            $this->areaOrigen->id,
            20,
            $this->user1->id,
            'Compra a proveedor'
        );

        $this->assertEquals(20, $this->service->getStock($this->item->id, $this->areaOrigen->id));
        $this->assertDatabaseHas('movimientos_inventario', [
            'id' => $movimiento->id,
            'tipo' => 'entrada',
            'cantidad' => 20,
            'area_destino_id' => $this->areaOrigen->id,
        ]);
    }

    public function test_salida_de_inventario_decrementa_y_valida_stock_insuficiente(): void
    {
        // 1. Ingresar 10 unidades
        $this->service->registrarEntrada($this->item->id, $this->areaOrigen->id, 10, $this->user1->id);

        // 2. Dar salida a 4 unidades
        $this->service->registrarSalida($this->item->id, $this->areaOrigen->id, 4, $this->user1->id, 'Consumo interno');
        $this->assertEquals(6, $this->service->getStock($this->item->id, $this->areaOrigen->id));

        // 3. Intentar sacar más de lo disponible (10 unidades cuando solo hay 6)
        $this->expectException(Exception::class);
        $this->service->registrarSalida($this->item->id, $this->areaOrigen->id, 10, $this->user1->id);
    }

    public function test_traslado_entre_areas_mueve_stock_y_reasigna_responsable(): void
    {
        // 1. Ingresar 30 unidades en Bodega Central (user1 encargado)
        $this->service->registrarEntrada($this->item->id, $this->areaOrigen->id, 30, $this->user1->id);

        // 2. Trasladar 12 unidades a Mostrador Ventas (user2 encargado)
        $mov = $this->service->registrarTraslado(
            $this->item->id,
            $this->areaOrigen->id,
            $this->areaDestino->id,
            12,
            $this->user1->id,
            'Traslado para venta diaria'
        );

        $this->assertEquals(18, $this->service->getStock($this->item->id, $this->areaOrigen->id));
        $this->assertEquals(12, $this->service->getStock($this->item->id, $this->areaDestino->id));

        $this->assertStringContainsString('Encargado Destino', $mov->motivo);
    }

    public function test_ajuste_manual_de_inventario_audita_cambios(): void
    {
        // 1. Ingresar 15 unidades
        $this->service->registrarEntrada($this->item->id, $this->areaOrigen->id, 15, $this->user1->id);

        // 2. Ajustar por conteo físico a 18 unidades (+3)
        $mov = $this->service->registrarAjuste(
            $this->item->id,
            $this->areaOrigen->id,
            18,
            $this->user1->id,
            'Conteo físico mensual auditado'
        );

        $this->assertEquals(18, $this->service->getStock($this->item->id, $this->areaOrigen->id));
        $this->assertStringContainsString('Conteo físico mensual auditado', $mov->motivo);
    }
}
