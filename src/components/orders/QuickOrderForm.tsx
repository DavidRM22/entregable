import { useMemo, useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useStore, IGV } from "@/store/useStore";
import { useToast } from "@/hooks/use-toast";
import { ScanLine, Plus, Trash2 } from "lucide-react";
import { formatCurrency } from "@/lib/format";

interface Item {
  producto_id: number;
  cantidad: number;
}

export const QuickOrderForm = ({ onCreated }: { onCreated?: (pedidoId: number) => void }) => {
  const productos = useStore((s) => s.productos);
  const clientes = useStore((s) => s.clientes);
  const crearPedido = useStore((s) => s.crearPedido);
  const generarFactura = useStore((s) => s.generarFactura);
  const { toast } = useToast();

  const [clienteId, setClienteId] = useState<string>(String(clientes[0]?.id ?? ""));
  const [items, setItems] = useState<Item[]>([{ producto_id: productos[0]?.id ?? 0, cantidad: 1 }]);

  const totales = useMemo(() => {
    const neto = items.reduce((sum, it) => {
      const p = productos.find((x) => x.id === it.producto_id);
      return sum + (p ? p.precio_unitario * it.cantidad : 0);
    }, 0);
    const igv = +(neto * IGV).toFixed(2);
    return { neto: +neto.toFixed(2), igv, total: +(neto + igv).toFixed(2) };
  }, [items, productos]);

  const updateItem = (idx: number, patch: Partial<Item>) => {
    setItems((prev) => prev.map((it, i) => (i === idx ? { ...it, ...patch } : it)));
  };

  const submit = () => {
    const limpios = items.filter((it) => it.producto_id && it.cantidad > 0);
    if (limpios.length === 0) {
      toast({ title: "Agrega al menos un producto", variant: "destructive" });
      return;
    }
    const res = crearPedido({ cliente_id: Number(clienteId), items: limpios });
    if (!res.ok || !res.pedido) {
      toast({ title: "Error al crear pedido", description: res.error, variant: "destructive" });
      return;
    }
    const factura = generarFactura(res.pedido.id);
    toast({
      title: "Pedido creado",
      description: `${res.pedido.numero_pedido} • ${factura?.serie_correlativo ?? "Sin factura"}`,
    });
    setItems([{ producto_id: productos[0]?.id ?? 0, cantidad: 1 }]);
    onCreated?.(res.pedido.id);
  };

  return (
    <Card className="p-6 shadow-card border-border/60">
      <div className="flex items-start justify-between mb-5">
        <div>
          <h3 className="text-lg font-semibold tracking-tight">Entrada Rápida de Pedidos</h3>
          <p className="text-sm text-muted-foreground mt-0.5">
            Descuenta inventario en tiempo real y genera la factura electrónica
          </p>
        </div>
        <Button variant="outline" size="icon" type="button" aria-label="Escanear">
          <ScanLine className="h-4 w-4" />
        </Button>
      </div>

      <div className="space-y-4">
        <div>
          <Label className="text-xs uppercase tracking-wide text-muted-foreground">Cliente</Label>
          <Select value={clienteId} onValueChange={setClienteId}>
            <SelectTrigger className="mt-1.5"><SelectValue /></SelectTrigger>
            <SelectContent>
              {clientes.map((c) => (
                <SelectItem key={c.id} value={String(c.id)}>
                  {c.nombre_razon_social} — {c.documento_identidad}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2">
          <Label className="text-xs uppercase tracking-wide text-muted-foreground">Productos</Label>
          {items.map((it, idx) => {
            const prod = productos.find((p) => p.id === it.producto_id);
            const sinStock = prod && it.cantidad > prod.stock_actual;
            return (
              <div key={idx} className="grid grid-cols-12 gap-2 items-start">
                <div className="col-span-7">
                  <Select
                    value={String(it.producto_id)}
                    onValueChange={(v) => updateItem(idx, { producto_id: Number(v) })}
                  >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                      {productos.map((p) => (
                        <SelectItem key={p.id} value={String(p.id)} disabled={p.stock_actual === 0}>
                          {p.nombre} • Stock {p.stock_actual}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {sinStock && (
                    <p className="text-xs text-destructive mt-1">Cantidad supera stock disponible</p>
                  )}
                </div>
                <div className="col-span-3">
                  <Input
                    type="number"
                    min={1}
                    value={it.cantidad}
                    onChange={(e) => updateItem(idx, { cantidad: Math.max(1, Number(e.target.value)) })}
                  />
                </div>
                <div className="col-span-2 text-right text-sm font-medium tabular-nums pt-2">
                  {prod ? formatCurrency(prod.precio_unitario * it.cantidad) : "—"}
                </div>
                {items.length > 1 && (
                  <button
                    type="button"
                    onClick={() => setItems((p) => p.filter((_, i) => i !== idx))}
                    className="col-span-12 text-xs text-muted-foreground hover:text-destructive flex items-center gap-1"
                  >
                    <Trash2 className="h-3 w-3" /> Quitar
                  </button>
                )}
              </div>
            );
          })}
          <Button
            variant="ghost"
            size="sm"
            type="button"
            onClick={() => setItems((p) => [...p, { producto_id: productos[0]?.id ?? 0, cantidad: 1 }])}
            className="text-primary hover:text-primary"
          >
            <Plus className="h-4 w-4 mr-1" /> Agregar producto
          </Button>
        </div>

        <div className="border-t border-border pt-4 space-y-1.5 text-sm">
          <div className="flex justify-between text-muted-foreground">
            <span>Subtotal</span><span className="tabular-nums">{formatCurrency(totales.neto)}</span>
          </div>
          <div className="flex justify-between text-muted-foreground">
            <span>IGV (18%)</span><span className="tabular-nums">{formatCurrency(totales.igv)}</span>
          </div>
          <div className="flex justify-between text-base font-semibold pt-2 border-t border-border">
            <span>Total</span><span className="tabular-nums">{formatCurrency(totales.total)}</span>
          </div>
        </div>

        <Button onClick={submit} className="w-full bg-primary hover:bg-primary/90 text-primary-foreground h-11">
          Crear Pedido & Descontar Inventario
        </Button>
      </div>
    </Card>
  );
};
