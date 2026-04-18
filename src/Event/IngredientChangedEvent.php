<?php

namespace App\Event;

use App\Entity\Ingredient;
use Symfony\Contracts\EventDispatcher\Event;

class IngredientChangedEvent extends Event
{
    public const string CREATED = 'ingredient.created';
    public const string UPDATED = 'ingredient.updated';

    public function __construct(
        private readonly Ingredient $ingredient
    ) {
    }

    public function getIngredient(): Ingredient
    {
        return $this->ingredient;
    }
}
