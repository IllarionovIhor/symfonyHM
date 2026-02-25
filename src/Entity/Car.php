<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Car
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

    #[ORM\ManyToOne(targetEntity: FuelUsageType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private ?FuelUsageType $fuelUsageType = null;

    #[ORM\ManyToOne(targetEntity: Tier::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private ?Tier $tier = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotNull]
    #[NotBlank]
    #[Length(min: 3, max: 255)]
    private ?string $plateNumber = null;

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

    public function getFuelUsageType(): ?FuelUsageType
    {
        return $this->fuelUsageType;
    }

    public function setFuelUsageType(?FuelUsageType $fuelUsageType): self
    {
        $this->fuelUsageType = $fuelUsageType;
        return $this;
    }

    public function getTier(): ?Tier
    {
        return $this->tier;
    }

    public function setTier(?Tier $tier): self
    {
        $this->tier = $tier;
        return $this;
    }

    public function getPlateNumber(): ?string
    {
        return $this->plateNumber;
    }

    public function setPlateNumber(string $plateNumber): self
    {
        $this->plateNumber = $plateNumber;
        return $this;
    }
}
