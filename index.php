<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$config = require __DIR__ . '/app/config/config.php';

$route = $_GET['r'] ?? 'dashboard';

$routes = [
    'dashboard' => [App\Controllers\DashboardController::class, 'index'],
    'productos' => [App\Controllers\ProductosController::class, 'index'],
    'productos/restock' => [App\Controllers\ProductosController::class, 'restock'],
    'pedidos' => [App\Controllers\PedidosController::class, 'index'],
    'pedidos/create' => [App\Controllers\PedidosController::class, 'create'],
    'pedidos/update-status' => [App\Controllers\PedidosController::class, 'updateStatus'],
    'facturas' => [App\Controllers\FacturasController::class, 'index'],
    'facturas/generate' => [App\Controllers\FacturasController::class, 'generate'],
    'facturas/download' => [App\Controllers\FacturasController::class, 'download'],
    'reportes' => [App\Controllers\ReportesController::class, 'index'],
    'api/dashboard-metrics' => [App\Controllers\ApiController::class, 'dashboardMetrics'],
    'api/search' => [App\Controllers\ApiController::class, 'globalSearch'],
    'api/productos' => [App\Controllers\ApiController::class, 'productosSearch'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    echo 'Ruta no encontrada';
    exit;
}

[$controllerClass, $method] = $routes[$route];
$controller = new $controllerClass($config);
$controller->$method();
