import type { CityResult, InsuranceRecord } from "@/types/api";
import { InsurerCard } from "./InsurerCard";

interface CitySectionProps {
  cityResult: CityResult;
  onSelectOffer: (insurerName: string, city: string, record: InsuranceRecord) => void;
}

export function CitySection({ cityResult, onSelectOffer }: CitySectionProps) {
  return (
    <div className="space-y-4">
      <h6 className="font-semibold text-xl leading-[24px] text-text-secondary">
        {cityResult.city}
      </h6>

      <div className="space-y-4">
        {cityResult.insurers.map((insurer, insurerIndex) => (
          <InsurerCard
            key={`${insurer.name}-${insurerIndex}`}
            insurer={insurer}
            city={cityResult.city}
            onSelectOffer={onSelectOffer}
          />
        ))}
      </div>
    </div>
  );
}
