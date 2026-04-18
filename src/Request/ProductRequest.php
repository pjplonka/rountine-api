<?php

namespace App\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[OA\Property(description: 'Name of the product', example: 'Chicken Breast')]
        public string $name,

        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        #[OA\Property(description: 'Price per 1kg', example: 500, nullable: true)]
        public ?int $price = null,

        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        #[OA\Property(description: 'Protein per 100g', example: 31, nullable: true)]
        public ?int $protein = null,

        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        #[OA\Property(description: 'Fat per 100g', example: 4, nullable: true)]
        public ?int $fat = null,

        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        #[OA\Property(description: 'Carbohydrates per 100g', example: 0, nullable: true)]
        public ?int $carbs = null,

        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        #[OA\Property(description: 'Sugar per 100g', example: 0, nullable: true)]
        public ?int $sugar = null,

        #[Assert\Type('string')]
        #[OA\Property(description: 'UUID of the shop category', example: '550e8400-e29b-41d4-a716-446655440000', nullable: true)]
        public ?string $shopCategoryUuid = null,

        #[OA\Property(description: 'UUID of the product', example: '550e8400-e29b-41d4-a716-446655440000', nullable: true)]
        public ?string $uuid = null,
    ) {
    }
}
