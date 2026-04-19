import { useMemo } from "react";
import { DollarSign, Clock, Package, AlertTriangle } from "lucide-react";
import { StatCard } from "@/components/dashboard/StatCard";
import { OrderTable } from "@/components/orders/OrderTable";
import { QuickOrderForm } from "@/components/orders/QuickOrderForm";
import { InventoryStatus } from "@/components/inventory/InventoryStatus";
import { InvoicePreview } from "@/components/invoices/InvoicePreview";
import { useStore } from "@/store/useStore";
import { formatCurrency } from "@/lib/format";
import { useState } from "react";

const Dashboard = () => {
  const pedidos = useStore((s) => s.pedidos);
  const productos = useStore((s) => s.productos);
  const alertas = useStore((s) => s.alertas);
  const [ultimoPedidoId, setUltimoPedidoId] = useState<number | null>(null);

  const stats = useMemo(() => {
    const ahora = new Date();
    const ventasMes = pedidos
      .filter((p) => new Date(p.fecha_pedido).getMonth() === ahora.getMonth())
      .reduce((s, p) => s + p.total_pagar, 0);
    const procesando = pedidos.filter((p) => p.estado === "Procesando").length;
    const totalProductos = productos.reduce((s, p) => s + p.stock_actual, 0);
    return { ventasMes, procesando, totalProductos };
  }, [pedidos, productos]);

  return (
    <div className="space-y-6 max-w-[1600px] mx-auto">
      <div>
        <h1 className="text-3xl font-bold tracking-tight text-foreground">TechSolutions Admin Dashboard</h1>
        <p className="text-muted-foreground mt-1">Gestión de inventario y facturación electrónica en tiempo real</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard label="Ventas (Este mes)" value={formatCurrency(stats.ventasMes)} icon={DollarSign} trend="+12%" tone="primary" />
        <StatCard label="Pedidos Pendientes" value={String(stats.procesando)} icon={Clock} tone="info" />
        <StatCard label="Stock Total" value={String(stats.totalProductos)} icon={Package} tone="success" />
        <StatCard label="Alertas Críticas" value={String(alertas.length)} icon={AlertTriangle} tone="warning" />
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div className="xl:col-span-2">
          <OrderTable title="Pedidos Recientes" limit={6} />
        </div>
        <div>
          <InventoryStatus />
        </div>
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div className="xl:col-span-2">
          <QuickOrderForm onCreated={setUltimoPedidoId} />
        </div>
        <div className="xl:col-span-3">
          <InvoicePreview pedidoId={ultimoPedidoId} />
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
