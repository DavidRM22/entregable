<h1 class="fw-bold mb-1">Gestión de Pedidos</h1>
<p class="text-secondary mb-4">Crea pedidos y visualiza su factura electrónica</p>
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="panel">
      <h4>Entrada Rápida de Pedidos</h4>
      <form method="post" action="?r=pedidos/create">
        <label class="form-label mt-2">Cliente</label>
        <select class="form-select mb-3" name="cliente_id" required>
          <option value="">Seleccione...</option>
          <?php foreach ($clientes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre_razon_social']) ?> — <?= htmlspecialchars($c['numero_documento']) ?></option><?php endforeach; ?>
        </select>
        <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="row g-2 mb-2">
          <div class="col-8"><select class="form-select" name="producto_id[]"><option value="">-- opcional --</option><?php foreach ($productos as $p): ?><option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> • Stock <?= (int)$p['stock_actual'] ?></option><?php endforeach; ?></select></div>
          <div class="col-4"><input class="form-control" type="number" min="1" name="cantidad[]" value="1"></div>
        </div>
        <?php endfor; ?>
        <button class="btn btn-success w-100 mt-2">Crear Pedido & Descontar Inventario</button>
      </form>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="panel h-100">
      <h4>Acceso rápido a facturas</h4>
      <p class="text-secondary">Después de crear o seleccionar un pedido, puedes ver y descargar su factura desde el módulo de facturación.</p>
      <a class="btn btn-outline-primary" href="?r=facturas">Ir a facturación</a>
    </div>
  </div>
</div>
<div class="panel">
  <h4>Todos los Pedidos</h4>
  <table class="table align-middle">
    <thead><tr><th>N° Pedido</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Acción</th></tr></thead>
    <tbody>
    <?php foreach ($pedidos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
        <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
        <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
        <td>
          <form method="post" action="?r=pedidos/update-status" class="d-flex gap-2">
            <input type="hidden" name="pedido_id" value="<?= (int)$p['id'] ?>">
            <select class="form-select form-select-sm" name="estado"><?php foreach (['Procesando', 'Enviado', 'Entregado', 'Cancelado'] as $estado): ?><option value="<?= $estado ?>" <?= $p['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option><?php endforeach; ?></select>
            <button class="btn btn-sm btn-outline-secondary">Guardar</button>
          </form>
        </td>
        <td class="fw-semibold">S/ <?= number_format((float)$p['total_pagar'], 2) ?></td>
        <td>
          <a class="btn btn-sm btn-outline-secondary" href="?r=facturas&pedido=<?= (int)$p['id'] ?>">Ver Factura</a>
          <a class="btn btn-sm btn-success" href="?r=facturas/download&pedido=<?= (int)$p['id'] ?>">Descargar</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
