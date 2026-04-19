import { create } from "zustand";
import type {
  Producto,
  Cliente,
  Pedido,
  Factura,
  Alerta,
  PedidoDetalle,
} from "@/types/models";

// Controlador: lógica central que simula la base de datos en memoria
const IGV_RATE = 0.18;

interface NuevoPedidoInput {
  cliente_id: number;
  items: { producto_id: number; cantidad: number }[];
}

interface StoreState {
  productos: Producto[];
  clientes: Cliente[];
  pedidos: Pedido[];
  facturas: Factura[];
  alertas: Alerta[];

  crearPedido: (input: NuevoPedidoInput) => { ok: boolean; pedido?: Pedido; error?: string };
  actualizarEstadoPedido: (pedidoId: number, estado: Pedido["estado"]) => void;
  reabastecerProducto: (productoId: number, cantidad: number) => void;
  generarFactura: (pedidoId: number) => Factura | null;
  removerAlerta: (id: string) => void;
}

const productosIniciales: Producto[] = [
  { id: 1, nombre: "ProBook X15 Laptop", sku: "SKU-10234", stock_actual: 125, stock_minimo: 5, precio_unitario: 1299, categoria: "Laptops", fecha_actualizacion: new Date().toISOString() },
  { id: 2, nombre: "SonicFlow Wireless Headphones", sku: "SKU-20567", stock_actual: 15, stock_minimo: 5, precio_unitario: 89, categoria: "Audio", fecha_actualizacion: new Date().toISOString() },
  { id: 3, nombre: "Galaxy S24 Ultra 5G", sku: "SKU-30912", stock_actual: 0, stock_minimo: 5, precio_unitario: 1199, categoria: "Smartphones", fecha_actualizacion: new Date().toISOString() },
  { id: 4, nombre: "X-Play Series Z", sku: "SKU-41101", stock_actual: 450, stock_minimo: 5, precio_unitario: 499, categoria: "Gaming", fecha_actualizacion: new Date().toISOString() },
  { id: 5, nombre: "HomeHub Plus", sku: "SKU-52203", stock_actual: 8, stock_minimo: 5, precio_unitario: 149, categoria: "Smart Home", fecha_actualizacion: new Date().toISOString() },
  { id: 6, nombre: "TabGo Air 11", sku: "SKU-63315", stock_actual: 0, stock_minimo: 5, precio_unitario: 499, categoria: "Tablets", fecha_actualizacion: new Date().toISOString() },
  { id: 7, nombre: "Smart Watch Series 5", sku: "SKU-71208", stock_actual: 3, stock_minimo: 5, precio_unitario: 249, categoria: "Wearables", fecha_actualizacion: new Date().toISOString() },
  { id: 8, nombre: "Gaming Keyboard Pro", sku: "SKU-82102", stock_actual: 4, stock_minimo: 5, precio_unitario: 79, categoria: "Gaming", fecha_actualizacion: new Date().toISOString() },
];

const clientesIniciales: Cliente[] = [
  { id: 1, documento_identidad: "20512345678", nombre_razon_social: "Saga Falabella S.A.", email: "compras@falabella.pe", direccion: "Av. Paseo de la República 3220, Lima" },
  { id: 2, documento_identidad: "20487654321", nombre_razon_social: "Ripley Corp S.A.", email: "ventas@ripley.pe", direccion: "Av. Las Begonias 415, San Isidro" },
  { id: 3, documento_identidad: "44556677", nombre_razon_social: "Carlos Mendoza", email: "carlos@mail.com", direccion: "Calle Los Olivos 123" },
];

const pedidosIniciales: Pedido[] = [
  {
    id: 10234, numero_pedido: "1A26010310283", cliente_id: 1, cliente_nombre: "Saga Falabella S.A.",
    fecha_pedido: "2025-02-23T10:00:00", estado: "Entregado",
    total_neto: 1100, total_impuestos: 198, total_pagar: 1298,
    detalles: [{ id: 1, producto_id: 1, producto_nombre: "ProBook X15 Laptop", cantidad: 1, precio_unitario_venta: 1299, subtotal: 1299 }],
  },
  {
    id: 10233, numero_pedido: "1A26010310289", cliente_id: 2, cliente_nombre: "Ripley Corp S.A.",
    fecha_pedido: "2025-02-23T10:00:00", estado: "Enviado",
    total_neto: 211, total_impuestos: 38, total_pagar: 249,
    detalles: [{ id: 2, producto_id: 2, producto_nombre: "SonicFlow Wireless Headphones", cantidad: 1, precio_unitario_venta: 249, subtotal: 249 }],
  },
  {
    id: 10232, numero_pedido: "1A26010310391", cliente_id: 3, cliente_nombre: "Carlos Mendoza",
    fecha_pedido: "2025-02-15T10:00:00", estado: "Procesando",
    total_neto: 423, total_impuestos: 76, total_pagar: 499,
    detalles: [{ id: 3, producto_id: 6, producto_nombre: "TabGo Air 11", cantidad: 1, precio_unitario_venta: 499, subtotal: 499 }],
  },
  {
    id: 10231, numero_pedido: "1A26010310322", cliente_id: 1, cliente_nombre: "Saga Falabella S.A.",
    fecha_pedido: "2025-01-23T10:00:00", estado: "Entregado",
    total_neto: 75, total_impuestos: 14, total_pagar: 89,
    detalles: [{ id: 4, producto_id: 2, producto_nombre: "SonicFlow Wireless Headphones", cantidad: 1, precio_unitario_venta: 89, subtotal: 89 }],
  },
];

