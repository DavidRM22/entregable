# TechSolutions Inventory Suite - PHP MVC (XAMPP)

Proyecto migrado completamente a **PHP 8 + MySQL** usando patrón **MVC**, listo para ejecutar en **XAMPP**.

## Funcionalidades

- Dashboard con métricas (ventas del mes, pedidos pendientes, stock y alertas).
- Gestión de productos con búsqueda y reabastecimiento (+50 unidades).
- Gestión de pedidos con validación de stock y actualización de estado.
- Facturación con generación por pedido y correlativo `F001-000001`.
- Reportes de ventas, IGV y top de productos vendidos.

## Estructura

- `index.php`: Front Controller principal (entrada directa para `http://localhost/index.php`).
- `public/index.php`: Punto de entrada alternativo compatible (redirige al `index.php` raíz).
- `app/controllers/*`: controladores por módulo.
- `app/models/*`: acceso a datos y reglas de negocio.
- `app/views/*`: vistas HTML.
- `database/schema.sql`: esquema y datos base de MySQL.

## Requisitos

- XAMPP con:
  - Apache
  - MySQL
  - PHP 8+

## Instalación en XAMPP

1. Copia este repositorio dentro de `htdocs`.
2. Inicia **Apache** y **MySQL** en XAMPP.
3. Crea la base de datos e importa `database/schema.sql` desde phpMyAdmin.
4. Verifica credenciales de conexión en `app/config/config.php`.
5. Abre en el navegador:
   - `http://localhost/<nombre-del-proyecto>/index.php`

## Rutas principales

- `?r=dashboard`
- `?r=productos`
- `?r=pedidos`
- `?r=facturas`
- `?r=reportes`
