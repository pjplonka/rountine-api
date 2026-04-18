<?php

namespace App\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateIngredientRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        #[OA\Property(description: 'New weight of the product in grams', example: 150)]
        public int $weight,
    ) {
    }
}
