<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductChangedEvent extends Event
{
    public const string CREATED = 'product.created';
    public const string UPDATED = 'product.updated';

    public function __construct(
        private readonly Product $product
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
