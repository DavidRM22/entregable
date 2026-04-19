<?php
$fmtMoney = static fn($n): string => 'S/ ' . number_format((float)$n, 2);
$fmtDate = static function (string $d): string {
    $ts = strtotime($d);
    if ($ts === false) return $d;
    $meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sept.', 'oct.', 'nov.', 'dic.'];
    return (int)date('j', $ts) . ' ' . $meses[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
};
$estadoTag = static function (string $estado): string {
    $map = [
        'Entregado' => 'tag-ok',
        'Enviado' => 'tag-info',
        'Procesando' => 'tag-warn',
        'Cancelado' => 'tag-bad',
    ];
    $c = $map[$estado] ?? 'tag-ok';
    return '<span class="tag ' . $c . '">' . htmlspecialchars($estado) . '</span>';
};
$procesandoCount = (int)$procesando;
?>
<h1 class="page-title">TechSolutions Admin Dashboard</h1>
<p class="page-sub">Gestión de inventario y facturación electrónica en tiempo real</p>

<div class="row-g grid-4" style="margin-bottom:22px" data-metrics>
  <div class="kpi">
    <div class="kpi-icon bg-green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div>
      <div class="kpi-label">Ventas (Este mes)</div>
      <div class="kpi-value" data-kpi="ventas"><?= $fmtMoney($ventasMes) ?><span class="kpi-delta">+12%</span></div>
    </div>
  </div>
  <div class="kpi">
    <div class="kpi-icon bg-blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div>
      <div class="kpi-label">Pedidos Pendientes</div>
      <div class="kpi-value" data-kpi="pedidos"><?= $procesandoCount ?></div>
    </div>
  </div>
  <div class="kpi">
    <div class="kpi-icon bg-teal">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
    </div>
    <div>
      <div class="kpi-label">Stock Total</div>
      <div class="kpi-value" data-kpi="stock"><?= (int)$stockTotal ?></div>
    </div>
  </div>
  <div class="kpi">
    <div class="kpi-icon bg-yellow">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <div>
      <div class="kpi-label">Alertas Críticas</div>
      <div class="kpi-value" data-kpi="alertas"><?= count($alertas) ?></div>
    </div>
  </div>
</div>

<div class="row-g grid-2-3">
  <div class="panel">
    <div style="margin-bottom:16px">
      <h3 class="panel-title">Pedidos Recientes</h3>
      <p class="panel-sub"><?= $procesandoCount ?> pedido(s) en procesamiento</p>
    </div>
    <table class="ts-table">
      <thead>
        <tr>
          <th style="width:30px"></th>
          <th>N° Pedido</th>
          <th>Producto</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th class="num">Total</th>
          <th class="num">Acción</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach (array_slice($pedidos, 0, 6) as $p):
        $primerDetalle = $p['detalles'][0] ?? null;
        $productoNombre = $primerDetalle ? $primerDetalle['producto_nombre'] : '—';
      ?>
        <tr>
          <td><input type="radio" name="dashboard_sel" style="accent-color:var(--brand)"></td>
          <td><span style="font-family:monospace;font-size:.82rem;color:#475569"><?= htmlspecialchars($p['numero_pedido']) ?></span></td>
          <td><?= htmlspecialchars($productoNombre) ?></td>
          <td style="color:#475569"><?= htmlspecialchars($fmtDate($p['fecha_pedido'])) ?></td>
          <td><?= $estadoTag($p['estado']) ?></td>
          <td class="num" style="font-weight:600"><?= $fmtMoney($p['total_pagar']) ?></td>
          <td class="num"><a class="btn-ts btn-outline-ts" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">Ver Factura</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="panel">
    <div style="margin-bottom:16px">
      <h3 class="panel-title">Estado de Inventario en Tiempo Real</h3>
      <p class="panel-sub">Niveles de stock por producto</p>
    </div>
    <?php foreach (array_slice($productos, 0, 6) as $p):
      $s = (int)$p['stock_actual'];
      $m = (int)$p['stock_minimo'];
      if ($s === 0) { $estado = 'Sin stock'; $cls = 'bad'; $pct = 5; }
      elseif ($s < $m) { $estado = 'Stock Bajo'; $cls = 'warn'; $pct = max(15, min(60, $m > 0 ? round(($s / ($m * 2)) * 100) : 30)); }
      else { $estado = 'Stock Saludable'; $cls = 'ok'; $pct = min(100, $m > 0 ? round(($s / ($m * 2)) * 100) : 100); }
    ?>
      <div class="stock-row">
        <div class="stock-head">
          <div class="stock-name"><?= htmlspecialchars($p['nombre']) ?></div>
          <div class="stock-status <?= $cls ?>"><?= $estado ?></div>
        </div>
        <div class="stock-bar"><div class="<?= $cls ?>" style="width:<?= $pct ?>%"></div></div>
        <div class="stock-meta"><?= $s ?> unidades · mínimo <?= $m ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
