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

    public function download(): void
    {
        $pedidoId = (int)($_GET['pedido'] ?? 0);
        if ($pedidoId <= 0) {
            $this->redirect('facturas');
        }

        $db = Database::connection($this->config['db']);
        $pedidoModel = new Pedido($db, $this->config);
        $facturaModel = new Factura($db, $this->config);

        $pedido = $pedidoModel->find($pedidoId);
        $factura = $facturaModel->findByPedido($pedidoId);

        if (!$pedido || !$factura) {
            $this->redirect('facturas&pedido=' . $pedidoId);
        }

        $lines = [
            'FACTURA ELECTRÓNICA ' . $factura['serie_correlativo'],
            'Estado SUNAT: ' . $factura['estado_sunat'],
            'Fecha emisión: ' . $factura['fecha_emision'],
            '',
            'Cliente: ' . $pedido['cliente_nombre'],
            'Pedido: ' . $pedido['numero_pedido'],
            str_repeat('-', 60),
        ];

        foreach ($pedido['detalles'] as $detalle) {
            $lines[] = sprintf(
                '%s | %d x S/ %0.2f = S/ %0.2f',
                $detalle['producto_nombre'],
                (int)$detalle['cantidad'],
                (float)$detalle['precio_unitario_venta'],
                (float)$detalle['subtotal']
            );
        }

        $lines[] = str_repeat('-', 60);
        $lines[] = 'Neto: S/ ' . number_format((float)$pedido['total_neto'], 2);
        $lines[] = 'IGV: S/ ' . number_format((float)$pedido['total_impuestos'], 2);
        $lines[] = 'Total: S/ ' . number_format((float)$pedido['total_pagar'], 2);

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename=Factura-' . $factura['serie_correlativo'] . '.txt');
        echo implode("\n", $lines);
        exit;
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
