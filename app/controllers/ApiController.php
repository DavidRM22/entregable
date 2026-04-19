<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Alerta;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;

class ApiController extends Controller
{
    private function json(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function dashboardMetrics(): void
    {
        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $productoModel = new Producto($db, $this->config);
        $alertaModel = new Alerta($db, $this->config);

        $pedidos = $pedidoModel->all();
        $productos = $productoModel->all();
        $alertas = $alertaModel->all();

        $mesActual = date('Y-m');
        $ventasMes = array_sum(array_map(static fn($p) => str_starts_with($p['fecha_pedido'], $mesActual) ? (float)$p['total_pagar'] : 0, $pedidos));
        $procesando = count(array_filter($pedidos, static fn($p) => $p['estado'] === 'Procesando'));
        $stockTotal = array_sum(array_map(static fn($p) => (int)$p['stock_actual'], $productos));

        $this->json([
            'ventasMes' => $ventasMes,
            'procesando' => $procesando,
            'stockTotal' => $stockTotal,
            'alertas' => count($alertas),
            'updatedAt' => date('Y-m-d H:i:s'),
        ]);
    }

    public function globalSearch(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        if (mb_strlen($q) < 2) {
            $this->json(['items' => []]);
        }

        $db = Database::connection($this->config['db']);
        $productoModel = new Producto($db, $this->config);
        $pedidoModel = new Pedido($db, $this->config);
        $clienteModel = new Cliente($db, $this->config);

        $items = [];

        foreach (array_slice($productoModel->search($q), 0, 5) as $producto) {
            $items[] = [
                'tipo' => 'Producto',
                'titulo' => $producto['nombre'],
                'subtitulo' => 'SKU: ' . $producto['sku'],
                'url' => '?r=productos&q=' . urlencode($producto['nombre']),
            ];
        }

        foreach ($pedidoModel->all() as $pedido) {
            if (stripos((string)$pedido['numero_pedido'], $q) !== false || stripos((string)$pedido['cliente_nombre'], $q) !== false) {
                $items[] = [
                    'tipo' => 'Pedido',
                    'titulo' => $pedido['numero_pedido'],
                    'subtitulo' => $pedido['cliente_nombre'] . ' · S/ ' . number_format((float)$pedido['total_pagar'], 2),
                    'url' => '?r=facturas&pedido=' . $pedido['id'],
                ];
            }
            if (count($items) >= 10) {
                break;
            }
        }

        foreach ($clienteModel->all() as $cliente) {
            if (stripos((string)$cliente['nombre_razon_social'], $q) !== false) {
                $items[] = [
                    'tipo' => 'Cliente',
                    'titulo' => $cliente['nombre_razon_social'],
                    'subtitulo' => 'RUC/DNI: ' . $cliente['numero_documento'],
                    'url' => '?r=pedidos',
                ];
            }
            if (count($items) >= 10) {
                break;
            }
        }

        $this->json(['items' => array_slice($items, 0, 10)]);
    }

    public function productosSearch(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));

        $db = Database::connection($this->config['db']);
        $productoModel = new Producto($db, $this->config);
        $productos = $productoModel->search($q);

        foreach ($productos as &$producto) {
            $stock = (int)$producto['stock_actual'];
            $min = (int)$producto['stock_minimo'];
            $producto['estado'] = $stock === 0 ? 'Sin Stock' : ($stock < $min ? 'Stock Bajo' : 'En Stock');
        }

        $this->json(['productos' => $productos]);
    }
}
