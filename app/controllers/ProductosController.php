<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Producto;

class ProductosController extends Controller
{
    public function index(): void
    {
        $db = Database::connection($this->config['db']);
        $productoModel = new Producto($db, $this->config);
        $q = trim($_GET['q'] ?? '');
        $productos = $productoModel->search($q);

        $this->render('productos/index', compact('productos', 'q'));
    }

    public function restock(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('productos');
        }

        $id = (int)($_POST['id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 50);

        $db = Database::connection($this->config['db']);
        $productoModel = new Producto($db, $this->config);
        $productoModel->restock($id, $cantidad);

        $this->redirect('productos');
    }
}