function generarAlertasIniciales(productos: Producto[]): Alerta[] {
  return productos
    .filter((p) => p.stock_actual < p.stock_minimo)
    .map((p) => ({
      id: `alert-init-${p.id}`,
      producto_id: p.id,
      producto_nombre: p.nombre,
      stock_actual: p.stock_actual,
      fecha: new Date().toISOString(),
    }));
}

export const useStore = create<StoreState>((set, get) => ({
  productos: productosIniciales,
  clientes: clientesIniciales,
  pedidos: pedidosIniciales,
  facturas: [],
  alertas: generarAlertasIniciales(productosIniciales),

  crearPedido: ({ cliente_id, items }) => {
    const state = get();
    const cliente = state.clientes.find((c) => c.id === cliente_id);
    if (!cliente) return { ok: false, error: "Cliente no encontrado" };

    // Validar stock disponible
    for (const item of items) {
      const prod = state.productos.find((p) => p.id === item.producto_id);
      if (!prod) return { ok: false, error: "Producto inexistente" };
      if (prod.stock_actual < item.cantidad)
        return { ok: false, error: `Stock insuficiente para ${prod.nombre}` };
    }

    const detalles: PedidoDetalle[] = items.map((it, idx) => {
      const prod = state.productos.find((p) => p.id === it.producto_id)!;
      return {
        id: idx + 1,
        producto_id: prod.id,
        producto_nombre: prod.nombre,
        cantidad: it.cantidad,
        precio_unitario_venta: prod.precio_unitario,
        subtotal: prod.precio_unitario * it.cantidad,
      };
    });

    const total_neto = detalles.reduce((sum, d) => sum + d.subtotal, 0);
    const total_impuestos = +(total_neto * IGV_RATE).toFixed(2);
    const total_pagar = +(total_neto + total_impuestos).toFixed(2);

    const nuevoId = Math.max(0, ...state.pedidos.map((p) => p.id)) + 1;
    const pedido: Pedido = {
      id: nuevoId,
      numero_pedido: `1A${Date.now().toString().slice(-10)}`,
      cliente_id,
      cliente_nombre: cliente.nombre_razon_social,
      fecha_pedido: new Date().toISOString(),
      estado: "Procesando",
      total_neto,
      total_impuestos,
      total_pagar,
      detalles,
    };

    // Descontar stock + generar alertas si stock < mínimo
    const productosActualizados = state.productos.map((p) => {
      const it = items.find((i) => i.producto_id === p.id);
      if (!it) return p;
      return { ...p, stock_actual: p.stock_actual - it.cantidad, fecha_actualizacion: new Date().toISOString() };
    });

    const nuevasAlertas: Alerta[] = [];
    productosActualizados.forEach((p) => {
      const yaAlerta = state.alertas.some((a) => a.producto_id === p.id);
      if (p.stock_actual < p.stock_minimo && !yaAlerta) {
        nuevasAlertas.push({
          id: `alert-${Date.now()}-${p.id}`,
          producto_id: p.id,
          producto_nombre: p.nombre,
          stock_actual: p.stock_actual,
          fecha: new Date().toISOString(),
        });
      }
    });

    set({
      productos: productosActualizados,
      pedidos: [pedido, ...state.pedidos],
      alertas: [...state.alertas, ...nuevasAlertas],
    });

    return { ok: true, pedido };
  },

  actualizarEstadoPedido: (pedidoId, estado) =>
    set((s) => ({
      pedidos: s.pedidos.map((p) => (p.id === pedidoId ? { ...p, estado } : p)),
    })),

  reabastecerProducto: (productoId, cantidad) =>
    set((s) => {
      const productos = s.productos.map((p) =>
        p.id === productoId
          ? { ...p, stock_actual: p.stock_actual + cantidad, fecha_actualizacion: new Date().toISOString() }
          : p
      );
      const prod = productos.find((p) => p.id === productoId);
      const alertas = prod && prod.stock_actual >= prod.stock_minimo
        ? s.alertas.filter((a) => a.producto_id !== productoId)
        : s.alertas;
      return { productos, alertas };
    }),

  generarFactura: (pedidoId) => {
    const state = get();
    const pedido = state.pedidos.find((p) => p.id === pedidoId);
    if (!pedido) return null;
    const existente = state.facturas.find((f) => f.pedido_id === pedidoId);
    if (existente) return existente;

    const correlativo = String(state.facturas.length + 1).padStart(6, "0");
    const factura: Factura = {
      id: state.facturas.length + 1,
      pedido_id: pedidoId,
      serie_correlativo: `F001-${correlativo}`,
      estado_sunat: "Aceptado",
      fecha_emision: new Date().toISOString(),
    };
    set({ facturas: [...state.facturas, factura] });
    return factura;
  },

  removerAlerta: (id) => set((s) => ({ alertas: s.alertas.filter((a) => a.id !== id) })),
}));

export const IGV = IGV_RATE;
