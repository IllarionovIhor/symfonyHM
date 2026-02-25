<?php
namespace App\Service;

use App\Entity\Driver;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DriverService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    public function createDriver(string $username, string $plainPassword, string $licenseNumber): Driver
    {
        $driver = $this->createDriverObject($username, $plainPassword, $licenseNumber);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $driver,
            $plainPassword
        );
        $driver->setPassword($hashedPassword);
        $this->validator->validateRequestDataByConstraints($driver);
        $this->entityManager->persist($driver);
        return $driver;
    }

    private function createDriverObject(string $username, string $password, string $licenseNumber): Driver
    {
        $driver = new Driver();
        $driver->setUsername($username);
        // Password will be hashed by the service
        $driver->setLicenseNumber($licenseNumber);
        return $driver;
    }

    public function updateDriver(Driver $driver, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if ($key === 'password') {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $driver,
                    $value
                );
                $driver->setPassword($hashedPassword);
                continue;
            }
            if (!method_exists($driver, $method)) {
                continue;
            }
            $driver->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($driver);
    }
}
