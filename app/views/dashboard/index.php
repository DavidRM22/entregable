<?php
$fmt = fn(float $n): string => 'S/ ' . number_format($n, 2);
?>
<p class="text-muted">Gestión de inventario y facturación electrónica en tiempo real.</p>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="p-3 border rounded bg-white"><small>Ventas (este mes)</small><h4><?= $fmt((float)$ventasMes) ?></h4></div></div>
  <div class="col-md-3"><div class="p-3 border rounded bg-white"><small>Pedidos pendientes</small><h4><?= (int)$procesando ?></h4></div></div>
  <div class="col-md-3"><div class="p-3 border rounded bg-white"><small>Stock total</small><h4><?= (int)$stockTotal ?></h4></div></div>
  <div class="col-md-3"><div class="p-3 border rounded bg-white"><small>Alertas críticas</small><h4><?= count($alertas) ?></h4></div></div>
</div>

<h5>Pedidos recientes</h5>
<table class="table table-sm table-striped">
  <thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Fecha</th><th class="text-end">Total</th></tr></thead>
  <tbody>
  <?php foreach (array_slice($pedidos, 0, 6) as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
      <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
      <td><?= htmlspecialchars($p['estado']) ?></td>
      <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
      <td class="text-end"><?= $fmt((float)$p['total_pagar']) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
