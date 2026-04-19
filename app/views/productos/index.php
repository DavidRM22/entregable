<?php
$estadoFor = static function (int $stock, int $min): array {
    if ($stock === 0) return ['Sin Stock', 'tag-bad'];
    if ($stock < $min) return ['Stock Bajo', 'tag-warn'];
    return ['En Stock', 'tag-ok'];
};
?>
<div class="flex-between" style="margin-bottom:22px;gap:16px;flex-wrap:wrap">
  <div>
    <h1 class="page-title">Inventario</h1>
    <p class="page-sub" style="margin:0"><?= count($productos) ?> productos en catálogo</p>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <div class="search-wrap" style="max-width:280px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
      <input id="producto-search" class="global-search" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar productos, SKU...">
    </div>
    <button class="btn-ts btn-outline-ts">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      Filtrar
    </button>
    <button class="btn-ts btn-blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nuevo Producto
    </button>
  </div>
</div>

<div class="panel" style="padding:8px 4px">
  <table class="ts-table" id="productos-table">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Categoría</th>
        <th>SKU</th>
        <th class="num">Stock</th>
        <th>Estado</th>
        <th class="num">Precio</th>
        <th class="num">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($productos as $p):
      $stock = (int)$p['stock_actual'];
      $min = (int)$p['stock_minimo'];
      [$estadoTxt, $estadoCls] = $estadoFor($stock, $min);
    ?>
      <tr>
        <td>
          <span class="prod-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          </span>
          <strong><?= htmlspecialchars($p['nombre']) ?></strong>
        </td>
        <td style="color:#475569"><?= htmlspecialchars($p['categoria']) ?></td>
        <td><span style="font-family:monospace;font-size:.82rem;color:#475569"><?= htmlspecialchars($p['sku']) ?></span></td>
        <td class="num" style="font-weight:700"><?= $stock ?></td>
        <td><span class="tag <?= $estadoCls ?>"><?= $estadoTxt ?></span></td>
        <td class="num" style="font-weight:600">S/ <?= number_format((float)$p['precio_unitario'], 2) ?></td>
        <td class="num">
          <form method="post" action="?r=productos/restock" style="display:inline">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <input type="hidden" name="cantidad" value="50">
            <button class="btn-ts btn-outline-ts" type="submit">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Reabastecer
            </button>
          </form>
        </td>
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
const boxSvg=`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>`;
const refreshSvg=`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>`;
pInput?.addEventListener('input',()=>{clearTimeout(pTimer);pTimer=setTimeout(async()=>{const r=await fetch(`?r=api/productos&q=${encodeURIComponent(pInput.value.trim())}`);const data=await r.json();pTable.innerHTML=(data.productos||[]).map(p=>`<tr><td><span class="prod-avatar">${boxSvg}</span><strong>${p.nombre}</strong></td><td style="color:#475569">${p.categoria}</td><td><span style="font-family:monospace;font-size:.82rem;color:#475569">${p.sku}</span></td><td class="num" style="font-weight:700">${p.stock_actual}</td><td><span class="tag ${estadoClass(p.estado)}">${p.estado}</span></td><td class="num" style="font-weight:600">S/ ${Number(p.precio_unitario).toFixed(2)}</td><td class="num"><form method="post" action="?r=productos/restock" style="display:inline"><input type="hidden" name="id" value="${p.id}"><input type="hidden" name="cantidad" value="50"><button class="btn-ts btn-outline-ts" type="submit">${refreshSvg} Reabastecer</button></form></td></tr>`).join('')||'<tr><td colspan="7" style="text-align:center;color:#64748b;padding:20px">Sin resultados</td></tr>';
},220);});
</script>
