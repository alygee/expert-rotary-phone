import { Card, CardContent } from "@/components/ui/card";
import offerImage from "@/assets/offer.png";

export function OfferCard() {
  return (
    <Card className="w-full md:w-1/2 bg-morning-light">
      <CardContent className="flex items-center justify-center w-full h-full">
        <img src={offerImage} alt="Offer" />
      </CardContent>
    </Card>
  );
}

