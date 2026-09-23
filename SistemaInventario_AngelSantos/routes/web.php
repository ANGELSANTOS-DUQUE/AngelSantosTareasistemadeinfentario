<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UnidadMedidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Estructura Organizacional
    Route::resource('empresas', EmpresaController::class);
    Route::resource('sucursales', SucursalController::class);
    Route::resource('areas', AreaController::class);

    // Catálogo Maestro
    Route::resource('categorias', CategoriaController::class);
    Route::resource('unidades', UnidadMedidaController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::get('items/{item}/inventario', [ItemController::class, 'inventario'])->name('items.inventario');
    Route::resource('items', ItemController::class);

    // Núcleo de Inventario y Movimientos
    Route::get('inventario/stock', [InventarioController::class, 'stock'])->name('inventario.stock');
    Route::get('api/stock-disponible', [MovimientoInventarioController::class, 'stockDisponible'])->name('api.stock.disponible');
    Route::get('movimientos', [MovimientoInventarioController::class, 'index'])->name('movimientos.index');
    Route::get('movimientos/create', [MovimientoInventarioController::class, 'create'])->name('movimientos.create');
    Route::post('movimientos', [MovimientoInventarioController::class, 'store'])->name('movimientos.store');
    Route::post('movimientos/entrada', [MovimientoInventarioController::class, 'store'])->name('movimientos.entrada');
    Route::post('movimientos/salida', [MovimientoInventarioController::class, 'store'])->name('movimientos.salida');
    Route::post('movimientos/traslado', [MovimientoInventarioController::class, 'store'])->name('movimientos.traslado');
    Route::post('movimientos/ajuste', [MovimientoInventarioController::class, 'store'])->name('movimientos.ajuste');

    // Reportes y Exportaciones
    Route::get('reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('reportes/movimientos', [ReporteController::class, 'movimientos'])->name('reportes.movimientos');
    Route::get('reportes/inventario/excel', [ReporteController::class, 'exportarInventarioExcel'])->name('reportes.inventario.excel');
    Route::get('reportes/inventario/pdf', [ReporteController::class, 'exportarInventarioPdf'])->name('reportes.inventario.pdf');
    Route::get('reportes/movimientos/excel', [ReporteController::class, 'exportarMovimientosExcel'])->name('reportes.movimientos.excel');
    Route::get('reportes/movimientos/pdf', [ReporteController::class, 'exportarMovimientosPdf'])->name('reportes.movimientos.pdf');
});

require __DIR__.'/auth.php';
