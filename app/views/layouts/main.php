<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($config['app_name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --brand:#10b981;--brand-dark:#059669;--brand-soft:#dcfce7;--brand-soft-2:#ecfdf5;
      --ink:#0f172a;--muted:#64748b;--border:#e5e7eb;--bg:#f6f8fb;
      --danger:#ef4444;--warn:#f59e0b;--info:#3b82f6;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{background:var(--bg);color:var(--ink);font-family:'Inter',-apple-system,Segoe UI,Roboto,sans-serif;margin:0}
    a{color:inherit}
    .app{display:flex;min-height:100vh}

    /* Sidebar */
    .sidebar{width:240px;background:#fff;border-right:1px solid var(--border);display:flex;flex-direction:column;position:sticky;top:0;height:100vh}
    .brand{display:flex;align-items:center;gap:12px;padding:20px 20px 24px}
    .brand-logo{width:40px;height:40px;border-radius:10px;background:var(--brand);display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0}
    .brand-name{font-weight:700;font-size:1.05rem;line-height:1.1}
    .brand-sub{display:block;color:var(--muted);font-size:.78rem;font-weight:500;margin-top:2px}
    .sidebar-nav{padding:4px 12px;display:flex;flex-direction:column;gap:2px;flex:1}
    .side-link{display:flex;align-items:center;gap:12px;text-decoration:none;color:#475569;padding:10px 14px;border-radius:10px;font-size:.92rem;font-weight:500;transition:background .15s,color .15s}
    .side-link:hover{background:#f1f5f9;color:var(--ink)}
    .side-link.active{background:var(--brand-soft-2);color:var(--brand-dark);font-weight:600}
    .side-link svg{width:18px;height:18px;flex-shrink:0}
    .sidebar-foot{padding:12px;border-top:1px solid var(--border)}

    /* Main */
    .main{flex:1;min-width:0;display:flex;flex-direction:column}
    .topbar{background:#fff;border-bottom:1px solid var(--border);padding:14px 28px;display:flex;align-items:center;gap:20px;position:sticky;top:0;z-index:10}
    .search-wrap{position:relative;flex:1;max-width:640px}
    .search-wrap svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;width:16px;height:16px}
    .global-search{width:100%;padding:10px 14px 10px 40px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;font-size:.9rem;outline:none;transition:border-color .15s,background .15s}
    .global-search:focus{border-color:var(--brand);background:#fff}
    .search-results{position:absolute;top:46px;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 10px 22px rgba(2,6,23,.1);display:none;max-height:300px;overflow:auto;z-index:20}
    .search-results a{display:block;padding:10px 14px;text-decoration:none;color:var(--ink);border-bottom:1px solid #f1f5f9}
    .search-results a small{display:block;color:var(--muted)}
    .search-results a:hover{background:#f8fafc}
    .bell{position:relative;color:#64748b;cursor:pointer;background:none;border:0;padding:6px}
    .bell svg{width:20px;height:20px}
    .bell::after{content:"";position:absolute;top:4px;right:4px;width:8px;height:8px;background:var(--danger);border-radius:50%;border:2px solid #fff}
    .user-chip{display:flex;align-items:center;gap:12px;padding-left:16px;border-left:1px solid var(--border)}
    .avatar{width:40px;height:40px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem}
    .user-info{line-height:1.2}
    .user-name{font-weight:600;font-size:.9rem}
    .user-mail{color:var(--muted);font-size:.8rem}

    .content{padding:28px 32px;flex:1}

    /* Panels */
    .panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px}
    .panel-title{font-size:1.05rem;font-weight:700;margin:0}
    .panel-sub{color:var(--muted);font-size:.85rem;margin:2px 0 0}

    /* Page title */
    .page-title{font-size:1.85rem;font-weight:800;margin:0 0 4px;letter-spacing:-.01em}
    .page-sub{color:var(--muted);margin:0 0 24px;font-size:.92rem}

    /* KPI cards */
    .kpi{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 20px;display:flex;gap:14px;align-items:center}
    .kpi-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .kpi-icon svg{width:20px;height:20px}
    .kpi-icon.bg-green{background:#d1fae5;color:#047857}
    .kpi-icon.bg-blue{background:#dbeafe;color:#1d4ed8}
    .kpi-icon.bg-teal{background:#ccfbf1;color:#0f766e}
    .kpi-icon.bg-yellow{background:#fef3c7;color:#b45309}
    .kpi-label{color:var(--muted);font-size:.82rem;margin-bottom:2px}
    .kpi-value{font-size:1.55rem;font-weight:800;color:var(--ink);letter-spacing:-.01em}
    .kpi-delta{color:#059669;font-size:.8rem;font-weight:600;margin-left:6px}

    /* Tags / badges */
    .tag{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:999px;font-size:.78rem;font-weight:600}
    .tag::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
    .tag-ok{background:#dcfce7;color:#166534}
    .tag-warn{background:#fef3c7;color:#b45309}
    .tag-bad{background:#fee2e2;color:#b91c1c}
    .tag-info{background:#dbeafe;color:#1d4ed8}
    .tag-warn::before{content:"\26A0";background:transparent;font-size:.8rem;line-height:1;width:auto;height:auto;color:#b45309}

    /* Tables */
    .ts-table{width:100%;border-collapse:collapse}
    .ts-table thead th{text-align:left;padding:12px 14px;font-size:.8rem;font-weight:600;color:var(--muted);border-bottom:1px solid var(--border);background:transparent}
    .ts-table tbody td{padding:16px 14px;border-bottom:1px solid #f1f5f9;font-size:.9rem;vertical-align:middle}
    .ts-table tbody tr:last-child td{border-bottom:0}
    .ts-table .num{text-align:right}

    /* Buttons */
    .btn-ts{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;font-size:.88rem;font-weight:600;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:background .15s,border-color .15s,color .15s}
    .btn-ts svg{width:14px;height:14px}
    .btn-primary-ts{background:var(--brand);color:#fff}
    .btn-primary-ts:hover{background:var(--brand-dark);color:#fff}
    .btn-blue{background:#2563eb;color:#fff}
    .btn-blue:hover{background:#1d4ed8;color:#fff}
    .btn-outline-ts{background:#fff;color:#334155;border-color:var(--border)}
    .btn-outline-ts:hover{background:#f8fafc;color:var(--ink)}
    .btn-ghost{background:transparent;color:#334155;border-color:var(--border)}
    .btn-ghost:hover{background:#f1f5f9}

    /* Form controls */
    .form-label-ts{display:block;font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px}
    .form-input-ts{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:10px;background:#fff;font-size:.9rem;outline:none;transition:border-color .15s}
    .form-input-ts:focus{border-color:var(--brand)}
    select.form-input-ts{appearance:none;background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8' fill='none'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%2364748b' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px}

    /* Product avatar */
    .prod-avatar{width:36px;height:36px;border-radius:8px;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;color:#64748b;margin-right:10px;vertical-align:middle}
    .prod-avatar svg{width:18px;height:18px}

    /* Stock bar */
    .stock-row{padding:10px 0}
    .stock-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
    .stock-name{font-weight:600;font-size:.9rem}
    .stock-status{font-size:.78rem;font-weight:600}
    .stock-status.ok{color:#059669}
    .stock-status.warn{color:#b45309}
    .stock-status.bad{color:#b91c1c}
    .stock-bar{height:6px;background:#f1f5f9;border-radius:999px;overflow:hidden}
    .stock-bar > div{height:100%;border-radius:999px}
    .stock-bar .ok{background:#10b981}
    .stock-bar .warn{background:#f59e0b}
    .stock-bar .bad{background:#ef4444}
    .stock-meta{color:var(--muted);font-size:.78rem;margin-top:4px}

    /* Invoice card */
    .invoice{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px}
    .invoice-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px}
    .invoice-brand{display:inline-block;background:var(--brand);color:#fff;padding:6px 14px;border-radius:8px;font-weight:700;font-size:.9rem}
    .invoice-title{font-size:.88rem;font-weight:700;letter-spacing:.02em;color:var(--ink);text-align:right}
    .invoice-number{color:var(--muted);font-size:.85rem;text-align:right;margin-top:2px}
    .invoice-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;padding:14px 0;border-top:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;margin:14px 0}
    .invoice-meta-grid small{display:block;color:var(--muted);font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;margin-bottom:4px}
    .invoice-meta-grid strong{font-size:.9rem}
    .invoice-table{width:100%;margin:8px 0}
    .invoice-table th{padding:10px 0;font-size:.78rem;font-weight:600;color:var(--muted);text-align:left;border-bottom:1px solid #f1f5f9}
    .invoice-table th.num,.invoice-table td.num{text-align:right}
    .invoice-table td{padding:12px 0;font-size:.9rem;border-bottom:1px solid #f1f5f9}
    .invoice-totals{margin-top:10px}
    .invoice-totals .row-t{display:flex;justify-content:space-between;padding:4px 0;font-size:.88rem;color:#475569}
    .invoice-totals .row-t.grand{border-top:1px solid var(--border);margin-top:8px;padding-top:10px;font-size:1.05rem;font-weight:700;color:var(--ink)}

    /* Facturas list */
    .fact-item{display:block;padding:16px;border-radius:12px;margin-bottom:10px;text-decoration:none;color:inherit;border:1px solid transparent;transition:background .15s,border-color .15s}
    .fact-item:hover{background:#f8fafc;color:inherit}
    .fact-item.active{background:var(--brand-soft-2);border-color:#a7f3d0}
    .fact-item .name{font-weight:700;font-size:.95rem}
    .fact-item .num{color:var(--muted);font-size:.8rem;margin-top:2px}
    .fact-item .row2{display:flex;justify-content:space-between;align-items:center;margin-top:8px}
    .fact-item .date{color:var(--muted);font-size:.82rem}
    .fact-item .amount{font-weight:700;font-size:.95rem}

    /* Report row */
    .report-row{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-bottom:1px solid #f1f5f9}
    .report-row:last-child{border-bottom:0}
    .report-row .name{font-weight:600}
    .report-row .cat{color:var(--muted);font-size:.82rem;margin-top:2px}
    .report-row .count{font-weight:700;font-size:.95rem;text-align:right}
    .report-row .amount{color:var(--muted);font-size:.82rem;text-align:right;margin-top:2px}

    /* Utilities */
    .flex-between{display:flex;justify-content:space-between;align-items:center}
    .stack-sm{display:flex;flex-direction:column;gap:8px}
    .row-g{display:grid;gap:20px}
    @media(min-width:992px){.grid-4{grid-template-columns:repeat(4,1fr)}.grid-3{grid-template-columns:repeat(3,1fr)}.grid-2-3{grid-template-columns:2fr 1fr}.grid-1-2{grid-template-columns:1fr 2fr}.grid-1-1{grid-template-columns:1fr 1fr}}
    @media(max-width:991px){.grid-4,.grid-3{grid-template-columns:1fr 1fr}.grid-2-3,.grid-1-2,.grid-1-1{grid-template-columns:1fr}}
    @media(max-width:575px){.grid-4,.grid-3{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-logo">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      </div>
      <div>
        <div class="brand-name">TechSolutions</div>
        <small class="brand-sub">Retail Management</small>
      </div>
    </div>
    <?php require __DIR__ . '/../partials/nav.php'; ?>
    <div class="sidebar-foot">
      <a class="side-link" href="#">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        Settings
      </a>
    </div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input id="global-search" class="global-search" placeholder="Buscar productos, pedidos o clientes...">
        <div id="global-results" class="search-results"></div>
      </div>
      <button class="bell" aria-label="Notificaciones">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <div class="user-chip">
        <div class="avatar">AU</div>
        <div class="user-info">
          <div class="user-name">Admin User</div>
          <div class="user-mail">admin@techsolutions.pe</div>
        </div>
      </div>
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
