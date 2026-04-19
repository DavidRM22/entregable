import { Card } from "@/components/ui/card";
import type { LucideIcon } from "lucide-react";

interface Props {
  label: string;
  value: string;
  icon: LucideIcon;
  trend?: string;
  tone?: "primary" | "info" | "warning" | "success";
}

const toneMap = {
  primary: { bg: "bg-primary-soft", text: "text-primary" },
  info: { bg: "bg-info-soft", text: "text-info" },
  warning: { bg: "bg-warning-soft", text: "text-warning" },
  success: { bg: "bg-success-soft", text: "text-success" },
};

export const StatCard = ({ label, value, icon: Icon, trend, tone = "primary" }: Props) => {
  const t = toneMap[tone];
  return (
    <Card className="p-5 shadow-card border-border/60">
      <div className="flex items-start gap-4">
        <div
          className="h-11 w-11 rounded-lg flex items-center justify-center shrink-0"
          style={{
            backgroundColor:
              tone === "primary" ? "hsl(var(--primary-soft))" :
              tone === "info" ? "hsl(var(--info-soft))" :
              tone === "warning" ? "hsl(var(--warning-soft))" :
              "hsl(var(--success-soft))",
          }}
        >
          <Icon className={`h-5 w-5 ${t.text}`} />
        </div>
        <div className="min-w-0 flex-1">
          <div className="text-sm text-muted-foreground">{label}</div>
          <div className="text-2xl font-bold tracking-tight mt-1 text-foreground">
            {value}
            {trend && <span className="text-xs font-medium text-success ml-2">{trend}</span>}
          </div>
        </div>
      </div>
    </Card>
  );
};
