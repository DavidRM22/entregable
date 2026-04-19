<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($config['app_name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f6f8fb;color:#0f172a}.app{display:flex;min-height:100vh}.sidebar{width:250px;background:#fff;border-right:1px solid #e5e7eb;padding:16px}.brand{font-weight:700;font-size:1.4rem}.brand small{display:block;color:#64748b;font-size:.9rem}.sidebar-nav{margin-top:24px;display:flex;flex-direction:column;gap:8px}.side-link{text-decoration:none;color:#334155;padding:10px 14px;border-radius:10px}.side-link.active,.side-link:hover{background:#dcfce7;color:#065f46}.main{flex:1}.topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:10px 24px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:10}.search-wrap{position:relative;flex:1;max-width:700px}.global-search{width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px}.search-results{position:absolute;top:44px;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 10px 22px rgba(2,6,23,.1);display:none;max-height:280px;overflow:auto}.search-results a{display:block;padding:10px 12px;text-decoration:none;color:#0f172a;border-bottom:1px solid #f1f5f9}.search-results a small{display:block;color:#64748b}.search-results a:hover{background:#f8fafc}.content{padding:24px}.panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px}.metric{font-size:2rem;font-weight:800}.tag{padding:2px 10px;border-radius:999px;font-size:.85rem}.tag-ok{background:#dcfce7;color:#166534}.tag-warn{background:#fef3c7;color:#b45309}.tag-bad{background:#fee2e2;color:#b91c1c}
  </style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">TechSolutions <small>Retail Management</small></div>
    <?php require __DIR__ . '/../partials/nav.php'; ?>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="search-wrap">
        <input id="global-search" class="global-search" placeholder="Buscar productos, pedidos o clientes...">
        <div id="global-results" class="search-results"></div>
      </div>
      <div class="fw-semibold">Admin User</div>
    </header>
    <main class="content"><?= $content ?></main>
  </div>
</div>
<script>
const globalInput=document.getElementById('global-search');
const globalResults=document.getElementById('global-results');
let searchTimer;
globalInput?.addEventListener('input',()=>{clearTimeout(searchTimer);const q=globalInput.value.trim();if(q.length<2){globalResults.style.display='none';return;}searchTimer=setTimeout(async()=>{const r=await fetch(`?r=api/search&q=${encodeURIComponent(q)}`);const data=await r.json();globalResults.innerHTML=(data.items||[]).map(i=>`<a href="${i.url}"><strong>${i.titulo}</strong><small>${i.tipo} · ${i.subtitulo}</small></a>`).join('')||'<div class="p-2 text-muted">Sin resultados</div>';globalResults.style.display='block';},220);});
document.addEventListener('click',e=>{if(!globalResults.contains(e.target)&&e.target!==globalInput){globalResults.style.display='none';}});
async function refreshDashboardMetrics(){const cards=document.querySelector('[data-metrics]');if(!cards)return;const r=await fetch('?r=api/dashboard-metrics');const d=await r.json();cards.querySelector('[data-kpi="ventas"]').textContent=`S/ ${Number(d.ventasMes).toFixed(2)}`;cards.querySelector('[data-kpi="pedidos"]').textContent=d.procesando;cards.querySelector('[data-kpi="stock"]').textContent=d.stockTotal;cards.querySelector('[data-kpi="alertas"]').textContent=d.alertas;}
refreshDashboardMetrics();setInterval(refreshDashboardMetrics,12000);
</script>
</body>
</html>
