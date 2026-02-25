<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Delete()
    ]
)]
/**
 * @ORM\Entity
 */
class DriverCar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private ?Driver $driver = null;

    #[ORM\ManyToOne(targetEntity: Car::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[NotNull]
    private ?Car $car = null;

    #[ORM\Column(type: 'datetime')]
    #[NotNull]
    private ?\DateTimeInterface $timeStart = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $timeEnd = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;
        return $this;
    }

    public function getTimeStart(): ?\DateTimeInterface
    {
        return $this->timeStart;
    }

    public function setTimeStart(\DateTimeInterface $timeStart): self
    {
        $this->timeStart = $timeStart;
        return $this;
    }

    public function getTimeEnd(): ?\DateTimeInterface
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(\DateTimeInterface $timeEnd): self
    {
        $this->timeEnd = $timeEnd;
        return $this;
    }
}
