<form class="row g-2 mb-3" method="get">
  <input type="hidden" name="r" value="productos">
  <div class="col-md-4">
    <input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar productos, SKU o categoría">
  </div>
  <div class="col-auto"><button class="btn btn-primary">Buscar</button></div>
</form>

<table class="table table-striped table-hover align-middle">
  <thead><tr><th>Producto</th><th>Categoría</th><th>SKU</th><th class="text-end">Stock</th><th>Estado</th><th class="text-end">Precio</th><th>Acción</th></tr></thead>
  <tbody>
  <?php foreach ($productos as $p): ?>
    <?php
      $stock = (int)$p['stock_actual'];
      $min = (int)$p['stock_minimo'];
      $estado = $stock === 0 ? 'Sin Stock' : ($stock < $min ? 'Stock Bajo' : 'En Stock');
    ?>
    <tr>
      <td><?= htmlspecialchars($p['nombre']) ?></td>
      <td><?= htmlspecialchars($p['categoria']) ?></td>
      <td><code><?= htmlspecialchars($p['sku']) ?></code></td>
      <td class="text-end"><?= $stock ?></td>
      <td><?= $estado ?></td>
      <td class="text-end">S/ <?= number_format((float)$p['precio_unitario'], 2) ?></td>
      <td>
        <form method="post" action="?r=productos/restock">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <input type="hidden" name="cantidad" value="50">
          <button class="btn btn-sm btn-outline-primary">Reabastecer +50</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
