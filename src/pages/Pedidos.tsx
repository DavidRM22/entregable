import { OrderTable } from "@/components/orders/OrderTable";
import { QuickOrderForm } from "@/components/orders/QuickOrderForm";
import { useState } from "react";
import { InvoicePreview } from "@/components/invoices/InvoicePreview";

const Pedidos = () => {
  const [ultimo, setUltimo] = useState<number | null>(null);
  return (
    <div className="space-y-6 max-w-[1600px] mx-auto">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Gestión de Pedidos</h1>
        <p className="text-muted-foreground mt-1">Crea pedidos y visualiza su factura electrónica</p>
      </div>
      <div className="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div className="xl:col-span-2"><QuickOrderForm onCreated={setUltimo} /></div>
        <div className="xl:col-span-3"><InvoicePreview pedidoId={ultimo} /></div>
      </div>
      <OrderTable title="Todos los Pedidos" />
    </div>
  );
};
export default Pedidos;
