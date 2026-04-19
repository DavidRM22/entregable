<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Producto extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM productos ORDER BY id')->fetchAll();
    }

    public function search(string $q): array
    {
        if ($q === '') {
            return $this->all();
        }

        $stmt = $this->db->prepare('SELECT * FROM productos WHERE nombre LIKE :q OR sku LIKE :q OR categoria LIKE :q ORDER BY id');
        $stmt->execute(['q' => "%{$q}%"]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM productos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function restock(int $id, int $cantidad): void
    {
        $stmt = $this->db->prepare('UPDATE productos SET stock_actual = stock_actual + :cantidad, fecha_actualizacion = NOW() WHERE id = :id');
        $stmt->execute(['cantidad' => $cantidad, 'id' => $id]);

        $cleanup = $this->db->prepare('DELETE a FROM alertas a JOIN productos p ON p.id = a.producto_id WHERE p.id = :id AND p.stock_actual >= p.stock_minimo');
        $cleanup->execute(['id' => $id]);
    }

    public function ensureLowStockAlert(int $productoId): void
    {
        $stmt = $this->db->prepare('SELECT id, nombre, stock_actual, stock_minimo FROM productos WHERE id = :id');
        $stmt->execute(['id' => $productoId]);
        $producto = $stmt->fetch();

        if (!$producto || (int)$producto['stock_actual'] >= (int)$producto['stock_minimo']) {
            return;
        }

        $exists = $this->db->prepare('SELECT COUNT(*) FROM alertas WHERE producto_id = :id');
        $exists->execute(['id' => $productoId]);
        if ((int)$exists->fetchColumn() > 0) {
            return;
        }

        $insert = $this->db->prepare('INSERT INTO alertas (producto_id, producto_nombre, stock_actual, fecha) VALUES (:producto_id, :producto_nombre, :stock_actual, NOW())');
        $insert->execute([
            'producto_id' => $productoId,
            'producto_nombre' => $producto['nombre'],
            'stock_actual' => $producto['stock_actual'],
        ]);
    }
}
