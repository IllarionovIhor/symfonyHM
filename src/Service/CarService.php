<?php
namespace App\Service;

use App\Entity\Car;
use App\Entity\FuelUsageType;
use App\Entity\Tier;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class CarService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createCar(string $name, FuelUsageType $fuelUsageType, Tier $tier, string $plateNumber): Car
    {
        $car = $this->createCarObject($name, $fuelUsageType, $tier, $plateNumber);
        $this->validator->validateRequestDataByConstraints($car);
        $this->entityManager->persist($car);
        return $car;
    }

    private function createCarObject(string $name, FuelUsageType $fuelUsageType, Tier $tier, string $plateNumber): Car
    {
        $car = new Car();
        $car->setName($name);
        $car->setFuelUsageType($fuelUsageType);
        $car->setTier($tier);
        $car->setPlateNumber($plateNumber);
        return $car;
    }

    public function updateCar(Car $car, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($car, $method)) {
                continue;
            }
            $car->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($car);
    }
}
