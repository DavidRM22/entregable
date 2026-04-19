<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Pedido;
use App\Models\Producto;

class ReportesController extends Controller
{
    public function index(): void
    {
        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $productoModel = new Producto($db, $this->config);

        $pedidos = $pedidoModel->all();
        $productos = $productoModel->all();

        $ventasTotales = array_sum(array_map(static fn($p) => (float)$p['total_pagar'], $pedidos));
        $igvTotal = array_sum(array_map(static fn($p) => (float)$p['total_impuestos'], $pedidos));

        $ventasPorProducto = [];
        foreach ($pedidos as $pedido) {
            foreach ($pedido['detalles'] as $detalle) {
                $pid = (int)$detalle['producto_id'];
                $ventasPorProducto[$pid] = ($ventasPorProducto[$pid] ?? 0) + (int)$detalle['cantidad'];
            }
        }

        $masVendidos = [];
        foreach ($productos as $producto) {
            $pid = (int)$producto['id'];
            $producto['vendido'] = $ventasPorProducto[$pid] ?? 0;
            $masVendidos[] = $producto;
        }

        usort($masVendidos, static fn($a, $b) => $b['vendido'] <=> $a['vendido']);
        $masVendidos = array_slice($masVendidos, 0, 5);

        $this->render('reportes/index', compact('pedidos', 'ventasTotales', 'igvTotal', 'masVendidos'));
    }
}
