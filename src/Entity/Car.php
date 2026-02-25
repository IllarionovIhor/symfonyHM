<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\Table(name: 'car')]
#[UniqueEntity(fields: ['plateNumber'])]
#[Groups(['show_car'])]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['show_car']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write_car']],
            normalizationContext: ['groups' => ['show_car']]
        ),
        new Get(
            normalizationContext: ['groups' => ['show_car']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write_car']],
            normalizationContext: ['groups' => ['show_car']]
        ),
        new Delete()
    ]
)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    #[Groups(['show_car'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['show_car','write_car'])]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: FuelUsageType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_car','write_car'])]
    private ?FuelUsageType $fuelUsageType = null;

    #[ORM\ManyToOne(targetEntity: Tier::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_car','write_car'])]
    private ?Tier $tier = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['show_car','write_car'])]
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