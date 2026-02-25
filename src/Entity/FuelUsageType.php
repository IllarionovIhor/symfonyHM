<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FuelUsageTypeRepository;
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

#[ORM\Entity(repositoryClass: FuelUsageTypeRepository::class)]
#[ORM\Table(name: 'fuel_usage_type')]
#[Groups(['show_fuel_usage_type'])]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['show_fuel_usage_type']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write_fuel_usage_type']],
            normalizationContext: ['groups' => ['show_fuel_usage_type']]
        ),
        new Get(
            normalizationContext: ['groups' => ['show_fuel_usage_type']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write_fuel_usage_type']],
            normalizationContext: ['groups' => ['show_fuel_usage_type']]
        ),
        new Delete()
    ]
)]
class FuelUsageType
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    #[Groups(['show_fuel_usage_type'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['show_fuel_usage_type','write_fuel_usage_type'])]
    private ?string $name = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Groups(['show_fuel_usage_type','write_fuel_usage_type'])]
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
