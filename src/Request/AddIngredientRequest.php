<?php

namespace App\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class AddIngredientRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[OA\Property(description: 'UUID of the meal', example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $mealUuid,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[OA\Property(description: 'UUID of the product', example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $productUuid,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        #[OA\Property(description: 'Weight of the product in grams', example: 100)]
        public int $weight,
    ) {
    }
}
