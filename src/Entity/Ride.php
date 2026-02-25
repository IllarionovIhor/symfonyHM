<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Ride
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotNull]
    #[NotBlank]
    #[Length(min: 2, max: 255)]
    private ?string $destination = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[NotNull]
    #[NotBlank]
    #[Length(min: 2, max: 255)]
    private ?string $from = null;

    #[ORM\OneToOne(targetEntity: Review::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Review $review = null;

    #[ORM\ManyToOne(targetEntity: Car::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private ?Driver $driver = null;

    #[ORM\Column(type: 'integer')]
    #[NotNull]
    #[PositiveOrZero]
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
    #[NotNull]
    #[NotBlank]
    #[Length(min: 2, max: 50)]
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
