<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Alerta extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM alertas ORDER BY fecha DESC')->fetchAll();
    }

    public function remove(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM alertas WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
