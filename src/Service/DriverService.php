<?php
namespace App\Service;

use App\Entity\Driver;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class DriverService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createDriver(string $username, string $password, string $licenseNumber): Driver
    {
        $driver = $this->createDriverObject($username, $password, $licenseNumber);
        $this->validator->validateRequestDataByConstraints($driver);
        $this->entityManager->persist($driver);
        return $driver;
    }

    private function createDriverObject(string $username, string $password, string $licenseNumber): Driver
    {
        $driver = new Driver();
        $driver->setUsername($username);
        $driver->setPassword($password);
        $driver->setLicenseNumber($licenseNumber);
        return $driver;
    }

    public function updateDriver(Driver $driver, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($driver, $method)) {
                continue;
            }
            $driver->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($driver);
    }
}
