<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;

#[ORM\Entity]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?string $uuid = null;

    #[ORM\ManyToOne(targetEntity: Meal::class, inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['ingredient:read'])]
    private ?Meal $meal = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?Product $product = null;

    #[ORM\Column]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $weight = null;

    #[ORM\Column(nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $calories = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $protein = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $fat = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $carbs = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $sugar = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read'])]
    private ?int $price = null;

    public function __construct()
    {
        $this->uuid = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
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

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(?int $calories): self
    {
        $this->calories = $calories;
        return $this;
    }

    public function getProtein(): ?int
    {
        return $this->protein;
    }

    public function setProtein(?int $protein): self
    {
        $this->protein = $protein;
        return $this;
    }

    public function getFat(): ?int
    {
        return $this->fat;
    }

    public function setFat(?int $fat): self
    {
        $this->fat = $fat;
        return $this;
    }

    public function getCarbs(): ?int
    {
        return $this->carbs;
    }

    public function setCarbs(?int $carbs): self
    {
        $this->carbs = $carbs;
        return $this;
    }

    public function getSugar(): ?int
    {
        return $this->sugar;
    }

    public function setSugar(?int $sugar): self
    {
        $this->sugar = $sugar;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;
        return $this;
    }
}
