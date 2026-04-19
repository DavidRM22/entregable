<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="p-3 border rounded bg-white"><small>Ventas totales</small><h4>S/ <?= number_format((float)$ventasTotales, 2) ?></h4></div></div>
  <div class="col-md-4"><div class="p-3 border rounded bg-white"><small>IGV recaudado</small><h4>S/ <?= number_format((float)$igvTotal, 2) ?></h4></div></div>
  <div class="col-md-4"><div class="p-3 border rounded bg-white"><small>Pedidos totales</small><h4><?= count($pedidos) ?></h4></div></div>
</div>

<h5>Top productos vendidos</h5>
<table class="table table-striped">
  <thead><tr><th>Producto</th><th>Categoría</th><th class="text-end">Unidades</th><th class="text-end">Valor estimado</th></tr></thead>
  <tbody>
  <?php foreach ($masVendidos as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p['nombre']) ?></td>
      <td><?= htmlspecialchars($p['categoria']) ?></td>
      <td class="text-end"><?= (int)$p['vendido'] ?></td>
      <td class="text-end">S/ <?= number_format((int)$p['vendido'] * (float)$p['precio_unitario'], 2) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
