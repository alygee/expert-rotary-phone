import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Typography } from "@/components/ui/typography";

interface CallbackCardProps {
  onCallback?: () => void;
}

export function CallbackCard({ onCallback }: CallbackCardProps) {
  return (
    <Card className="shadow-elevation-2">
      <CardContent className="p-4 space-y-5">
        <div className="space-y-2">
          <Typography variant="h6">Оставьте свои контакты</Typography>
          <Typography variant="body2" className="text-text-secondary">
            Не нашли, что хотели? Мы перезвоним вам
          </Typography>
        </div>
        <div className="space-y-4">
          <div className="text-center md:text-right">
            <Button
              className="text-white"
              variant="solid-secondary"
              size="large"
              onClick={onCallback}
            >
              Заказать обратный звонок
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
