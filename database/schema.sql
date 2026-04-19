CREATE DATABASE IF NOT EXISTS techsolutions_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techsolutions_inventory;

DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS pedido_detalles;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS alertas;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS productos;

CREATE TABLE productos (
  id INT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  sku VARCHAR(30) NOT NULL UNIQUE,
  stock_actual INT NOT NULL,
  stock_minimo INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  categoria VARCHAR(80) NOT NULL,
  imagen_url VARCHAR(255) NULL,
  fecha_actualizacion DATETIME NOT NULL
);

CREATE TABLE clientes (
  id INT PRIMARY KEY,
  documento_identidad VARCHAR(20) NOT NULL,
  nombre_razon_social VARCHAR(150) NOT NULL,
  email VARCHAR(120) NULL,
  direccion VARCHAR(180) NULL
);

CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero_pedido VARCHAR(25) NOT NULL UNIQUE,
  cliente_id INT NOT NULL,
  fecha_pedido DATETIME NOT NULL,
  estado ENUM('Procesando','Enviado','Entregado','Cancelado') NOT NULL DEFAULT 'Procesando',
  total_neto DECIMAL(10,2) NOT NULL,
  total_impuestos DECIMAL(10,2) NOT NULL,
  total_pagar DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE pedido_detalles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  producto_id INT NOT NULL,
  producto_nombre VARCHAR(150) NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario_venta DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE facturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL UNIQUE,
  serie_correlativo VARCHAR(20) NOT NULL UNIQUE,
  pdf_url VARCHAR(255) NULL,
  estado_sunat ENUM('Aceptado','Rechazado','Pendiente') NOT NULL DEFAULT 'Aceptado',
  fecha_emision DATETIME NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

CREATE TABLE alertas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto_id INT NOT NULL,
  producto_nombre VARCHAR(150) NOT NULL,
  stock_actual INT NOT NULL,
  fecha DATETIME NOT NULL,
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

INSERT INTO productos (id, nombre, sku, stock_actual, stock_minimo, precio_unitario, categoria, fecha_actualizacion) VALUES
(1,'ProBook X15 Laptop','SKU-10234',125,5,1299,'Laptops',NOW()),
(2,'SonicFlow Wireless Headphones','SKU-20567',15,5,89,'Audio',NOW()),
(3,'Galaxy S24 Ultra 5G','SKU-30912',0,5,1199,'Smartphones',NOW()),
(4,'X-Play Series Z','SKU-41101',450,5,499,'Gaming',NOW()),
(5,'HomeHub Plus','SKU-52203',8,5,149,'Smart Home',NOW()),
(6,'TabGo Air 11','SKU-63315',0,5,499,'Tablets',NOW()),
(7,'Smart Watch Series 5','SKU-71208',3,5,249,'Wearables',NOW()),
(8,'Gaming Keyboard Pro','SKU-82102',4,5,79,'Gaming',NOW());

INSERT INTO clientes (id, documento_identidad, nombre_razon_social, email, direccion) VALUES
(1,'20512345678','Saga Falabella S.A.','compras@falabella.pe','Av. Paseo de la República 3220, Lima'),
(2,'20487654321','Ripley Corp S.A.','ventas@ripley.pe','Av. Las Begonias 415, San Isidro'),
(3,'44556677','Carlos Mendoza','carlos@mail.com','Calle Los Olivos 123');

INSERT INTO pedidos (id, numero_pedido, cliente_id, fecha_pedido, estado, total_neto, total_impuestos, total_pagar) VALUES
(10234,'1A26010310283',1,'2025-02-23 10:00:00','Entregado',1100,198,1298),
(10233,'1A26010310289',2,'2025-02-23 10:00:00','Enviado',211,38,249),
(10232,'1A26010310391',3,'2025-02-15 10:00:00','Procesando',423,76,499),
(10231,'1A26010310322',1,'2025-01-23 10:00:00','Entregado',75,14,89);

INSERT INTO pedido_detalles (pedido_id, producto_id, producto_nombre, cantidad, precio_unitario_venta, subtotal) VALUES
(10234,1,'ProBook X15 Laptop',1,1299,1299),
(10233,2,'SonicFlow Wireless Headphones',1,249,249),
(10232,6,'TabGo Air 11',1,499,499),
(10231,2,'SonicFlow Wireless Headphones',1,89,89);

INSERT INTO alertas (producto_id, producto_nombre, stock_actual, fecha) VALUES
(3,'Galaxy S24 Ultra 5G',0,NOW()),
(6,'TabGo Air 11',0,NOW()),
(7,'Smart Watch Series 5',3,NOW()),
(8,'Gaming Keyboard Pro',4,NOW());
