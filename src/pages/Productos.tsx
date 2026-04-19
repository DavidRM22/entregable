import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { useStore } from "@/store/useStore";
import { Search, RefreshCw, Filter, Plus, Package } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

const Productos = () => {
  const productos = useStore((s) => s.productos);
  const reabastecer = useStore((s) => s.reabastecerProducto);
  const [q, setQ] = useState("");
  const { toast } = useToast();

  const filtrados = productos.filter((p) =>
    [p.nombre, p.sku, p.categoria].some((f) => f.toLowerCase().includes(q.toLowerCase()))
  );

  const onRestock = (id: number, nombre: string) => {
    reabastecer(id, 50);
    toast({ title: "Stock actualizado", description: `${nombre} +50 unidades` });
  };

  const statusBadge = (p: typeof productos[number]) => {
    if (p.stock_actual === 0) return <Badge className="bg-destructive/10 text-destructive border-destructive/30 rounded-full" variant="outline">● Sin Stock</Badge>;
    if (p.stock_actual < p.stock_minimo) return <Badge className="bg-warning-soft text-warning border-warning/40 rounded-full" variant="outline">⚠ Stock Bajo</Badge>;
    return <Badge className="bg-success-soft text-success border-success/30 rounded-full" variant="outline">● En Stock</Badge>;
  };

  return (
    <div className="space-y-6 max-w-[1600px] mx-auto">
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Inventario</h1>
          <p className="text-muted-foreground mt-1">{productos.length} productos en catálogo</p>
        </div>
        <div className="flex gap-2">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Buscar productos, SKU..." className="pl-9 w-72" />
          </div>
          <Button variant="outline"><Filter className="h-4 w-4 mr-2" />Filtrar</Button>
          <Button className="bg-info hover:bg-info/90 text-info-foreground"><Plus className="h-4 w-4 mr-2" />Nuevo Producto</Button>
        </div>
      </div>

      <Card className="shadow-card border-border/60">
        <Table>
          <TableHeader>
            <TableRow className="hover:bg-transparent">
              <TableHead>Producto</TableHead>
              <TableHead>Categoría</TableHead>
              <TableHead>SKU</TableHead>
              <TableHead className="text-right">Stock</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Precio</TableHead>
              <TableHead className="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filtrados.map((p) => (
              <TableRow key={p.id}>
                <TableCell>
                  <div className="flex items-center gap-3">
                    <div className="h-10 w-10 rounded-lg bg-muted flex items-center justify-center">
                      <Package className="h-5 w-5 text-muted-foreground" />
                    </div>
                    <span className="font-medium">{p.nombre}</span>
                  </div>
                </TableCell>
                <TableCell className="text-muted-foreground">{p.categoria}</TableCell>
                <TableCell className="font-mono text-xs text-muted-foreground">{p.sku}</TableCell>
                <TableCell className="text-right font-semibold tabular-nums">{p.stock_actual}</TableCell>
                <TableCell>{statusBadge(p)}</TableCell>
                <TableCell className="text-right tabular-nums">S/ {p.precio_unitario.toFixed(2)}</TableCell>
                <TableCell className="text-right">
                  <Button size="sm" variant="outline" onClick={() => onRestock(p.id, p.nombre)}>
                    <RefreshCw className="h-3.5 w-3.5 mr-1.5" />Reabastecer
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
};
export default Productos;
