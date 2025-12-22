import { Typography } from '@/components/ui/typography';
import type {
  FilterResponse,
  InsuranceRecord,
  FallbackData,
} from '@/types/api';
import { CitySection } from './CitySection';
import { InsurerCard } from './InsurerCard';

interface InsuranceCardsProps {
  apiData: FilterResponse | null;
  onSelectOffer: (
    insurerName: string,
    city: string,
    record: InsuranceRecord
  ) => void;
}

function FallbackSection({
  fallbackData,
  onSelectOffer,
}: {
  fallbackData: FallbackData;
  onSelectOffer: (
    insurerName: string,
    city: string,
    record: InsuranceRecord
  ) => void;
}) {
  // Используем первый город из not_found_cities для идентификации, или "fallback" если список пуст
  const fallbackCity =
    fallbackData.not_found_cities && fallbackData.not_found_cities.length > 0
      ? fallbackData.not_found_cities[0]
      : 'fallback';

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <Typography className="text-4xl font-semibold tracking-wide">
          {fallbackData.title}
        </Typography>
        <Typography className="text-xl">{fallbackData.description}</Typography>
      </div>

      <div className="space-y-4">
        {fallbackData.insurers.map((insurer, insurerIndex) => (
          <InsurerCard
            key={`fallback-${insurer.name}-${insurerIndex}`}
            insurer={insurer}
            city={fallbackCity}
            onSelectOffer={onSelectOffer}
          />
        ))}
      </div>
    </div>
  );
}

export function InsuranceCards({
  apiData,
  onSelectOffer,
}: InsuranceCardsProps) {
  const hasResults = apiData?.results && apiData.results.length > 0;
  const hasFallback =
    apiData?.fallback !== null && apiData?.fallback !== undefined;

  if (!apiData || (!hasResults && !hasFallback)) {
    return (
      <div className="flex items-center justify-center h-full">
        <Typography variant="body1" className="text-text-secondary">
          Нет доступных предложений
        </Typography>
      </div>
    );
  }

  return (
    <div className="space-y-16">
      {hasResults && (
        <div className="space-y-4">
          <Typography className="text-h4">Предложения для вас</Typography>
          {apiData.results.map((cityResult, cityIndex) => (
            <CitySection
              key={`${cityResult.city}-${cityIndex}`}
              cityResult={cityResult}
              onSelectOffer={onSelectOffer}
            />
          ))}
        </div>
      )}

      {hasFallback && apiData.fallback && (
        <FallbackSection
          fallbackData={apiData.fallback}
          onSelectOffer={onSelectOffer}
        />
      )}
    </div>
  );
}
