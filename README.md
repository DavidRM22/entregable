# TechSolutions Inventory Suite - PHP MVC (XAMPP)

Migración del sistema original a **PHP 8 + MySQL** con patrón **MVC**, manteniendo funcionalidades principales:

- Dashboard con métricas (ventas del mes, pedidos pendientes, stock, alertas).
- Inventario de productos con búsqueda y reabastecimiento (+50).
- Pedidos: creación rápida con validación de stock y cambio de estado.
- Facturas: generación por pedido con correlativo `F001-000001`.
- Reportes: ventas totales, IGV y top productos vendidos.

---

## Estructura MVC

- `public/index.php`: Front Controller + enrutamiento.
- `app/controllers/*`: controladores por módulo.
- `app/models/*`: acceso a datos y reglas de negocio.
- `app/views/*`: vistas HTML (Bootstrap).
- `database/schema.sql`: base de datos completa para MySQL.

---

## Requisitos

- XAMPP con:
  - Apache
  - MySQL
  - PHP 8+

---

## Instalación en XAMPP

1. Copia este repositorio a `htdocs`, por ejemplo:
   - `C:\xampp\htdocs\techsolutions-inventory-suite`
2. Inicia **Apache** y **MySQL** desde XAMPP Control Panel.
3. Crea la base de datos e importa el script:
   - Abre `http://localhost/phpmyadmin`
   - Importa `database/schema.sql`
4. Verifica la conexión en:
   - `app/config/config.php`
   - Por defecto usa `root` sin contraseña.
5. Ejecuta en el navegador:
   - `http://localhost/techsolutions-inventory-suite/public/`

---

## Rutas principales

- `?r=dashboard`
- `?r=productos`
- `?r=pedidos`
- `?r=facturas`
- `?r=reportes`

---

## Nota de compatibilidad

El frontend original en React/TypeScript se mantiene en el repositorio como referencia histórica, pero la versión ejecutable para XAMPP es la implementación PHP MVC descrita arriba.
