<h1 class="fw-bold mb-1">Reportes</h1>
<p class="text-secondary mb-4">Análisis de ventas e inventario</p>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="panel"><small>Ventas totales</small><div class="metric">S/ <?= number_format((float)$ventasTotales, 2) ?></div></div></div>
  <div class="col-md-4"><div class="panel"><small>IGV recaudado</small><div class="metric">S/ <?= number_format((float)$igvTotal, 2) ?></div></div></div>
  <div class="col-md-4"><div class="panel"><small>Pedidos totales</small><div class="metric"><?= count($pedidos) ?></div></div></div>
</div>
<div class="panel">
  <h4>Top productos vendidos</h4>
  <?php foreach ($masVendidos as $p): ?>
    <div class="d-flex justify-content-between border-bottom py-2">
      <div><strong><?= htmlspecialchars($p['nombre']) ?></strong><div class="text-secondary"><?= htmlspecialchars($p['categoria']) ?></div></div>
      <div class="text-end"><strong><?= (int)$p['vendido'] ?> uds</strong><div class="text-secondary">S/ <?= number_format((int)$p['vendido'] * (float)$p['precio_unitario'], 2) ?></div></div>
    </div>
  <?php endforeach; ?>
</div>
