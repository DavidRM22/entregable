import { Card } from "@/components/ui/card";
import { useStore } from "@/store/useStore";

export const InventoryStatus = () => {
  const productos = useStore((s) => s.productos);
  // Tomamos los primeros 6 más relevantes (mezcla de bajo y alto stock)
  const lista = [...productos]
    .sort((a, b) => a.stock_actual / Math.max(a.stock_minimo, 1) - b.stock_actual / Math.max(b.stock_minimo, 1))
    .slice(0, 6);

  return (
    <Card className="p-6 shadow-card border-border/60">
      <div className="mb-5">
        <h3 className="text-lg font-semibold tracking-tight">Estado de Inventario en Tiempo Real</h3>
        <p className="text-sm text-muted-foreground mt-0.5">Niveles de stock por producto</p>
      </div>
      <div className="space-y-4">
        {lista.map((p) => {
          const ratio = Math.min(1, p.stock_actual / Math.max(p.stock_minimo * 4, 20));
          let tone: "ok" | "low" | "out" = "ok";
          if (p.stock_actual === 0) tone = "out";
          else if (p.stock_actual < p.stock_minimo) tone = "low";
          const label = tone === "out" ? "Sin stock" : tone === "low" ? "Stock Bajo" : "Stock Saludable";
          const barColor =
            tone === "out" ? "hsl(var(--destructive))"
            : tone === "low" ? "hsl(var(--warning))"
            : "hsl(var(--primary))";
          const labelColor =
            tone === "out" ? "text-destructive"
            : tone === "low" ? "text-warning"
            : "text-primary";

          return (
            <div key={p.id}>
              <div className="flex items-center justify-between mb-1.5">
                <div className="text-sm font-medium text-foreground truncate pr-3">{p.nombre}</div>
                <div className={`text-xs font-medium ${labelColor}`}>{label}</div>
              </div>
              <div className="h-2 rounded-full bg-muted overflow-hidden">
                <div
                  className="h-full rounded-full transition-all duration-500"
                  style={{ width: `${Math.max(4, ratio * 100)}%`, backgroundColor: barColor }}
                />
              </div>
              <div className="text-xs text-muted-foreground mt-1 tabular-nums">
                {p.stock_actual} unidades • mínimo {p.stock_minimo}
              </div>
            </div>
          );
        })}
      </div>
    </Card>
  );
};
