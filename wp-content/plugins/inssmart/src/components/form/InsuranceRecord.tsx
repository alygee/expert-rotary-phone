import { Typography } from '@/components/ui/typography';
import type { InsuranceRecord as InsuranceRecordType } from '@/types/api';
import { formatCurrency } from '@/utils/format';
import { InsurancePrice } from '@/components/form/InsurancePrice';
import { getInsurerLogo } from '@/utils/insurerLogos';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface InsuranceRecordProps {
  record: InsuranceRecordType;
  insurerName: string;
  city: string;
  onSelectOffer: (
    insurerName: string,
    city: string,
    record: InsuranceRecordType
  ) => void;
}

export function InsuranceRecord({
  record,
  insurerName,
  city,
  onSelectOffer,
}: InsuranceRecordProps) {
  const logoPath = getInsurerLogo(insurerName);

  return (
    <Card className="shadow-elevation-3">
      <CardContent className="p-3">
        <div className="flex justify-between items-start mb-2">
          <div className="flex gap-3 items-center">
            {logoPath && (
              <img
                src={logoPath}
                alt={`${insurerName} логотип`}
                className="w-11 h-11 md:w-24 md:h-24 object-contain rounded-lg"
              />
            )}
            <Typography variant="subtitle2" className="mb-1">
              {insurerName}
            </Typography>
          </div>
          <div className="flex flex-col text-right">
            <Typography variant="h5">
              {formatCurrency(record.total_price)}
            </Typography>
            <Typography variant="body2" className="text-text-secondary">
              в год за человека
            </Typography>
            <Button
              className="w-full text-white mt-2 hidden md:block text-center"
              variant="solid"
              size="small"
              onClick={() => onSelectOffer(insurerName, city, record)}
            >
              Оформить
            </Button>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-2 p-2 bg-background rounded-lg">
          <InsurancePrice
            label="Поликлиника"
            price={record.prices.polyclinic}
          />
          <InsurancePrice
            label="Стоматология"
            price={record.prices.dentistry}
          />
          <InsurancePrice
            label="Скорая помощь"
            price={record.prices.ambulance}
          />
          <InsurancePrice
            label="Госпитализация"
            price={record.prices.hospitalization}
          />
          <InsurancePrice
            label="Врач на дом"
            price={record.prices.doctor_home}
          />
        </div>
        <Button
          className="w-full text-white mt-4 md:hidden"
          variant="solid"
          size="small"
          onClick={() => onSelectOffer(insurerName, city, record)}
        >
          Оформить
        </Button>
      </CardContent>
    </Card>
  );
}
