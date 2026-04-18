<?php

namespace App\EventSubscriber;

use App\Calories\CaloriesCalculator;
use App\Event\ProductChangedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ProductChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CaloriesCalculator     $caloriesCalculator,
        private EntityManagerInterface $entityManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductChangedEvent::CREATED => 'onProductChanged',
            ProductChangedEvent::UPDATED => 'onProductChanged',
        ];
    }

    public function onProductChanged(ProductChangedEvent $event): void
    {
        $product = $event->getProduct();

        if (!$product->canCalculateCalories()) {
            return;
        }

        $calories = $this->caloriesCalculator->calculate(
            $product->getProtein() ?? 0,
            $product->getCarbs() ?? 0,
            $product->getFat() ?? 0
        );

        $product->setCalories($calories);

        $this->entityManager->flush();
    }
}
