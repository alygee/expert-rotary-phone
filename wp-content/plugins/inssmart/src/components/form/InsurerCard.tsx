import type { Insurer, InsuranceRecord as InsuranceRecordType } from "@/types/api";
import { InsuranceRecord } from "@/components/form/InsuranceRecord";

interface InsurerCardProps {
  insurer: Insurer;
  city: string;
  onSelectOffer: (insurerName: string, city: string, record: InsuranceRecordType) => void;
}

export function InsurerCard({ insurer, city, onSelectOffer }: InsurerCardProps) {
  return (
    <div className="space-y-3">
      {insurer.records.map((record, recordIndex) => (
        <InsuranceRecord
          key={recordIndex}
          record={record}
          insurerName={insurer.name}
          city={city}
          onSelectOffer={onSelectOffer}
        />
      ))}
    </div>
  );
}
