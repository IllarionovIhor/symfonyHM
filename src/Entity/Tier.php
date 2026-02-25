<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Tier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotNull]
    #[NotBlank]
    #[Length(min: 2, max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'float')]
    #[NotNull]
    #[PositiveOrZero]
    private ?float $priceModifier = null;

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

    public function getPriceModifier(): ?float
    {
        return $this->priceModifier;
    }

    public function setPriceModifier(float $priceModifier): self
    {
        $this->priceModifier = $priceModifier;
        return $this;
    }
}
