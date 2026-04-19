import { NavLink, useLocation } from "react-router-dom";
import { LayoutDashboard, ShoppingCart, Package, BarChart3, FileText, Settings, Boxes } from "lucide-react";
import { cn } from "@/lib/utils";

const items = [
  { to: "/", label: "Dashboard", icon: LayoutDashboard },
  { to: "/pedidos", label: "Pedidos", icon: ShoppingCart },
  { to: "/productos", label: "Productos", icon: Package },
  { to: "/facturas", label: "Facturación", icon: FileText },
  { to: "/reportes", label: "Reportes", icon: BarChart3 },
];

export const Sidebar = () => {
  const { pathname } = useLocation();
  return (
    <aside className="hidden lg:flex w-64 shrink-0 flex-col border-r border-border bg-card">
      <div className="flex items-center gap-2 px-6 h-16 border-b border-border">
        <div className="h-9 w-9 rounded-lg bg-primary flex items-center justify-center">
          <Boxes className="h-5 w-5 text-primary-foreground" />
        </div>
        <div>
          <div className="text-base font-semibold tracking-tight text-foreground">TechSolutions</div>
          <div className="text-[11px] text-muted-foreground -mt-0.5">Retail Management</div>
        </div>
      </div>
      <nav className="flex-1 p-3 space-y-1">
        {items.map(({ to, label, icon: Icon }) => {
          const active = pathname === to;
          return (
            <NavLink
              key={to}
              to={to}
              className={cn(
                "flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
                active
                  ? "bg-primary-soft text-primary"
                  : "text-muted-foreground hover:text-foreground hover:bg-muted"
              )}
              style={active ? { backgroundColor: "hsl(var(--primary-soft))" } : undefined}
            >
              <Icon className="h-4 w-4" />
              {label}
            </NavLink>
          );
        })}
      </nav>
      <div className="p-3 border-t border-border">
        <NavLink
          to="/settings"
          className="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-muted"
        >
          <Settings className="h-4 w-4" />
          Settings
        </NavLink>
      </div>
    </aside>
  );
};
