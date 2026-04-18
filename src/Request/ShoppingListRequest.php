<?php

namespace App\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ShoppingListRequest
{
    public function __construct(
        #[Assert\Type('string')]
        #[OA\Property(description: 'UUID of the meal', example: '550e8400-e29b-41d4-a716-446655440000', nullable: true)]
        #[Assert\Expression(
            "((this.mealUuid != null and this.servings != null) and this.productUuid == null and this.customName == null) or " .
            "((this.productUuid != null and this.weight != null) and this.mealUuid == null and this.customName == null) or " .
            "(this.customName != null and this.mealUuid == null and this.productUuid == null)",
            message: "You must provide either (mealUuid and servings), (productUuid and weight), or customName."
        )]
        public ?string $mealUuid = null,

        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        #[OA\Property(description: 'Number of servings', example: 2, nullable: true)]
        public ?int $servings = null,

        #[Assert\Type('string')]
        #[OA\Property(description: 'UUID of the product', example: '550e8400-e29b-41d4-a716-446655440000', nullable: true)]
        public ?string $productUuid = null,

        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        #[OA\Property(description: 'Weight of the product in grams', example: 250, nullable: true)]
        public ?int $weight = null,

        #[Assert\Length(min: 1, max: 255)]
        #[OA\Property(description: 'Custom product name', example: 'Milk from the farm', nullable: true)]
        public ?string $customName = null,
    ) {
    }
}
