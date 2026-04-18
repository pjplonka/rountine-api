<?php

namespace App\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ShopCategoryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[OA\Property(description: 'Name of the shop category', example: 'Vegetables')]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        #[OA\Property(description: 'Order position of the category', example: 1)]
        public int $order,
    ) {
    }
}
