import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Typography } from "@/components/ui/typography";

interface SuccessCardProps {
  onClose?: () => void;
  onReset?: () => void;
}

export function SuccessCard({ onClose, onReset }: SuccessCardProps) {
  return (
    <Card className="shadow-elevation-2">
      <CardContent className="p-4 space-y-5">
        <div className="space-y-2">
          <Typography variant="h4">
            Спасибо! Заявка успешно отправлена
          </Typography>
          <Typography className="text-lg text-text-secondary">
            Наш менеджер свяжется с вами в течение 30 минут
          </Typography>
        </div>
        <div className="flex flex-col sm:flex-row gap-3 justify-end">
          <Button
            variant="contained"
            size="large"
            onClick={onClose}
            className="text-primary"
          >
            Закрыть
          </Button>
          <Button
            variant="solid"
            size="large"
            className="text-white"
            onClick={onReset}
          >
            Сделать новый расчёт
          </Button>
        </div>
      </CardContent>
    </Card>
  );
}
