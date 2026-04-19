<?php
$fmtMoney = static fn($n): string => 'S/ ' . number_format((float)$n, 2);
$fmtDate = static function (string $d): string {
    $ts = strtotime($d);
    if ($ts === false) return $d;
    $meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sept.', 'oct.', 'nov.', 'dic.'];
    return (int)date('j', $ts) . ' ' . $meses[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
};
$igvRate = (float)($config['igv_rate'] ?? 0.18);

$pedido = null;
foreach ($pedidos as $p) {
    if ((int)$p['id'] === (int)$pedidoSeleccionado) { $pedido = $p; break; }
}
?>
<h1 class="page-title">Facturación Electrónica</h1>
<p class="page-sub">Genera y descarga facturas electrónicas con IGV calculado</p>

<div class="row-g grid-1-2">
  <div class="panel" style="padding:14px">
    <?php foreach ($pedidos as $p):
      $active = (int)$p['id'] === (int)$pedidoSeleccionado;
    ?>
      <a class="fact-item <?= $active ? 'active' : '' ?>" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">
        <div class="name"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
        <div class="num"><?= htmlspecialchars($p['numero_pedido']) ?></div>
        <div class="row2">
          <div class="date"><?= htmlspecialchars($fmtDate($p['fecha_pedido'])) ?></div>
          <div class="amount"><?= $fmtMoney($p['total_pagar']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="panel">
    <div class="flex-between" style="margin-bottom:20px;gap:12px;flex-wrap:wrap">
      <div>
        <h3 class="panel-title">Factura Electrónica</h3>
        <p class="panel-sub">SUNAT — <?= $facturaActual ? htmlspecialchars($facturaActual['estado_sunat']) : 'Pendiente' ?></p>
      </div>
      <?php if ($facturaActual): ?>
        <a class="btn-ts btn-primary-ts" href="?r=facturas/download&pedido=<?= (int)$pedidoSeleccionado ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Descargar Factura
        </a>
      <?php elseif ($pedido): ?>
        <form method="post" action="?r=facturas/generate">
          <input type="hidden" name="pedido_id" value="<?= (int)$pedido['id'] ?>">
          <button class="btn-ts btn-blue">Generar factura</button>
        </form>
      <?php endif; ?>
    </div>

    <?php if ($pedido): ?>
      <?php
        $docCliente = '20512345678';
        $serie = $facturaActual ? $facturaActual['serie_correlativo'] : 'F001-000001';
        $tsEmision = $facturaActual && !empty($facturaActual['fecha_emision']) ? strtotime($facturaActual['fecha_emision']) : strtotime($pedido['fecha_pedido']);
        if (!$tsEmision) $tsEmision = time();
      ?>
      <div class="invoice">
        <div class="invoice-head">
          <div>
            <div class="invoice-brand">TechSolutions</div>
            <div style="margin-top:10px;color:var(--muted);font-size:.82rem">RUC 20512345678<br>Av. Javier Prado 1234, Lima — Perú</div>
          </div>
          <div>
            <div class="invoice-title">FACTURA ELECTRÓNICA</div>
            <div class="invoice-number"><?= htmlspecialchars($serie) ?></div>
            <div class="invoice-number" style="margin-top:8px"><?= htmlspecialchars(date('j M. Y, g:i a', $tsEmision)) ?></div>
          </div>
        </div>

        <div class="invoice-meta-grid">
          <div>
            <small>Cliente</small>
            <strong><?= htmlspecialchars($pedido['cliente_nombre']) ?></strong>
            <div style="color:var(--muted);font-size:.82rem;margin-top:2px">Doc: <?= htmlspecialchars($docCliente) ?></div>
          </div>
          <div>
            <small>Pedido</small>
            <strong><?= htmlspecialchars($pedido['numero_pedido']) ?></strong>
            <div style="color:var(--muted);font-size:.82rem;margin-top:2px">Estado: <?= htmlspecialchars($pedido['estado']) ?></div>
          </div>
        </div>

        <table class="invoice-table">
          <thead><tr><th>Descripción</th><th class="num">Cant.</th><th class="num">P. Unit.</th><th class="num">Subtotal</th></tr></thead>
          <tbody>
          <?php foreach ($pedido['detalles'] as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['producto_nombre']) ?></td>
              <td class="num"><?= (int)$d['cantidad'] ?></td>
              <td class="num"><?= $fmtMoney($d['precio_unitario_venta']) ?></td>
              <td class="num"><?= $fmtMoney($d['subtotal']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <div class="invoice-totals">
          <div class="row-t"><span>Subtotal</span><span><?= $fmtMoney($pedido['total_neto']) ?></span></div>
          <div class="row-t"><span>IGV (<?= (int)round($igvRate * 100) ?>%)</span><span><?= $fmtMoney($pedido['total_impuestos']) ?></span></div>
          <div class="row-t grand"><span>Total</span><span><?= $fmtMoney($pedido['total_pagar']) ?></span></div>
        </div>
      </div>
    <?php else: ?>
      <p style="color:var(--muted)">Selecciona un pedido para ver su factura.</p>
    <?php endif; ?>
  </div>
</div>
