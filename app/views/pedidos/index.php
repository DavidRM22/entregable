<h5>Crear pedido rápido</h5>
<form method="post" action="?r=pedidos/create" class="border rounded p-3 mb-4 bg-light-subtle">
  <div class="mb-3">
    <label class="form-label">Cliente</label>
    <select class="form-select" name="cliente_id" required>
      <option value="">Seleccione...</option>
      <?php foreach ($clientes as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre_razon_social']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="row g-2 mb-2">
    <div class="col-md-7"><label class="form-label">Producto</label></div>
    <div class="col-md-3"><label class="form-label">Cantidad</label></div>
  </div>

  <?php for ($i = 0; $i < 3; $i++): ?>
    <div class="row g-2 mb-2">
      <div class="col-md-7">
        <select class="form-select" name="producto_id[]">
          <option value="">-- opcional --</option>
          <?php foreach ($productos as $p): ?>
            <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> (stock: <?= (int)$p['stock_actual'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><input type="number" class="form-control" min="1" name="cantidad[]" value="1"></div>
    </div>
  <?php endfor; ?>

  <button class="btn btn-primary">Crear pedido</button>
</form>

<h5>Todos los pedidos</h5>
<table class="table table-striped table-hover">
  <thead><tr><th># Pedido</th><th>Cliente</th><th>Estado</th><th>Fecha</th><th class="text-end">Total</th><th>Cambiar estado</th></tr></thead>
  <tbody>
  <?php foreach ($pedidos as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
      <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
      <td><?= htmlspecialchars($p['estado']) ?></td>
      <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
      <td class="text-end">S/ <?= number_format((float)$p['total_pagar'], 2) ?></td>
      <td>
        <form method="post" action="?r=pedidos/update-status" class="d-flex gap-2">
          <input type="hidden" name="pedido_id" value="<?= (int)$p['id'] ?>">
          <select class="form-select form-select-sm" name="estado">
            <?php foreach (['Procesando', 'Enviado', 'Entregado', 'Cancelado'] as $estado): ?>
              <option value="<?= $estado ?>" <?= $p['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-outline-secondary">Guardar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
