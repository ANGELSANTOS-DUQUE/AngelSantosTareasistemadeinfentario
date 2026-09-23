<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Empresa ───────────────────────────────────────────────
        $empresa = Empresa::firstOrCreate(
            ['nombre' => 'Distribuidora La Central, S.A.'],
            [
                'identificacion_fiscal' => 'J-12345678-9',
                'direccion' => 'Av. Comercio 456, Zona Centro',
                'telefono'  => '555-1234',
                'correo'    => 'contacto@lacentral.com',
                'estado'    => true,
            ]
        );

        // ─── 2. Sucursales ────────────────────────────────────────────
        $sucursalPrincipal = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Central', 'empresa_id' => $empresa->id],
            ['direccion' => 'Av. Principal 100', 'telefono' => '555-0001', 'estado' => true]
        );
        $sucursalNorte = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Norte', 'empresa_id' => $empresa->id],
            ['direccion' => 'Calle 15 Norte 200', 'telefono' => '555-0002', 'estado' => true]
        );

        // ─── 3. Usuarios ──────────────────────────────────────────────
        $adminEmpresa = User::firstOrCreate(
            ['email' => 'admin@lacentral.com'],
            [
                'name'              => 'Carlos Administrador',
                'password'          => Hash::make('password'),
                'empresa_id'        => $empresa->id,
                'estado'            => true,
                'email_verified_at' => now(),
            ]
        );
        $adminEmpresa->assignRole('admin_empresa');

        $encargado1 = User::firstOrCreate(
            ['email' => 'encargado1@lacentral.com'],
            [
                'name'              => 'Ana Martínez',
                'password'          => Hash::make('password'),
                'empresa_id'        => $empresa->id,
                'estado'            => true,
                'email_verified_at' => now(),
            ]
        );
        $encargado1->assignRole('encargado_area');

        $encargado2 = User::firstOrCreate(
            ['email' => 'encargado2@lacentral.com'],
            [
                'name'              => 'Roberto Sánchez',
                'password'          => Hash::make('password'),
                'empresa_id'        => $empresa->id,
                'estado'            => true,
                'email_verified_at' => now(),
            ]
        );
        $encargado2->assignRole('encargado_area');

        $lector = User::firstOrCreate(
            ['email' => 'lector@lacentral.com'],
            [
                'name'              => 'María Lectora',
                'password'          => Hash::make('password'),
                'empresa_id'        => $empresa->id,
                'estado'            => true,
                'email_verified_at' => now(),
            ]
        );
        $lector->assignRole('consulta');

        // ─── 4. Áreas ─────────────────────────────────────────────────
        $bodegaPrincipal = Area::firstOrCreate(
            ['nombre' => 'Bodega Principal', 'sucursal_id' => $sucursalPrincipal->id],
            ['descripcion' => 'Almacenamiento principal de productos', 'encargado_id' => $encargado1->id, 'estado' => true]
        );
        $recepcion = Area::firstOrCreate(
            ['nombre' => 'Recepción y Despacho', 'sucursal_id' => $sucursalPrincipal->id],
            ['descripcion' => 'Área de recepción y despacho de mercancía', 'encargado_id' => $encargado1->id, 'estado' => true]
        );
        $exhibicion = Area::firstOrCreate(
            ['nombre' => 'Sala de Exhibición', 'sucursal_id' => $sucursalPrincipal->id],
            ['descripcion' => 'Área de exhibición de productos al público', 'encargado_id' => $encargado2->id, 'estado' => true]
        );
        $bodegaNorte = Area::firstOrCreate(
            ['nombre' => 'Bodega Norte', 'sucursal_id' => $sucursalNorte->id],
            ['descripcion' => 'Almacenamiento en sucursal norte', 'encargado_id' => $encargado2->id, 'estado' => true]
        );
        $oficinaNorte = Area::firstOrCreate(
            ['nombre' => 'Oficina Norte', 'sucursal_id' => $sucursalNorte->id],
            ['descripcion' => 'Suministros de oficina sucursal norte', 'encargado_id' => $encargado2->id, 'estado' => true]
        );

        // ─── 5. Categorías ────────────────────────────────────────────
        $catElectro = Categoria::firstOrCreate(['nombre' => 'Electrónica', 'empresa_id' => $empresa->id], ['descripcion' => 'Equipos y accesorios electrónicos']);
        $catOficina = Categoria::firstOrCreate(['nombre' => 'Oficina', 'empresa_id' => $empresa->id], ['descripcion' => 'Artículos de papelería y oficina']);
        $catLimpieza = Categoria::firstOrCreate(['nombre' => 'Limpieza', 'empresa_id' => $empresa->id], ['descripcion' => 'Productos de limpieza e higiene']);
        $catHerram = Categoria::firstOrCreate(['nombre' => 'Herramientas', 'empresa_id' => $empresa->id], ['descripcion' => 'Herramientas y equipos de trabajo']);

        // ─── 6. Unidades de Medida (obtener existentes) ────────────────
        $uniUnd  = UnidadMedida::where('abreviatura', 'und')->first()  ?? UnidadMedida::create(['nombre' => 'Unidad', 'abreviatura' => 'und']);
        $uniCaja = UnidadMedida::where('abreviatura', 'caja')->first() ?? UnidadMedida::create(['nombre' => 'Caja', 'abreviatura' => 'caja']);
        $uniLt   = UnidadMedida::where('abreviatura', 'lt')->first()   ?? UnidadMedida::create(['nombre' => 'Litro', 'abreviatura' => 'lt']);
        $uniKg   = UnidadMedida::where('abreviatura', 'kg')->first()   ?? UnidadMedida::create(['nombre' => 'Kilogramo', 'abreviatura' => 'kg']);

        // ─── 7. Proveedores ───────────────────────────────────────────
        $provTech = Proveedor::firstOrCreate(
            ['nombre' => 'TechSupplies S.A.', 'empresa_id' => $empresa->id],
            ['contacto' => 'Luis Torres', 'telefono' => '555-9001', 'correo' => 'ventas@techsupplies.com']
        );
        $provOficina = Proveedor::firstOrCreate(
            ['nombre' => 'Distribuidora Papel y Más', 'empresa_id' => $empresa->id],
            ['contacto' => 'Elena Vargas', 'telefono' => '555-9002', 'correo' => 'contacto@papelmas.com']
        );
        $provLimpieza = Proveedor::firstOrCreate(
            ['nombre' => 'CleanPro Industrial', 'empresa_id' => $empresa->id],
            ['contacto' => 'Pedro Montoya', 'telefono' => '555-9003', 'correo' => 'ventas@cleanpro.com']
        );

        // ─── 8. Ítems ─────────────────────────────────────────────────
        $items = [
            // Electrónica
            ['sku' => 'ELEC-001', 'nombre' => 'Laptop HP ProBook 14"', 'categoria_id' => $catElectro->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 3, 'costo_unitario' => 850.00],
            ['sku' => 'ELEC-002', 'nombre' => 'Monitor Samsung 27" Full HD', 'categoria_id' => $catElectro->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 2, 'costo_unitario' => 320.00],
            ['sku' => 'ELEC-003', 'nombre' => 'Teclado Mecánico Logitech', 'categoria_id' => $catElectro->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 5, 'costo_unitario' => 75.00],
            ['sku' => 'ELEC-004', 'nombre' => 'Mouse Inalámbrico', 'categoria_id' => $catElectro->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 10, 'costo_unitario' => 25.00],
            ['sku' => 'ELEC-005', 'nombre' => 'Disco Duro Externo 1TB', 'categoria_id' => $catElectro->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 5, 'costo_unitario' => 65.00],
            // Oficina
            ['sku' => 'OFIC-001', 'nombre' => 'Resma Papel Bond A4', 'categoria_id' => $catOficina->id, 'unidad_medida_id' => $uniCaja->id, 'proveedor_id' => $provOficina->id, 'stock_minimo' => 20, 'costo_unitario' => 8.50],
            ['sku' => 'OFIC-002', 'nombre' => 'Bolígrafos BIC (caja x12)', 'categoria_id' => $catOficina->id, 'unidad_medida_id' => $uniCaja->id, 'proveedor_id' => $provOficina->id, 'stock_minimo' => 15, 'costo_unitario' => 3.00],
            ['sku' => 'OFIC-003', 'nombre' => 'Carpetas de Archivado', 'categoria_id' => $catOficina->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provOficina->id, 'stock_minimo' => 30, 'costo_unitario' => 1.50],
            ['sku' => 'OFIC-004', 'nombre' => 'Grapadora Industrial', 'categoria_id' => $catOficina->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provOficina->id, 'stock_minimo' => 5, 'costo_unitario' => 12.00],
            // Limpieza
            ['sku' => 'LIMP-001', 'nombre' => 'Detergente Industrial 5L', 'categoria_id' => $catLimpieza->id, 'unidad_medida_id' => $uniLt->id, 'proveedor_id' => $provLimpieza->id, 'stock_minimo' => 10, 'costo_unitario' => 18.00],
            ['sku' => 'LIMP-002', 'nombre' => 'Desinfectante Multiusos 1L', 'categoria_id' => $catLimpieza->id, 'unidad_medida_id' => $uniLt->id, 'proveedor_id' => $provLimpieza->id, 'stock_minimo' => 15, 'costo_unitario' => 7.50],
            ['sku' => 'LIMP-003', 'nombre' => 'Mopa de Microfibra', 'categoria_id' => $catLimpieza->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provLimpieza->id, 'stock_minimo' => 5, 'costo_unitario' => 22.00],
            // Herramientas
            ['sku' => 'HERR-001', 'nombre' => 'Taladro Inalámbrico Bosch', 'categoria_id' => $catHerram->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 3, 'costo_unitario' => 145.00],
            ['sku' => 'HERR-002', 'nombre' => 'Juego Destornilladores (12 pzas)', 'categoria_id' => $catHerram->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 5, 'costo_unitario' => 35.00],
            ['sku' => 'HERR-003', 'nombre' => 'Cinta Métrica 5m', 'categoria_id' => $catHerram->id, 'unidad_medida_id' => $uniUnd->id, 'proveedor_id' => $provTech->id, 'stock_minimo' => 8, 'costo_unitario' => 9.00],
        ];

        $itemModels = [];
        foreach ($items as $itemData) {
            $item = Item::firstOrCreate(
                ['sku' => $itemData['sku'], 'empresa_id' => $empresa->id],
                array_merge($itemData, ['empresa_id' => $empresa->id, 'estado' => true, 'descripcion' => null])
            );
            $itemModels[$itemData['sku']] = $item;
        }

        // ─── 9. Stock Inicial (Inventario por Área) ───────────────────
        $stockDistribution = [
            // [sku, area, cantidad]
            ['ELEC-001', $bodegaPrincipal->id, 12],
            ['ELEC-001', $bodegaNorte->id, 4],
            ['ELEC-002', $bodegaPrincipal->id, 8],
            ['ELEC-002', $exhibicion->id, 3],
            ['ELEC-003', $bodegaPrincipal->id, 20],
            ['ELEC-003', $bodegaNorte->id, 10],
            ['ELEC-004', $bodegaPrincipal->id, 35],
            ['ELEC-004', $recepcion->id, 5],
            ['ELEC-005', $bodegaPrincipal->id, 18],
            ['OFIC-001', $bodegaPrincipal->id, 80],
            ['OFIC-001', $oficinaNorte->id, 30],
            ['OFIC-002', $bodegaPrincipal->id, 45],
            ['OFIC-002', $oficinaNorte->id, 20],
            ['OFIC-003', $bodegaPrincipal->id, 3],   // bajo mínimo
            ['OFIC-004', $bodegaPrincipal->id, 2],   // bajo mínimo
            ['LIMP-001', $bodegaPrincipal->id, 25],
            ['LIMP-001', $bodegaNorte->id, 8],
            ['LIMP-002', $bodegaPrincipal->id, 0],   // agotado
            ['LIMP-003', $bodegaPrincipal->id, 7],
            ['HERR-001', $bodegaPrincipal->id, 5],
            ['HERR-002', $bodegaPrincipal->id, 10],
            ['HERR-003', $bodegaPrincipal->id, 15],
            ['HERR-003', $bodegaNorte->id, 5],
        ];

        foreach ($stockDistribution as [$sku, $areaId, $cantidad]) {
            InventarioArea::updateOrCreate(
                ['item_id' => $itemModels[$sku]->id, 'area_id' => $areaId],
                ['cantidad' => $cantidad]
            );
        }

        // ─── 10. Movimientos Históricos ───────────────────────────────
        $superAdminUser = User::where('email', 'superadmin@inventario.com')->first();

        $movimientos = [
            ['item_sku' => 'ELEC-001', 'tipo' => 'entrada', 'cantidad' => 20, 'area_origen_id' => null, 'area_destino_id' => $bodegaPrincipal->id, 'motivo' => 'Compra inicial de equipos Q3', 'dias_atras' => 30],
            ['item_sku' => 'ELEC-004', 'tipo' => 'entrada', 'cantidad' => 50, 'area_origen_id' => null, 'area_destino_id' => $bodegaPrincipal->id, 'motivo' => 'Reposición de stock mouse', 'dias_atras' => 25],
            ['item_sku' => 'OFIC-001', 'tipo' => 'entrada', 'cantidad' => 100, 'area_origen_id' => null, 'area_destino_id' => $bodegaPrincipal->id, 'motivo' => 'Compra trimestral de papel', 'dias_atras' => 20],
            ['item_sku' => 'ELEC-001', 'tipo' => 'traslado', 'cantidad' => 4, 'area_origen_id' => $bodegaPrincipal->id, 'area_destino_id' => $bodegaNorte->id, 'motivo' => 'Asignación a sucursal norte', 'dias_atras' => 18],
            ['item_sku' => 'OFIC-001', 'tipo' => 'traslado', 'cantidad' => 30, 'area_origen_id' => $bodegaPrincipal->id, 'area_destino_id' => $oficinaNorte->id, 'motivo' => 'Dotación oficina norte', 'dias_atras' => 15],
            ['item_sku' => 'ELEC-004', 'tipo' => 'salida', 'cantidad' => 10, 'area_origen_id' => $bodegaPrincipal->id, 'area_destino_id' => null, 'motivo' => 'Entrega a departamento TI', 'dias_atras' => 12],
            ['item_sku' => 'LIMP-001', 'tipo' => 'entrada', 'cantidad' => 30, 'area_origen_id' => null, 'area_destino_id' => $bodegaPrincipal->id, 'motivo' => 'Reposición mensual limpieza', 'dias_atras' => 10],
            ['item_sku' => 'OFIC-003', 'tipo' => 'salida', 'cantidad' => 47, 'area_origen_id' => $bodegaPrincipal->id, 'area_destino_id' => null, 'motivo' => 'Distribución entre departamentos', 'dias_atras' => 8],
            ['item_sku' => 'LIMP-002', 'tipo' => 'salida', 'cantidad' => 20, 'area_origen_id' => $bodegaPrincipal->id, 'area_destino_id' => null, 'motivo' => 'Uso en limpieza mensual', 'dias_atras' => 5],
            ['item_sku' => 'ELEC-003', 'tipo' => 'ajuste', 'cantidad' => 3, 'area_origen_id' => null, 'area_destino_id' => $bodegaNorte->id, 'motivo' => 'Ajuste por conteo físico de inventario', 'dias_atras' => 3],
            ['item_sku' => 'HERR-001', 'tipo' => 'entrada', 'cantidad' => 5, 'area_origen_id' => null, 'area_destino_id' => $bodegaPrincipal->id, 'motivo' => 'Compra herramientas nuevas', 'dias_atras' => 2],
            ['item_sku' => 'ELEC-002', 'tipo' => 'traslado', 'cantidad' => 3, 'area_origen_id' => $bodegaPrincipal->id, 'area_destino_id' => $exhibicion->id, 'motivo' => 'Exhibición de monitores en sala', 'dias_atras' => 1],
        ];

        foreach ($movimientos as $mov) {
            $item = $itemModels[$mov['item_sku']];
            MovimientoInventario::create([
                'item_id'        => $item->id,
                'tipo'           => $mov['tipo'],
                'cantidad'       => $mov['cantidad'],
                'area_origen_id' => $mov['area_origen_id'],
                'area_destino_id' => $mov['area_destino_id'],
                'usuario_id'     => $adminEmpresa->id,
                'motivo'         => $mov['motivo'],
                'created_at'     => now()->subDays($mov['dias_atras']),
            ]);
        }

        $this->command->info('✅ DemoDataSeeder completado:');
        $this->command->info('   - 1 Empresa: Distribuidora La Central, S.A.');
        $this->command->info('   - 2 Sucursales, 5 Áreas');
        $this->command->info('   - 4 Usuarios (admin, 2 encargados, lector)');
        $this->command->info('   - 15 Ítems distribuidos en áreas');
        $this->command->info('   - 12 Movimientos históricos');
        $this->command->info('');
        $this->command->info('Credenciales:');
        $this->command->info('  superadmin@inventario.com / password');
        $this->command->info('  admin@lacentral.com       / password');
        $this->command->info('  encargado1@lacentral.com  / password');
        $this->command->info('  lector@lacentral.com      / password');
    }
}
