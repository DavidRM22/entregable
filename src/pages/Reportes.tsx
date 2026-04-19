import { Card } from "@/components/ui/card";
import { useStore } from "@/store/useStore";
import { formatCurrency } from "@/lib/format";

const Reportes = () => {
  const pedidos = useStore((s) => s.pedidos);
  const productos = useStore((s) => s.productos);

  const ventasTotales = pedidos.reduce((s, p) => s + p.total_pagar, 0);
  const igvTotal = pedidos.reduce((s, p) => s + p.total_impuestos, 0);
  const masVendidos = [...productos]
    .map((p) => {
      const vendido = pedidos.flatMap((x) => x.detalles).filter((d) => d.producto_id === p.id)
        .reduce((s, d) => s + d.cantidad, 0);
      return { ...p, vendido };
    })
    .sort((a, b) => b.vendido - a.vendido)
    .slice(0, 5);

  return (
    <div className="space-y-6 max-w-[1600px] mx-auto">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Reportes</h1>
        <p className="text-muted-foreground mt-1">Análisis de ventas e inventario</p>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card className="p-6 shadow-card">
          <div className="text-sm text-muted-foreground">Ventas totales</div>
          <div className="text-3xl font-bold mt-2">{formatCurrency(ventasTotales)}</div>
        </Card>
        <Card className="p-6 shadow-card">
          <div className="text-sm text-muted-foreground">IGV recaudado</div>
          <div className="text-3xl font-bold mt-2">{formatCurrency(igvTotal)}</div>
        </Card>
        <Card className="p-6 shadow-card">
          <div className="text-sm text-muted-foreground">Pedidos totales</div>
          <div className="text-3xl font-bold mt-2">{pedidos.length}</div>
        </Card>
      </div>
      <Card className="p-6 shadow-card">
        <h3 className="text-lg font-semibold mb-4">Top productos vendidos</h3>
        <div className="space-y-3">
          {masVendidos.map((p) => (
            <div key={p.id} className="flex items-center justify-between border-b border-border last:border-0 pb-3 last:pb-0">
              <div>
                <div className="font-medium">{p.nombre}</div>
                <div className="text-xs text-muted-foreground">{p.categoria}</div>
              </div>
              <div className="text-right">
                <div className="font-semibold tabular-nums">{p.vendido} uds</div>
                <div className="text-xs text-muted-foreground">{formatCurrency(p.vendido * p.precio_unitario)}</div>
              </div>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
};
export default Reportes;
