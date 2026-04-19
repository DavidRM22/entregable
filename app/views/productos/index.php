<h1 class="fw-bold mb-1">Inventario</h1>
<p class="text-secondary mb-4"><?= count($productos) ?> productos en catálogo</p>
<div class="panel">
  <div class="d-flex justify-content-end mb-3">
    <input id="producto-search" class="form-control" style="max-width:360px" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar productos, SKU...">
  </div>
  <table class="table align-middle" id="productos-table">
    <thead><tr><th>Producto</th><th>Categoría</th><th>SKU</th><th>Stock</th><th>Estado</th><th>Precio</th><th>Acción</th></tr></thead>
    <tbody>
    <?php foreach ($productos as $p): $stock=(int)$p['stock_actual'];$min=(int)$p['stock_minimo'];$estado=$stock===0?'Sin Stock':($stock<$min?'Stock Bajo':'En Stock'); ?>
      <tr>
        <td><?= htmlspecialchars($p['nombre']) ?></td><td><?= htmlspecialchars($p['categoria']) ?></td><td><?= htmlspecialchars($p['sku']) ?></td><td class="fw-semibold"><?= $stock ?></td>
        <td><span class="tag <?= $estado==='En Stock'?'tag-ok':($estado==='Stock Bajo'?'tag-warn':'tag-bad') ?>"><?= $estado ?></span></td>
        <td>S/ <?= number_format((float)$p['precio_unitario'], 2) ?></td>
        <td><form method="post" action="?r=productos/restock"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="cantidad" value="50"><button class="btn btn-sm btn-outline-primary">Reabastecer +50</button></form></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script>
const pInput=document.getElementById('producto-search');
const pTable=document.querySelector('#productos-table tbody');
let pTimer;
function estadoClass(e){if(e==='En Stock')return 'tag-ok';if(e==='Stock Bajo')return 'tag-warn';return 'tag-bad';}
pInput?.addEventListener('input',()=>{clearTimeout(pTimer);pTimer=setTimeout(async()=>{const r=await fetch(`?r=api/productos&q=${encodeURIComponent(pInput.value.trim())}`);const data=await r.json();pTable.innerHTML=(data.productos||[]).map(p=>`<tr><td>${p.nombre}</td><td>${p.categoria}</td><td>${p.sku}</td><td class="fw-semibold">${p.stock_actual}</td><td><span class="tag ${estadoClass(p.estado)}">${p.estado}</span></td><td>S/ ${Number(p.precio_unitario).toFixed(2)}</td><td><form method="post" action="?r=productos/restock"><input type="hidden" name="id" value="${p.id}"><input type="hidden" name="cantidad" value="50"><button class="btn btn-sm btn-outline-primary">Reabastecer +50</button></form></td></tr>`).join('')||'<tr><td colspan="7" class="text-center text-muted">Sin resultados</td></tr>';
},220);});
</script>
