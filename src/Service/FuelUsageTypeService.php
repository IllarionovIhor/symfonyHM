<?php
namespace App\Service;

use App\Entity\FuelUsageType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class FuelUsageTypeService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createFuelUsageType(string $name, int $co2PerKm): FuelUsageType
    {
        $fuelUsageType = $this->createFuelUsageTypeObject($name, $co2PerKm);
        $this->validator->validateRequestDataByConstraints($fuelUsageType);
        $this->entityManager->persist($fuelUsageType);
        return $fuelUsageType;
    }

    private function createFuelUsageTypeObject(string $name, int $co2PerKm): FuelUsageType
    {
        $fuelUsageType = new FuelUsageType();
        $fuelUsageType->setName($name);
        $fuelUsageType->setCo2PerKm($co2PerKm);
        return $fuelUsageType;
    }

    public function updateFuelUsageType(FuelUsageType $fuelUsageType, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($fuelUsageType, $method)) {
                continue;
            }
            $fuelUsageType->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($fuelUsageType);
    }
}
