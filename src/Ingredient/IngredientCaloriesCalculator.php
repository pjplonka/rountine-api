<?php

namespace App\Ingredient;

use App\Entity\Ingredient;

readonly class IngredientCaloriesCalculator
{
    public function calculate(Ingredient $ingredient): void
    {
        $product = $ingredient->getProduct();
        $weight = $ingredient->getWeight();

        if (!$product || !$weight) {
            return;
        }

        if ($product->getCalories() === null) {
            return;
        }

        $totalCalories = (int) round(($product->getCalories() * $weight) / 100);

        $ingredient->setCalories($totalCalories);
    }
}
