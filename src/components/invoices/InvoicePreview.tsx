import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Download } from "lucide-react";
import { useStore } from "@/store/useStore";
import { formatCurrency, formatDate } from "@/lib/format";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

interface Props {
  pedidoId?: number | null;
}

export const InvoicePreview = ({ pedidoId }: Props) => {
  const pedidos = useStore((s) => s.pedidos);
  const facturas = useStore((s) => s.facturas);
  const clientes = useStore((s) => s.clientes);
  const generarFactura = useStore((s) => s.generarFactura);

  const pedido = pedidoId ? pedidos.find((p) => p.id === pedidoId) : pedidos[0];
  if (!pedido) {
    return (
      <Card className="p-6 shadow-card border-border/60">
        <p className="text-sm text-muted-foreground text-center py-12">
          Crea un pedido para visualizar la factura electrónica
        </p>
      </Card>
    );
  }

  let factura = facturas.find((f) => f.pedido_id === pedido.id);
  if (!factura) factura = generarFactura(pedido.id) ?? undefined;
  const cliente = clientes.find((c) => c.id === pedido.cliente_id);

  const downloadPDF = () => {
    const doc = new jsPDF();
    doc.setFillColor(20, 130, 70);
    doc.rect(0, 0, 210, 28, "F");
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(20);
    doc.setFont("helvetica", "bold");
    doc.text("TechSolutions", 14, 18);
    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text("Retail Management — RUC 20512345678", 14, 24);

    doc.setTextColor(40, 40, 40);
    doc.setFontSize(14);
    doc.setFont("helvetica", "bold");
    doc.text("FACTURA ELECTRÓNICA", 140, 40);
    doc.setFontSize(11);
    doc.text(factura!.serie_correlativo, 140, 47);

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(`Cliente: ${cliente?.nombre_razon_social ?? ""}`, 14, 50);
    doc.text(`Doc: ${cliente?.documento_identidad ?? ""}`, 14, 56);
    doc.text(`Fecha: ${formatDate(factura!.fecha_emision)}`, 14, 62);
    doc.text(`Pedido: ${pedido.numero_pedido}`, 14, 68);

    autoTable(doc, {
      startY: 78,
      head: [["Descripción", "Cant.", "P. Unit.", "Subtotal"]],
      body: pedido.detalles.map((d) => [
        d.producto_nombre, String(d.cantidad),
        formatCurrency(d.precio_unitario_venta),
        formatCurrency(d.subtotal),
      ]),
      theme: "striped",
      headStyles: { fillColor: [20, 130, 70] },
    });

    const finalY = (doc as any).lastAutoTable.finalY + 10;
    doc.setFontSize(10);
    doc.text(`Subtotal: ${formatCurrency(pedido.total_neto)}`, 150, finalY);
    doc.text(`IGV (18%): ${formatCurrency(pedido.total_impuestos)}`, 150, finalY + 6);
    doc.setFont("helvetica", "bold");
    doc.text(`Total: ${formatCurrency(pedido.total_pagar)}`, 150, finalY + 14);

    doc.save(`${factura!.serie_correlativo}.pdf`);
  };

  return (
    <Card className="shadow-card border-border/60 overflow-hidden">
      <div className="px-6 py-4 border-b border-border flex items-center justify-between bg-card">
        <div>
          <h3 className="text-lg font-semibold tracking-tight">Factura Electrónica</h3>
          <p className="text-xs text-muted-foreground">SUNAT — {factura?.estado_sunat}</p>
        </div>
        <Button onClick={downloadPDF} className="bg-primary hover:bg-primary/90 text-primary-foreground">
          <Download className="h-4 w-4 mr-2" /> Descargar Factura
        </Button>
      </div>
      <div className="p-6 bg-secondary/40">
        <div className="bg-card rounded-lg shadow-elevated p-6 max-w-2xl mx-auto">
          <div className="flex justify-between items-start pb-4 border-b border-border">
            <div>
              <div className="inline-block bg-primary text-primary-foreground px-3 py-1 rounded font-bold tracking-wide">
                TechSolutions
              </div>
              <div className="text-xs text-muted-foreground mt-2">RUC 20512345678</div>
              <div className="text-xs text-muted-foreground">Av. Javier Prado 1234, Lima — Perú</div>
            </div>
            <div className="text-right">
              <div className="text-sm font-semibold">FACTURA ELECTRÓNICA</div>
              <div className="text-base font-mono mt-1">{factura?.serie_correlativo}</div>
              <div className="text-xs text-muted-foreground mt-1">{formatDate(factura?.fecha_emision ?? "")}</div>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4 py-4 text-sm">
            <div>
              <div className="text-xs text-muted-foreground uppercase tracking-wide">Cliente</div>
              <div className="font-medium">{cliente?.nombre_razon_social}</div>
              <div className="text-xs text-muted-foreground">Doc: {cliente?.documento_identidad}</div>
            </div>
            <div>
              <div className="text-xs text-muted-foreground uppercase tracking-wide">Pedido</div>
              <div className="font-medium font-mono">{pedido.numero_pedido}</div>
              <div className="text-xs text-muted-foreground">Estado: {pedido.estado}</div>
            </div>
          </div>

          <table className="w-full text-sm">
            <thead>
              <tr className="border-y border-border text-muted-foreground">
                <th className="py-2 text-left font-medium">Descripción</th>
                <th className="py-2 text-center font-medium w-16">Cant.</th>
                <th className="py-2 text-right font-medium w-24">P. Unit.</th>
                <th className="py-2 text-right font-medium w-28">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              {pedido.detalles.map((d) => (
                <tr key={d.id} className="border-b border-border">
                  <td className="py-3">{d.producto_nombre}</td>
                  <td className="py-3 text-center tabular-nums">{d.cantidad}</td>
                  <td className="py-3 text-right tabular-nums">{formatCurrency(d.precio_unitario_venta)}</td>
                  <td className="py-3 text-right tabular-nums font-medium">{formatCurrency(d.subtotal)}</td>
                </tr>
              ))}
            </tbody>
          </table>

          <div className="flex justify-end pt-4">
            <div className="w-64 space-y-1.5 text-sm">
              <div className="flex justify-between text-muted-foreground">
                <span>Subtotal</span><span className="tabular-nums">{formatCurrency(pedido.total_neto)}</span>
              </div>
              <div className="flex justify-between text-muted-foreground">
                <span>IGV (18%)</span><span className="tabular-nums">{formatCurrency(pedido.total_impuestos)}</span>
              </div>
              <div className="flex justify-between font-bold text-base pt-2 border-t border-border">
                <span>Total</span><span className="tabular-nums">{formatCurrency(pedido.total_pagar)}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Card>
  );
};
