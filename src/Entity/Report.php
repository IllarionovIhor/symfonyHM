<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ReportType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReportType $reportType = null;

    #[ORM\Column(type: 'integer')]
    private ?int $comment = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
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
