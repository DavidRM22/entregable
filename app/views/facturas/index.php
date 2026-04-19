<div class="row g-3">
  <div class="col-md-4">
    <h5>Pedidos</h5>
    <div class="list-group">
      <?php foreach ($pedidos as $p): ?>
        <a class="list-group-item list-group-item-action <?= (int)$p['id'] === (int)$pedidoSeleccionado ? 'active' : '' ?>" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">
          <div class="fw-semibold"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
          <small><?= htmlspecialchars($p['numero_pedido']) ?> | S/ <?= number_format((float)$p['total_pagar'], 2) ?></small>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="col-md-8">
    <h5>Vista de factura</h5>
    <?php
      $pedido = null;
      foreach ($pedidos as $p) {
          if ((int)$p['id'] === (int)$pedidoSeleccionado) {
              $pedido = $p;
              break;
          }
      }
    ?>
    <?php if ($pedido): ?>
      <?php if (!$facturaActual): ?>
        <form method="post" action="?r=facturas/generate" class="mb-3">
          <input type="hidden" name="pedido_id" value="<?= (int)$pedido['id'] ?>">
          <button class="btn btn-primary">Generar factura</button>
        </form>
      <?php endif; ?>

      <div class="border rounded p-3 bg-white">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nombre']) ?></p>
        <p><strong>Pedido:</strong> <?= htmlspecialchars($pedido['numero_pedido']) ?></p>
        <p><strong>Factura:</strong> <?= $facturaActual ? htmlspecialchars($facturaActual['serie_correlativo']) : 'No generada' ?></p>
        <p><strong>Estado SUNAT:</strong> <?= $facturaActual ? htmlspecialchars($facturaActual['estado_sunat']) : 'Pendiente' ?></p>
        <table class="table table-sm">
          <thead><tr><th>Producto</th><th class="text-end">Cant</th><th class="text-end">P.Unit</th><th class="text-end">Subtotal</th></tr></thead>
          <tbody>
          <?php foreach ($pedido['detalles'] as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['producto_nombre']) ?></td>
              <td class="text-end"><?= (int)$d['cantidad'] ?></td>
              <td class="text-end">S/ <?= number_format((float)$d['precio_unitario_venta'], 2) ?></td>
              <td class="text-end">S/ <?= number_format((float)$d['subtotal'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <p class="text-end mb-1">Neto: S/ <?= number_format((float)$pedido['total_neto'], 2) ?></p>
        <p class="text-end mb-1">IGV: S/ <?= number_format((float)$pedido['total_impuestos'], 2) ?></p>
        <p class="text-end fw-bold">Total: S/ <?= number_format((float)$pedido['total_pagar'], 2) ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>
