<?php
namespace App\Service;

use App\Entity\ReportType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class ReportTypeService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createReportType(string $name, string $description): ReportType
    {
        $reportType = $this->createReportTypeObject($name, $description);
        $this->validator->validateRequestDataByConstraints($reportType);
        $this->entityManager->persist($reportType);
        return $reportType;
    }

    private function createReportTypeObject(string $name, string $description): ReportType
    {
        $reportType = new ReportType();
        $reportType->setName($name);
        $reportType->setDescription($description);
        return $reportType;
    }

    public function updateReportType(ReportType $reportType, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($reportType, $method)) {
                continue;
            }
            $reportType->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($reportType);
    }
}
