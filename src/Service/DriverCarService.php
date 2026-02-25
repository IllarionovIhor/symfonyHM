<?php
namespace App\Service;

use App\Entity\DriverCar;
use App\Entity\Driver;
use App\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class DriverCarService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createDriverCar(Driver $driver, Car $car, \DateTimeInterface $timeStart, \DateTimeInterface $timeEnd): DriverCar
    {
        $driverCar = $this->createDriverCarObject($driver, $car, $timeStart, $timeEnd);
        $this->validator->validateRequestDataByConstraints($driverCar);
        $this->entityManager->persist($driverCar);
        return $driverCar;
    }

    private function createDriverCarObject(Driver $driver, Car $car, \DateTimeInterface $timeStart, \DateTimeInterface $timeEnd): DriverCar
    {
        $driverCar = new DriverCar();
        $driverCar->setDriver($driver);
        $driverCar->setCar($car);
        $driverCar->setTimeStart($timeStart);
        $driverCar->setTimeEnd($timeEnd);
        return $driverCar;
    }

    public function updateDriverCar(DriverCar $driverCar, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($driverCar, $method)) {
                continue;
            }
            $driverCar->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($driverCar);
    }
}
