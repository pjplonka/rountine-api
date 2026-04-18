<?php

namespace App\Ingredient;

use App\Entity\Ingredient;

readonly class IngredientMacrosCalculator
{
    public function calculate(Ingredient $ingredient): void
    {
        $product = $ingredient->getProduct();
        if (!$product) {
            return;
        }

        $weight = $ingredient->getWeight();
        
        $ingredient->setProtein($this->calculateValue($product->getProtein(), $weight));
        $ingredient->setFat($this->calculateValue($product->getFat(), $weight));
        $ingredient->setCarbs($this->calculateValue($product->getCarbs(), $weight));
        $ingredient->setSugar($this->calculateValue($product->getSugar(), $weight));
    }

    private function calculateValue(?int $baseValue, int $weight): ?int
    {
        if (null === $baseValue) {
            return null;
        }

        return (int) round(($baseValue * $weight) / 100);
    }
}
