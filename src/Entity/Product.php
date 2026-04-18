<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read', 'shopping_list:read'])]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read', 'shopping_list:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read'])]
    private ?string $price = null;

    #[ORM\Column(nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read'])]
    private ?int $calories = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read'])]
    private ?int $protein = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read'])]
    private ?int $fat = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read'])]
    private ?int $carbs = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'product:read'])]
    private ?int $sugar = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isCompleted = false;

    #[ORM\ManyToOne(targetEntity: ShopCategory::class)]
    #[Serializer\Groups(['product:read', 'meal:read', 'ingredient:read'])]
    private ?ShopCategory $shopCategory = null;

    public function __construct()
    {
        $this->uuid = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    #[Serializer\Ignore]
    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    #[Serializer\Ignore]
    public function updateCompletionStatus(): void
    {
        $this->isCompleted = null !== $this->price &&
            null !== $this->protein &&
            null !== $this->fat &&
            null !== $this->carbs &&
            null !== $this->sugar;
    }

    #[Serializer\Ignore]
    public function canCalculateCalories(): bool
    {
        return null !== $this->protein &&
            null !== $this->fat &&
            null !== $this->carbs;
    }

    public function getShopCategory(): ?ShopCategory
    {
        return $this->shopCategory;
    }

    public function setShopCategory(?ShopCategory $shopCategory): self
    {
        $this->shopCategory = $shopCategory;
        return $this;
    }
}
