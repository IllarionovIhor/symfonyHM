<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DriverCarRepository;
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

#[ORM\Entity(repositoryClass: DriverCarRepository::class)]
#[ORM\Table(name: 'driver_car')]
#[Groups(['show_driver_car'])]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['show_driver_car']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write_driver_car']],
            normalizationContext: ['groups' => ['show_driver_car']]
        ),
        new Get(
            normalizationContext: ['groups' => ['show_driver_car']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write_driver_car']],
            normalizationContext: ['groups' => ['show_driver_car']]
        ),
        new Delete()
    ]
)]
class DriverCar
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    #[Groups(['show_driver_car'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_driver_car','write_driver_car'])]
    private ?Driver $driver = null;

    #[ORM\ManyToOne(targetEntity: Car::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_driver_car','write_driver_car'])]
    private ?Car $car = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    #[Groups(['show_driver_car','write_driver_car'])]
    private ?\DateTimeInterface $timeStart = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['show_driver_car','write_driver_car'])]
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
