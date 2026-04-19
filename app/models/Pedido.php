<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use RuntimeException;

class Pedido extends Model
{
    public function all(): array
    {
        $sql = 'SELECT p.*, c.nombre_razon_social AS cliente_nombre
                FROM pedidos p
                JOIN clientes c ON c.id = p.cliente_id
                ORDER BY p.id DESC';
        $rows = $this->db->query($sql)->fetchAll();

        foreach ($rows as &$row) {
            $row['detalles'] = $this->details((int)$row['id']);
        }
        return $rows;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, c.nombre_razon_social AS cliente_nombre FROM pedidos p JOIN clientes c ON c.id = p.cliente_id WHERE p.id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $row['detalles'] = $this->details($id);
        return $row;
    }

    public function details(int $pedidoId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM pedido_detalles WHERE pedido_id = :pedido_id ORDER BY id');
        $stmt->execute(['pedido_id' => $pedidoId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $pedidoId, string $estado): void
    {
        $stmt = $this->db->prepare('UPDATE pedidos SET estado = :estado WHERE id = :id');
        $stmt->execute(['estado' => $estado, 'id' => $pedidoId]);
    }

    public function create(int $clienteId, array $items): int
    {
        $igvRate = (float)$this->config['igv_rate'];
        $this->db->beginTransaction();

        try {
            $detailRows = [];
            $totalNeto = 0.0;

            foreach ($items as $item) {
                $productoId = (int)$item['producto_id'];
                $cantidad = (int)$item['cantidad'];

                $productStmt = $this->db->prepare('SELECT id, nombre, precio_unitario, stock_actual FROM productos WHERE id = :id FOR UPDATE');
                $productStmt->execute(['id' => $productoId]);
                $producto = $productStmt->fetch();

                if (!$producto) {
                    throw new RuntimeException('Producto inexistente');
                }
                if ((int)$producto['stock_actual'] < $cantidad) {
                    throw new RuntimeException('Stock insuficiente para ' . $producto['nombre']);
                }

                $subtotal = (float)$producto['precio_unitario'] * $cantidad;
                $totalNeto += $subtotal;
                $detailRows[] = [
                    'producto_id' => $productoId,
                    'producto_nombre' => $producto['nombre'],
                    'cantidad' => $cantidad,
                    'precio_unitario_venta' => $producto['precio_unitario'],
                    'subtotal' => $subtotal,
                ];

                $updateStock = $this->db->prepare('UPDATE productos SET stock_actual = stock_actual - :cantidad, fecha_actualizacion = NOW() WHERE id = :id');
                $updateStock->execute(['cantidad' => $cantidad, 'id' => $productoId]);
            }

            $totalImpuestos = round($totalNeto * $igvRate, 2);
            $totalPagar = round($totalNeto + $totalImpuestos, 2);

            $numeroPedido = '1A' . substr((string)time() . random_int(1000, 9999), -10);
            $insertOrder = $this->db->prepare('INSERT INTO pedidos (numero_pedido, cliente_id, fecha_pedido, estado, total_neto, total_impuestos, total_pagar) VALUES (:numero_pedido, :cliente_id, NOW(), :estado, :total_neto, :total_impuestos, :total_pagar)');
            $insertOrder->execute([
                'numero_pedido' => $numeroPedido,
                'cliente_id' => $clienteId,
                'estado' => 'Procesando',
                'total_neto' => $totalNeto,
                'total_impuestos' => $totalImpuestos,
                'total_pagar' => $totalPagar,
            ]);

            $pedidoId = (int)$this->db->lastInsertId();
            $insertDetail = $this->db->prepare('INSERT INTO pedido_detalles (pedido_id, producto_id, producto_nombre, cantidad, precio_unitario_venta, subtotal) VALUES (:pedido_id, :producto_id, :producto_nombre, :cantidad, :precio_unitario_venta, :subtotal)');

            foreach ($detailRows as $row) {
                $insertDetail->execute(array_merge(['pedido_id' => $pedidoId], $row));
            }

            $this->db->commit();
            return $pedidoId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
