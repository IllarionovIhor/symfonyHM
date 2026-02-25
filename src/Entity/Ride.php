<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RideRepository;
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

#[ORM\Entity(repositoryClass: RideRepository::class)]
#[ORM\Table(name: 'ride')]
#[Groups(['show_ride'])]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['show_ride']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write_ride']],
            normalizationContext: ['groups' => ['show_ride']]
        ),
        new Get(
            normalizationContext: ['groups' => ['show_ride']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write_ride']],
            normalizationContext: ['groups' => ['show_ride']]
        ),
        new Delete()
    ]
)]
class Ride
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    #[Groups(['show_ride'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['show_ride','write_ride'])]
    private ?string $destination = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['show_ride','write_ride'])]
    private ?string $from = null;

    #[ORM\OneToOne(targetEntity: Review::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['show_ride','write_ride'])]
    private ?Review $review = null;

    #[ORM\OneToOne(targetEntity: Report::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['show_ride','write_ride'])]
    private ?Report $report = null;
    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Car::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_ride','write_ride'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_ride','write_ride'])]
    private ?Driver $driver = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Groups(['show_ride','write_ride'])]
    private ?int $totalCost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;
        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;
        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function getTotalCost(): ?int
    {
        return $this->totalCost;
    }

    public function setTotalCost(int $totalCost): self
    {
        $this->totalCost = $totalCost;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    #[Groups(['show_ride','write_ride'])]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
