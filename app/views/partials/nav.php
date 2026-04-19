<?php $active = $_GET['r'] ?? 'dashboard'; ?>
<ul class="nav nav-pills gap-2">
  <li class="nav-item"><a class="nav-link <?= str_starts_with($active, 'dashboard') ? 'active' : '' ?>" href="?r=dashboard">Dashboard</a></li>
  <li class="nav-item"><a class="nav-link <?= str_starts_with($active, 'productos') ? 'active' : '' ?>" href="?r=productos">Productos</a></li>
  <li class="nav-item"><a class="nav-link <?= str_starts_with($active, 'pedidos') ? 'active' : '' ?>" href="?r=pedidos">Pedidos</a></li>
  <li class="nav-item"><a class="nav-link <?= str_starts_with($active, 'facturas') ? 'active' : '' ?>" href="?r=facturas">Facturas</a></li>
  <li class="nav-item"><a class="nav-link <?= str_starts_with($active, 'reportes') ? 'active' : '' ?>" href="?r=reportes">Reportes</a></li>
</ul>
