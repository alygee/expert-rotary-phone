import { Typography } from '@/components/ui/typography';
import { CirclePlusIcon, CircleMinusIcon } from '../icons';
import { formatCurrency, parsePriceString } from '@/utils/format';

interface InsurancePriceProps {
  label: string;
  price: string;
}

export function InsurancePrice({ label, price }: InsurancePriceProps) {
  const hasData = price && price.trim() !== '' && price !== '0';

  const formattedPrice = hasData
    ? formatCurrency(parsePriceString(price))
    : null;

  return (
    <div className="flex flex-row md:flex-col">
      <div className="flex gap-2 items-center">
        {hasData ? <CirclePlusIcon size={17} /> : <CircleMinusIcon size={17} />}
        <Typography variant="body1" className="text-text-secondary">
          {label}
        </Typography>
      </div>
      <div className="flex-1 text-right md:text-left">
        {formattedPrice && (
          <Typography variant="subtitle2">{formattedPrice}</Typography>
        )}
      </div>
    </div>
  );
}
