<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class ShopCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Serializer\Groups(['shop_category:read'])]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Groups(['shop_category:read'])]
    private ?string $name = null;

    #[ORM\Column(name: '`order`')]
    #[Serializer\Groups(['shop_category:read'])]
    private ?int $order = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }
}
