<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Factura extends Model
{
    public function all(): array
    {
        $sql = 'SELECT f.*, p.numero_pedido, c.nombre_razon_social AS cliente_nombre, p.total_neto, p.total_impuestos, p.total_pagar
                FROM facturas f
                JOIN pedidos p ON p.id = f.pedido_id
                JOIN clientes c ON c.id = p.cliente_id
                ORDER BY f.id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function findByPedido(int $pedidoId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM facturas WHERE pedido_id = :pedido_id LIMIT 1');
        $stmt->execute(['pedido_id' => $pedidoId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createFromPedido(int $pedidoId): array
    {
        $existing = $this->findByPedido($pedidoId);
        if ($existing) {
            return $existing;
        }

        $count = (int)$this->db->query('SELECT COUNT(*) FROM facturas')->fetchColumn() + 1;
        $serie = 'F001-' . str_pad((string)$count, 6, '0', STR_PAD_LEFT);

        $stmt = $this->db->prepare('INSERT INTO facturas (pedido_id, serie_correlativo, estado_sunat, fecha_emision) VALUES (:pedido_id, :serie_correlativo, :estado_sunat, NOW())');
        $stmt->execute([
            'pedido_id' => $pedidoId,
            'serie_correlativo' => $serie,
            'estado_sunat' => 'Aceptado',
        ]);

        return [
            'id' => (int)$this->db->lastInsertId(),
            'pedido_id' => $pedidoId,
            'serie_correlativo' => $serie,
            'estado_sunat' => 'Aceptado',
            'fecha_emision' => date('Y-m-d H:i:s'),
        ];
    }
}
