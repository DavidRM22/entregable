import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { useStore } from "@/store/useStore";
import { formatShortDate } from "@/lib/format";
import type { EstadoPedido } from "@/types/models";
import { useNavigate } from "react-router-dom";

const estadoStyle: Record<EstadoPedido, string> = {
  Entregado: "bg-success-soft text-success border-success/30",
  Enviado: "bg-info-soft text-info border-info/30",
  Procesando: "bg-warning-soft text-warning border-warning/40",
  Cancelado: "bg-destructive/10 text-destructive border-destructive/30",
};

interface Props {
  title?: string;
  subtitle?: string;
  limit?: number;
  showCheckbox?: boolean;
}

export const OrderTable = ({ title = "Pedidos Recientes", subtitle, limit, showCheckbox = true }: Props) => {
  const pedidos = useStore((s) => s.pedidos);
  const navigate = useNavigate();
  const lista = limit ? pedidos.slice(0, limit) : pedidos;
  const procesando = pedidos.filter((p) => p.estado === "Procesando").length;

  return (
    <Card className="shadow-card border-border/60">
      <div className="px-6 py-5 border-b border-border">
        <h3 className="text-lg font-semibold tracking-tight">{title}</h3>
        <p className="text-sm text-muted-foreground mt-0.5">
          {subtitle ?? `${procesando} pedido(s) en procesamiento`}
        </p>
      </div>
      <div className="overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow className="border-border hover:bg-transparent">
              {showCheckbox && (
                <TableHead className="w-10">
                  <Checkbox />
                </TableHead>
              )}
              <TableHead>N° Pedido</TableHead>
              <TableHead>Producto</TableHead>
              <TableHead>Fecha</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Total</TableHead>
              <TableHead className="text-right">Acción</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {lista.map((p) => (
              <TableRow key={p.id} className="border-border">
                {showCheckbox && (
                  <TableCell>
                    <Checkbox />
                  </TableCell>
                )}
                <TableCell className="font-mono text-xs">{p.numero_pedido}</TableCell>
                <TableCell className="font-medium">
                  {p.detalles[0]?.producto_nombre}
                  {p.detalles.length > 1 && (
                    <span className="text-muted-foreground"> +{p.detalles.length - 1}</span>
                  )}
                </TableCell>
                <TableCell className="text-muted-foreground text-sm">
                  {formatShortDate(p.fecha_pedido)}
                </TableCell>
                <TableCell>
                  <Badge variant="outline" className={`rounded-full px-3 ${estadoStyle[p.estado]}`}>
                    {p.estado}
                  </Badge>
                </TableCell>
                <TableCell className="text-right font-semibold tabular-nums">
                  S/ {p.total_pagar.toFixed(2)}
                </TableCell>
                <TableCell className="text-right">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => navigate(`/facturas?pedido=${p.id}`)}
                  >
                    Ver Factura
                  </Button>
                </TableCell>
              </TableRow>
            ))}
            {lista.length === 0 && (
              <TableRow>
                <TableCell colSpan={7} className="text-center py-12 text-muted-foreground">
                  No hay pedidos registrados
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    </Card>
  );
};
