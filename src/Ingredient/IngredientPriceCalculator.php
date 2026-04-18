<?php

namespace App\Ingredient;

use App\Entity\Ingredient;

readonly class IngredientPriceCalculator
{
    public function calculate(Ingredient $ingredient): void
    {
        $product = $ingredient->getProduct();
        $weight = $ingredient->getWeight();

        if (!$product || !$weight) {
            return;
        }

        if ($product->getPrice() === null) {
            return;
        }
#
        $totalPrice = (int) round((($product->getPrice() / 1000) * $weight));
        $ingredient->setPrice($totalPrice);
    }
}
