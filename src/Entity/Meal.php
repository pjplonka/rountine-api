<?php

namespace App\Entity;

//use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
//#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Serializer\Groups(['meal:read', 'ingredient:read', 'shopping_list:read'])]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Groups(['meal:read', 'meal:write', 'ingredient:read', 'shopping_list:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Ingredient::class, mappedBy: 'meal', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Serializer\Groups(['meal:read', 'meal:write'])]
    private Collection $ingredients;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->uuid = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
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

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setMeal($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getMeal() === $this) {
                $ingredient->setMeal(null);
            }
        }

        return $this;
    }
}
