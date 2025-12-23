import { Card, CardContent } from '@/components/ui/card';
import { Typography } from '@/components/ui/typography';
import { Loader } from '@/components/ui/loader';

export function LoadingScreen() {
  return (
    <Card className="w-full max-w-5xl mx-auto">
      <CardContent className="py-4">
        <Typography className="text-left text-h4">
          Подождите, ведем расчёт
        </Typography>
        <Typography className="text-left text-body1 text-text-secondary mt-2">
          Сравните предложение от топовых страховых компаний и оформите полис
          без визита в офис
        </Typography>
        <div className="flex flex-col items-center justify-center space-y-6 mt-4 mb-3">
          <Loader size={32} />
        </div>
      </CardContent>
    </Card>
  );
}
