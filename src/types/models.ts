// MVC - Models: Interfaces de dominio
export type EstadoPedido = "Procesando" | "Enviado" | "Entregado" | "Cancelado";
export type EstadoSunat = "Aceptado" | "Rechazado" | "Pendiente";

export interface Producto {
  id: number;
  nombre: string;
  sku: string;
  stock_actual: number;
  stock_minimo: number;
  precio_unitario: number;
  categoria: string;
  imagen_url?: string;
  fecha_actualizacion: string;
}

export interface Cliente {
  id: number;
  documento_identidad: string;
  nombre_razon_social: string;
  email?: string;
  direccion?: string;
}

export interface PedidoDetalle {
  id: number;
  producto_id: number;
  producto_nombre: string;
  cantidad: number;
  precio_unitario_venta: number;
  subtotal: number;
}

export interface Pedido {
  id: number;
  numero_pedido: string;
  cliente_id: number;
  cliente_nombre: string;
  fecha_pedido: string;
  estado: EstadoPedido;
  total_neto: number;
  total_impuestos: number;
  total_pagar: number;
  detalles: PedidoDetalle[];
}

export interface Factura {
  id: number;
  pedido_id: number;
  serie_correlativo: string;
  pdf_url?: string;
  estado_sunat: EstadoSunat;
  fecha_emision: string;
}

export interface Alerta {
  id: string;
  producto_id: number;
  producto_nombre: string;
  stock_actual: number;
  fecha: string;
}
