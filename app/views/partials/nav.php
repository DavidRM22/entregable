<?php $active = $_GET['r'] ?? 'dashboard'; ?>
<nav class="sidebar-nav">
  <a class="side-link <?= str_starts_with($active, 'dashboard') ? 'active' : '' ?>" href="?r=dashboard">Dashboard</a>
  <a class="side-link <?= str_starts_with($active, 'pedidos') ? 'active' : '' ?>" href="?r=pedidos">Pedidos</a>
  <a class="side-link <?= str_starts_with($active, 'productos') ? 'active' : '' ?>" href="?r=productos">Productos</a>
  <a class="side-link <?= str_starts_with($active, 'facturas') ? 'active' : '' ?>" href="?r=facturas">Facturación</a>
  <a class="side-link <?= str_starts_with($active, 'reportes') ? 'active' : '' ?>" href="?r=reportes">Reportes</a>
</nav>
