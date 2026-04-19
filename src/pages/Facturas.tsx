import { useSearchParams } from "react-router-dom";
import { Card } from "@/components/ui/card";
import { useStore } from "@/store/useStore";
import { InvoicePreview } from "@/components/invoices/InvoicePreview";
import { formatShortDate } from "@/lib/format";
import { cn } from "@/lib/utils";
import { useState } from "react";

const Facturas = () => {
  const [params] = useSearchParams();
  const initial = params.get("pedido") ? Number(params.get("pedido")) : null;
  const pedidos = useStore((s) => s.pedidos);
  const [seleccionado, setSeleccionado] = useState<number | null>(initial ?? pedidos[0]?.id ?? null);

  return (
    <div className="space-y-6 max-w-[1600px] mx-auto">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Facturación Electrónica</h1>
        <p className="text-muted-foreground mt-1">Genera y descarga facturas electrónicas con IGV calculado</p>
      </div>
      <div className="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <Card className="p-2 shadow-card border-border/60 xl:col-span-1 max-h-[700px] overflow-auto">
          {pedidos.map((p) => (
            <button
              key={p.id}
              onClick={() => setSeleccionado(p.id)}
              className={cn(
                "w-full text-left p-3 rounded-lg transition-colors",
                seleccionado === p.id ? "bg-primary-soft" : "hover:bg-muted"
              )}
              style={seleccionado === p.id ? { backgroundColor: "hsl(var(--primary-soft))" } : undefined}
            >
              <div className="text-sm font-semibold">{p.cliente_nombre}</div>
              <div className="text-xs text-muted-foreground font-mono">{p.numero_pedido}</div>
              <div className="flex items-center justify-between mt-1.5">
                <span className="text-xs text-muted-foreground">{formatShortDate(p.fecha_pedido)}</span>
                <span className="text-sm font-semibold tabular-nums">S/ {p.total_pagar.toFixed(2)}</span>
              </div>
            </button>
          ))}
        </Card>
        <div className="xl:col-span-3">
          <InvoicePreview pedidoId={seleccionado} />
        </div>
      </div>
    </div>
  );
};
export default Facturas;
