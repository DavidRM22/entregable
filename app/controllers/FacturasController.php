<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Factura;
use App\Models\Pedido;

class FacturasController extends Controller
{
    public function index(): void
    {
        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $facturaModel = new Factura($db, $this->config);

        $pedidos = $pedidoModel->all();
        $facturas = $facturaModel->all();
        $pedidoSeleccionado = (int)($_GET['pedido'] ?? ($pedidos[0]['id'] ?? 0));
        $facturaActual = null;

        if ($pedidoSeleccionado > 0) {
            $facturaActual = $facturaModel->findByPedido($pedidoSeleccionado);
        }

        $this->render('facturas/index', compact('pedidos', 'facturas', 'pedidoSeleccionado', 'facturaActual'));
    }

    public function generate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('facturas');
        }

        $pedidoId = (int)($_POST['pedido_id'] ?? 0);
        $db = Database::connection($this->config['db']);
        $facturaModel = new Factura($db, $this->config);
        $facturaModel->createFromPedido($pedidoId);

        $this->redirect('facturas&pedido=' . $pedidoId);
    }
}
