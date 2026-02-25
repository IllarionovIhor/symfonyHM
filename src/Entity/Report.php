<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReportRepository;
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

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\Table(name: 'report')]
#[Groups(['show_report'])]

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['show_report']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write_report']],
            normalizationContext: ['groups' => ['show_report']]
        ),
        new Get(
            normalizationContext: ['groups' => ['show_report']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write_report']],
            normalizationContext: ['groups' => ['show_report']]
        ),
        new Delete()
    ]
)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    #[Groups(['show_report'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ReportType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_report','write_report'])]
    private ?ReportType $reportType = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(['show_report','write_report'])]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['show_report','write_report'])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReportType(): ?ReportType
    {
        return $this->reportType;
    }

    public function setReportType(?ReportType $reportType): self
    {
        $this->reportType = $reportType;
        return $this;
    }

    public function getComment(): ?int
    {
        return $this->comment;
    }

    public function setComment(int $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }
}
