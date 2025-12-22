import { Card, CardContent } from "@/components/ui/card";
import { Typography } from "@/components/ui/typography";
import { formatCurrency } from "@/utils/format";

interface SelectedInsurerCardProps {
  insurerName: string;
  totalPrice: number;
  logoPath?: string | null;
}

export function SelectedInsurerCard({
  insurerName,
  totalPrice,
  logoPath,
}: SelectedInsurerCardProps) {
  return (
    <Card className="shadow-elevation-2">
      <CardContent className="p-4 flex justify-between">
        <div className="flex gap-3 items-center">
          {logoPath && (
            <img
              src={logoPath}
              alt={`${insurerName} логотип`}
              className="w-16 h-16 object-contain"
            />
          )}
          <Typography variant="subtitle1" className="mb-1">
            {insurerName}
          </Typography>
        </div>
        <div className="flex flex-col items-center">
          <Typography variant="h5">~ {formatCurrency(totalPrice)}</Typography>
          <Typography variant="caption">в год за человека</Typography>
        </div>
      </CardContent>
    </Card>
  );
}
