<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Alerta;
use App\Models\Factura;
use App\Models\Pedido;
use App\Models\Producto;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $productoModel = new Producto($db, $this->config);
        $alertaModel = new Alerta($db, $this->config);
        $facturaModel = new Factura($db, $this->config);

        $pedidos = $pedidoModel->all();
        $productos = $productoModel->all();
        $alertas = $alertaModel->all();
        $facturas = $facturaModel->all();

        $mesActual = date('Y-m');
        $ventasMes = array_sum(array_map(static fn($p) => str_starts_with($p['fecha_pedido'], $mesActual) ? (float)$p['total_pagar'] : 0, $pedidos));
        $procesando = count(array_filter($pedidos, static fn($p) => $p['estado'] === 'Procesando'));
        $stockTotal = array_sum(array_map(static fn($p) => (int)$p['stock_actual'], $productos));

        $this->render('dashboard/index', compact('pedidos', 'productos', 'alertas', 'facturas', 'ventasMes', 'procesando', 'stockTotal'));
    }
}
