<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TierRepository;
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

#[ORM\Entity(repositoryClass: TierRepository::class)]
#[ORM\Table(name: 'tier')]
#[UniqueEntity(fields: ['name'])]
#[Groups(['show_tier'])]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['show_tier']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write_tier']],
            normalizationContext: ['groups' => ['show_tier']]
        ),
        new Get(
            normalizationContext: ['groups' => ['show_tier']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write_tier']],
            normalizationContext: ['groups' => ['show_tier']]
        ),
        new Delete()
    ]
)]
class Tier
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    #[Groups(['show_tier'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['show_tier','write_tier'])]
    private ?string $name = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Groups(['show_tier','write_tier'])]
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
