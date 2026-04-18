<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class ShoppingList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Serializer\Groups(['shopping_list:read'])]
    private ?string $uuid = null;

    #[ORM\ManyToOne(targetEntity: Meal::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Serializer\Groups(['shopping_list:read'])]
    private ?Meal $meal = null;

    #[ORM\Column(nullable: true)]
    #[Serializer\Groups(['shopping_list:read'])]
    private ?int $servings = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Serializer\Groups(['shopping_list:read'])]
    private ?Product $product = null;

    #[ORM\Column(nullable: true)]
    #[Serializer\Groups(['shopping_list:read'])]
    private ?int $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Serializer\Groups(['shopping_list:read'])]
    private ?string $customName = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): self
    {
        $this->meal = $meal;
        return $this;
    }

    public function getServings(): ?int
    {
        return $this->servings;
    }

    public function setServings(?int $servings): self
    {
        $this->servings = $servings;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getCustomName(): ?string
    {
        return $this->customName;
    }

    public function setCustomName(?string $customName): self
    {
        $this->customName = $customName;
        return $this;
    }
}
