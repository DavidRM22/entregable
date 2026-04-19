<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;

class PedidosController extends Controller
{
    public function index(): void
    {
        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $clienteModel = new Cliente($db, $this->config);
        $productoModel = new Producto($db, $this->config);

        $pedidos = $pedidoModel->all();
        $clientes = $clienteModel->all();
        $productos = $productoModel->all();

        $this->render('pedidos/index', compact('pedidos', 'clientes', 'productos'));
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pedidos');
        }

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $productoIds = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $items = [];

        foreach ($productoIds as $i => $pid) {
            $cantidad = (int)($cantidades[$i] ?? 0);
            if ((int)$pid > 0 && $cantidad > 0) {
                $items[] = ['producto_id' => (int)$pid, 'cantidad' => $cantidad];
            }
        }

        if ($clienteId <= 0 || empty($items)) {
            $this->redirect('pedidos');
        }

        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $productoModel = new Producto($db, $this->config);

        try {
            $pedidoId = $pedidoModel->create($clienteId, $items);
            foreach ($items as $item) {
                $productoModel->ensureLowStockAlert($item['producto_id']);
            }
            $this->redirect('facturas&pedido=' . $pedidoId);
        } catch (\Throwable $e) {
            $this->redirect('pedidos');
        }
    }

    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pedidos');
        }

        $id = (int)($_POST['pedido_id'] ?? 0);
        $estado = (string)($_POST['estado'] ?? 'Procesando');

        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $pedidoModel->updateStatus($id, $estado);

        $this->redirect('pedidos');
    }
}
