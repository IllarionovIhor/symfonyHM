<?php
namespace App\Service;

use App\Entity\Report;
use App\Entity\ReportType;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class ReportService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createReport(ReportType $reportType, int $comment, Client $client): Report
    {
        $report = $this->createReportObject($reportType, $comment, $client);
        $this->validator->validateRequestDataByConstraints($report);
        $this->entityManager->persist($report);
        return $report;
    }

    private function createReportObject(ReportType $reportType, int $comment, Client $client): Report
    {
        $report = new Report();
        $report->setReportType($reportType);
        $report->setComment($comment);
        $report->setClient($client);
        return $report;
    }

    public function updateReport(Report $report, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($report, $method)) {
                continue;
            }
            $report->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($report);
    }
}
