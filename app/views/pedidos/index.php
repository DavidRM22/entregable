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
$procesandoCount = count(array_filter($pedidos, static fn($p) => $p['estado'] === 'Procesando'));
$pedidoPreview = $pedidos[0] ?? null;
$igvRate = (float)($config['igv_rate'] ?? 0.18);
?>
<h1 class="page-title">Gestión de Pedidos</h1>
<p class="page-sub">Crea pedidos y visualiza su factura electrónica</p>

<div class="row-g grid-1-1" style="margin-bottom:24px">
  <!-- Entrada Rápida -->
  <div class="panel">
    <div class="flex-between" style="margin-bottom:18px">
      <div>
        <h3 class="panel-title">Entrada Rápida de Pedidos</h3>
        <p class="panel-sub">Descuenta inventario en tiempo real y genera la factura electrónica</p>
      </div>
      <button type="button" class="btn-ts btn-ghost" title="Escanear">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
      </button>
    </div>

    <form method="post" action="?r=pedidos/create" id="quick-order-form">
      <label class="form-label-ts">Cliente</label>
      <select class="form-input-ts" name="cliente_id" required style="margin-bottom:16px">
        <option value="">Seleccione...</option>
        <?php foreach ($clientes as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre_razon_social']) ?> — <?= htmlspecialchars($c['numero_documento']) ?></option>
        <?php endforeach; ?>
      </select>

      <label class="form-label-ts">Productos</label>
      <div id="prod-rows">
        <?php for ($i = 0; $i < 1; $i++): ?>
          <div class="prod-row" style="display:grid;grid-template-columns:1fr 90px auto;gap:10px;margin-bottom:10px;align-items:center">
            <select class="form-input-ts prod-select" name="producto_id[]" data-idx="<?= $i ?>">
              <option value="">-- opcional --</option>
              <?php foreach ($productos as $p): ?>
                <option value="<?= (int)$p['id'] ?>" data-price="<?= (float)$p['precio_unitario'] ?>"><?= htmlspecialchars($p['nombre']) ?> • Stock <?= (int)$p['stock_actual'] ?></option>
              <?php endforeach; ?>
            </select>
            <input class="form-input-ts prod-qty" type="number" min="1" name="cantidad[]" value="1">
            <div class="prod-line-total" style="min-width:90px;text-align:right;font-weight:600;color:#0f172a">S/ 0.00</div>
          </div>
        <?php endfor; ?>
        <!-- placeholders extra que se enviarán pero quedan ocultos por defecto -->
        <template id="prod-row-template">
          <div class="prod-row" style="display:grid;grid-template-columns:1fr 90px auto;gap:10px;margin-bottom:10px;align-items:center">
            <select class="form-input-ts prod-select" name="producto_id[]">
              <option value="">-- opcional --</option>
              <?php foreach ($productos as $p): ?>
                <option value="<?= (int)$p['id'] ?>" data-price="<?= (float)$p['precio_unitario'] ?>"><?= htmlspecialchars($p['nombre']) ?> • Stock <?= (int)$p['stock_actual'] ?></option>
              <?php endforeach; ?>
            </select>
            <input class="form-input-ts prod-qty" type="number" min="1" name="cantidad[]" value="1">
            <div class="prod-line-total" style="min-width:90px;text-align:right;font-weight:600;color:#0f172a">S/ 0.00</div>
          </div>
        </template>
      </div>

      <button type="button" id="add-prod" class="btn-ts" style="background:transparent;color:var(--brand-dark);padding:6px 0;margin-top:4px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Agregar producto
      </button>

      <div style="border-top:1px solid var(--border);margin:18px 0 14px;padding-top:14px">
        <div class="flex-between" style="padding:3px 0;color:#475569;font-size:.9rem"><span>Subtotal</span><span id="q-sub">S/ 0.00</span></div>
        <div class="flex-between" style="padding:3px 0;color:#475569;font-size:.9rem"><span>IGV (<?= (int)round($igvRate * 100) ?>%)</span><span id="q-igv">S/ 0.00</span></div>
        <div class="flex-between" style="padding:8px 0 2px;font-weight:700;font-size:1.05rem"><span>Total</span><span id="q-total">S/ 0.00</span></div>
      </div>

      <button class="btn-ts btn-primary-ts" style="width:100%;justify-content:center;padding:12px">Crear Pedido &amp; Descontar Inventario</button>
    </form>
  </div>

  <!-- Factura preview -->
  <div class="panel">
    <div class="flex-between" style="margin-bottom:16px;gap:12px;flex-wrap:wrap">
      <div>
        <h3 class="panel-title">Factura Electrónica</h3>
        <p class="panel-sub">SUNAT — Aceptado</p>
      </div>
      <?php if ($pedidoPreview): ?>
        <a class="btn-ts btn-primary-ts" href="?r=facturas/download&pedido=<?= (int)$pedidoPreview['id'] ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Descargar Factura
        </a>
      <?php endif; ?>
    </div>

    <?php if ($pedidoPreview): ?>
      <div class="invoice">
        <div class="invoice-head">
          <div>
            <div class="invoice-brand">TechSolutions</div>
            <div style="margin-top:10px;color:var(--muted);font-size:.82rem">RUC 20512345678<br>Av. Javier Prado 1234, Lima — Perú</div>
          </div>
          <div>
            <div class="invoice-title">FACTURA ELECTRÓNICA</div>
            <div class="invoice-number">F001-000001</div>
            <div class="invoice-number" style="margin-top:8px"><?= htmlspecialchars(date('j M. Y, g:i a', strtotime($pedidoPreview['fecha_pedido']) ?: time())) ?></div>
          </div>
        </div>
        <div class="invoice-meta-grid">
          <div>
            <small>Cliente</small>
            <strong><?= htmlspecialchars($pedidoPreview['cliente_nombre']) ?></strong>
            <div style="color:var(--muted);font-size:.82rem;margin-top:2px">Doc: 20512345678</div>
          </div>
          <div>
            <small>Pedido</small>
            <strong><?= htmlspecialchars($pedidoPreview['numero_pedido']) ?></strong>
            <div style="color:var(--muted);font-size:.82rem;margin-top:2px">Estado: <?= htmlspecialchars($pedidoPreview['estado']) ?></div>
          </div>
        </div>
        <table class="invoice-table">
          <thead><tr><th>Descripción</th><th class="num">Cant.</th><th class="num">P. Unit.</th><th class="num">Subtotal</th></tr></thead>
          <tbody>
          <?php foreach ($pedidoPreview['detalles'] as $d): ?>
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
          <div class="row-t"><span>Subtotal</span><span><?= $fmtMoney($pedidoPreview['total_neto']) ?></span></div>
          <div class="row-t"><span>IGV (<?= (int)round($igvRate * 100) ?>%)</span><span><?= $fmtMoney($pedidoPreview['total_impuestos']) ?></span></div>
          <div class="row-t grand"><span>Total</span><span><?= $fmtMoney($pedidoPreview['total_pagar']) ?></span></div>
        </div>
      </div>
    <?php else: ?>
      <p style="color:var(--muted)">Crea un pedido para visualizar su factura.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Listado -->
<div class="panel">
  <div style="margin-bottom:16px">
    <h3 class="panel-title">Todos los Pedidos</h3>
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
    <?php foreach ($pedidos as $p):
      $primerDetalle = $p['detalles'][0] ?? null;
      $productoNombre = $primerDetalle ? $primerDetalle['producto_nombre'] : '—';
    ?>
      <tr>
        <td><input type="radio" name="pedidos_sel" style="accent-color:var(--brand)"></td>
        <td><span style="font-family:monospace;font-size:.82rem;color:#475569"><?= htmlspecialchars($p['numero_pedido']) ?></span></td>
        <td><?= htmlspecialchars($productoNombre) ?></td>
        <td style="color:#475569"><?= htmlspecialchars($fmtDate($p['fecha_pedido'])) ?></td>
        <td>
          <form method="post" action="?r=pedidos/update-status" style="display:inline-flex;gap:6px;align-items:center">
            <input type="hidden" name="pedido_id" value="<?= (int)$p['id'] ?>">
            <?= $estadoTag($p['estado']) ?>
            <select class="form-input-ts" name="estado" style="padding:4px 8px;font-size:.78rem;width:auto" onchange="this.form.submit()">
              <?php foreach (['Procesando', 'Enviado', 'Entregado', 'Cancelado'] as $estado): ?>
                <option value="<?= $estado ?>" <?= $p['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
        <td class="num" style="font-weight:600">S/ <?= number_format((float)$p['total_pagar'], 2) ?></td>
        <td class="num"><a class="btn-ts btn-outline-ts" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">Ver Factura</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
(function(){
  const rows=document.getElementById('prod-rows');
  const addBtn=document.getElementById('add-prod');
  const tpl=document.getElementById('prod-row-template');
  const IGV=<?= $igvRate ?>;
  function fmt(n){return 'S/ '+n.toFixed(2)}
  function recalc(){
    let sub=0;
    rows.querySelectorAll('.prod-row').forEach(r=>{
      const sel=r.querySelector('.prod-select');
      const qty=parseInt(r.querySelector('.prod-qty').value||'0',10);
      const price=parseFloat(sel.options[sel.selectedIndex]?.dataset.price||'0');
      const line=qty*price;
      r.querySelector('.prod-line-total').textContent=fmt(line);
      sub+=line;
    });
    const igv=Math.round(sub*IGV*100)/100;
    document.getElementById('q-sub').textContent=fmt(sub);
    document.getElementById('q-igv').textContent=fmt(igv);
    document.getElementById('q-total').textContent=fmt(sub+igv);
  }
  rows.addEventListener('input',recalc);
  rows.addEventListener('change',recalc);
  addBtn.addEventListener('click',()=>{
    const clone=tpl.content.cloneNode(true);
    rows.appendChild(clone);
    recalc();
  });
  recalc();
})();
</script>
