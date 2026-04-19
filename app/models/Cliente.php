<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM clientes ORDER BY id')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
