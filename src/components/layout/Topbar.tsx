import { Bell, Search } from "lucide-react";
import { Input } from "@/components/ui/input";
import { useStore } from "@/store/useStore";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Badge } from "@/components/ui/badge";

export const Topbar = () => {
  const alertas = useStore((s) => s.alertas);
  return (
    <header className="h-16 border-b border-border bg-card px-4 lg:px-8 flex items-center gap-4">
      <div className="flex-1 max-w-xl relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <Input
          placeholder="Buscar productos, pedidos o clientes..."
          className="pl-9 bg-secondary border-transparent focus-visible:bg-card"
        />
      </div>
      <Popover>
        <PopoverTrigger asChild>
          <button className="relative h-10 w-10 rounded-full hover:bg-muted flex items-center justify-center transition-colors">
            <Bell className="h-5 w-5 text-muted-foreground" />
            {alertas.length > 0 && (
              <span className="absolute top-2 right-2 h-2 w-2 rounded-full bg-destructive" />
            )}
          </button>
        </PopoverTrigger>
        <PopoverContent align="end" className="w-80 p-0">
          <div className="px-4 py-3 border-b border-border">
            <div className="text-sm font-semibold">Alertas de Stock</div>
            <div className="text-xs text-muted-foreground">{alertas.length} producto(s) bajo el mínimo</div>
          </div>
          <div className="max-h-72 overflow-auto divide-y divide-border">
            {alertas.length === 0 && (
              <div className="px-4 py-8 text-center text-sm text-muted-foreground">Sin alertas activas</div>
            )}
            {alertas.map((a) => (
              <div key={a.id} className="px-4 py-3 text-sm">
                <div className="font-medium text-foreground">{a.producto_nombre}</div>
                <div className="text-xs text-muted-foreground mt-0.5 flex items-center gap-2">
                  <Badge variant="outline" className="border-warning/40 text-warning bg-warning-soft">
                    {a.stock_actual} unidades
                  </Badge>
                  Stock crítico
                </div>
              </div>
            ))}
          </div>
        </PopoverContent>
      </Popover>
      <div className="flex items-center gap-3 pl-3 border-l border-border">
        <div className="h-9 w-9 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-semibold">
          AU
        </div>
        <div className="hidden md:block">
          <div className="text-sm font-medium leading-tight">Admin User</div>
          <div className="text-xs text-muted-foreground">admin@techsolutions.pe</div>
        </div>
      </div>
    </header>
  );
};
