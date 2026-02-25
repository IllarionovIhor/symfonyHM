<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FuelUsageType
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

    #[ORM\Column(type: 'integer')]
    #[NotNull]
    #[PositiveOrZero]
    private ?int $co2PerKm = null;

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

    public function getCo2PerKm(): ?int
    {
        return $this->co2PerKm;
    }

    public function setCo2PerKm(int $co2PerKm): self
    {
        $this->co2PerKm = $co2PerKm;
        return $this;
    }
}
