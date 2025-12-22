import { Button } from "@/components/ui/button";
import { ArrowBackIcon } from "../icons/ArrowBackIcon";
import { Typography } from "../ui/typography";
import { CloseIcon } from "../icons/CloseIcon";
import type { Step3Mode } from "@/types/form";

interface StepNavigationProps {
  currentStep: number;
  step3Mode: Step3Mode;
  isSubmitted?: boolean;
  onBack: () => void;
}

export function StepNavigation({ currentStep, step3Mode, isSubmitted, onBack }: StepNavigationProps) {
  const handleBackClick = () => {
    onBack();
  };

  // Скрываем кнопку "Назад" на первом шаге или если форма OrderForm успешно отправлена
  const shouldHideBackButton = currentStep === 1 || (isSubmitted && step3Mode === "order");

  return (
    <div className={`w-full max-w-6xl mx-auto flex ${shouldHideBackButton ? 'justify-end' : 'justify-between'}`}>
      {!shouldHideBackButton && (
        <Button 
          variant="text-secondary" 
          size="mediumSquare"
          onClick={handleBackClick}
        >
          <ArrowBackIcon />
          <Typography className="text-primary">Назад</Typography>
        </Button>
      )}
      <Button variant="text-secondary" size="mediumSquare">
        <CloseIcon />
      </Button>
    </div>
  );
}
