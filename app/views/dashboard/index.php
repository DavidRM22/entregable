<?php $fmt = fn(float $n): string => 'S/ ' . number_format($n, 2); ?>
<h1 class="fw-bold mb-1">TechSolutions Admin Dashboard</h1>
<p class="text-secondary mb-4">Gestión de inventario y facturación electrónica en tiempo real</p>
<div class="row g-3 mb-4" data-metrics>
  <div class="col-md-3"><div class="panel"><small>Ventas (Este mes)</small><div class="metric" data-kpi="ventas"><?= $fmt((float)$ventasMes) ?></div></div></div>
  <div class="col-md-3"><div class="panel"><small>Pedidos Pendientes</small><div class="metric" data-kpi="pedidos"><?= (int)$procesando ?></div></div></div>
  <div class="col-md-3"><div class="panel"><small>Stock Total</small><div class="metric" data-kpi="stock"><?= (int)$stockTotal ?></div></div></div>
  <div class="col-md-3"><div class="panel"><small>Alertas Críticas</small><div class="metric" data-kpi="alertas"><?= count($alertas) ?></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-8">
    <div class="panel">
      <h4 class="fw-bold">Pedidos Recientes</h4>
      <table class="table align-middle">
        <thead><tr><th>N° Pedido</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Acción</th></tr></thead>
        <tbody>
        <?php foreach (array_slice($pedidos, 0, 6) as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
            <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
            <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
            <td><?= htmlspecialchars($p['estado']) ?></td>
            <td class="fw-semibold"><?= $fmt((float)$p['total_pagar']) ?></td>
            <td><a class="btn btn-sm btn-outline-secondary" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">Ver Factura</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel">
      <h4 class="fw-bold">Estado de Inventario</h4>
      <?php foreach (array_slice($productos, 0, 6) as $p): $s=(int)$p['stock_actual'];$m=(int)$p['stock_minimo'];$percent=min(100,$m>0?round(($s/$m)*100):100); ?>
      <div class="mb-2"><strong><?= htmlspecialchars($p['nombre']) ?></strong><div class="progress" style="height:8px"><div class="progress-bar <?= $s===0?'bg-danger':($s<$m?'bg-warning':'bg-success') ?>" style="width:<?= $percent ?>%"></div></div><small class="text-muted"><?= $s ?> unidades · mínimo <?= $m ?></small></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
