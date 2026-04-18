<?php

namespace App\EventSubscriber;

use App\Event\IngredientChangedEvent;
use App\Ingredient\IngredientCaloriesCalculator;
use App\Ingredient\IngredientMacrosCalculator;
use App\Ingredient\IngredientPriceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class IngredientChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private IngredientCaloriesCalculator $caloriesCalculator,
        private IngredientPriceCalculator    $priceCalculator,
        private IngredientMacrosCalculator   $macrosCalculator,
        private EntityManagerInterface       $entityManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IngredientChangedEvent::CREATED => 'onIngredientChanged',
            IngredientChangedEvent::UPDATED => 'onIngredientChanged',
        ];
    }

    public function onIngredientChanged(IngredientChangedEvent $event): void
    {
        $ingredient = $event->getIngredient();

        $this->caloriesCalculator->calculate($ingredient);
        $this->priceCalculator->calculate($ingredient);
        $this->macrosCalculator->calculate($ingredient);

        $this->entityManager->flush();
    }
}
