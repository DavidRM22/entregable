<h1 class="fw-bold mb-1">Facturación Electrónica</h1>
<p class="text-secondary mb-4">Genera y descarga facturas electrónicas con IGV calculado</p>
<div class="row g-3">
  <div class="col-lg-4">
    <div class="panel">
      <?php foreach ($pedidos as $p): ?>
        <a class="d-block text-decoration-none p-2 rounded mb-2 <?= (int)$p['id']===(int)$pedidoSeleccionado?'bg-success-subtle':'' ?>" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">
          <div class="fw-semibold text-dark"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
          <small class="text-secondary"><?= htmlspecialchars($p['numero_pedido']) ?></small>
          <div class="fw-semibold text-dark">S/ <?= number_format((float)$p['total_pagar'], 2) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Factura Electrónica</h4>
        <?php if ($facturaActual): ?><a class="btn btn-success" href="?r=facturas/download&pedido=<?= (int)$pedidoSeleccionado ?>">Descargar Factura</a><?php endif; ?>
      </div>
      <?php
      $pedido = null;
      foreach ($pedidos as $p) {
          if ((int)$p['id'] === (int)$pedidoSeleccionado) { $pedido = $p; break; }
      }
      ?>
      <?php if ($pedido): ?>
        <?php if (!$facturaActual): ?>
          <form method="post" action="?r=facturas/generate" class="mb-3"><input type="hidden" name="pedido_id" value="<?= (int)$pedido['id'] ?>"><button class="btn btn-primary">Generar factura</button></form>
        <?php endif; ?>
        <div class="border rounded p-3">
          <div class="row"><div class="col"><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nombre']) ?></div><div class="col text-end"><strong>Factura:</strong> <?= $facturaActual ? htmlspecialchars($facturaActual['serie_correlativo']) : 'Pendiente' ?></div></div>
          <div><strong>Estado SUNAT:</strong> <?= $facturaActual ? htmlspecialchars($facturaActual['estado_sunat']) : 'Pendiente' ?></div>
          <table class="table table-sm mt-2"><thead><tr><th>Descripción</th><th>Cant.</th><th>P. Unit</th><th>Subtotal</th></tr></thead><tbody><?php foreach ($pedido['detalles'] as $d): ?><tr><td><?= htmlspecialchars($d['producto_nombre']) ?></td><td><?= (int)$d['cantidad'] ?></td><td>S/ <?= number_format((float)$d['precio_unitario_venta'], 2) ?></td><td>S/ <?= number_format((float)$d['subtotal'], 2) ?></td></tr><?php endforeach; ?></tbody></table>
          <div class="text-end">Subtotal: S/ <?= number_format((float)$pedido['total_neto'], 2) ?></div>
          <div class="text-end">IGV: S/ <?= number_format((float)$pedido['total_impuestos'], 2) ?></div>
          <div class="text-end fw-bold">Total: S/ <?= number_format((float)$pedido['total_pagar'], 2) ?></div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
