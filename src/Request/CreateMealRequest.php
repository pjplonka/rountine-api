<?php

namespace App\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateMealRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 150)]
        #[OA\Property(description: 'Name of the meal', example: 'English breakfast')]
        public string $name,
    ) {
    }
}
