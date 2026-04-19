<?php $fmtMoney = static fn($n): string => 'S/ ' . number_format((float)$n, 2); ?>
<h1 class="page-title">Reportes</h1>
<p class="page-sub">Análisis de ventas e inventario</p>

<div class="row-g grid-3" style="margin-bottom:22px">
  <div class="panel">
    <div class="kpi-label">Ventas totales</div>
    <div class="kpi-value" style="font-size:1.85rem;margin-top:8px"><?= $fmtMoney($ventasTotales) ?></div>
  </div>
  <div class="panel">
    <div class="kpi-label">IGV recaudado</div>
    <div class="kpi-value" style="font-size:1.85rem;margin-top:8px"><?= $fmtMoney($igvTotal) ?></div>
  </div>
  <div class="panel">
    <div class="kpi-label">Pedidos totales</div>
    <div class="kpi-value" style="font-size:1.85rem;margin-top:8px"><?= count($pedidos) ?></div>
  </div>
</div>

<div class="panel">
  <h3 class="panel-title" style="margin-bottom:8px">Top productos vendidos</h3>
  <div>
    <?php foreach ($masVendidos as $p): ?>
      <div class="report-row">
        <div>
          <div class="name"><?= htmlspecialchars($p['nombre']) ?></div>
          <div class="cat"><?= htmlspecialchars($p['categoria']) ?></div>
        </div>
        <div>
          <div class="count"><?= (int)$p['vendido'] ?> uds</div>
          <div class="amount"><?= $fmtMoney((int)$p['vendido'] * (float)$p['precio_unitario']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
