<?php
namespace App\Service;

use App\Entity\Ride;
use App\Entity\Review;
use App\Entity\Car;
use App\Entity\Driver;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class RideService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createRide(string $destination, string $from, ?Review $review, Car $car, Driver $driver, int $totalCost, string $status): Ride
    {
        $ride = $this->createRideObject($destination, $from, $review, $car, $driver, $totalCost, $status);
        $this->validator->validateRequestDataByConstraints($ride);
        $this->entityManager->persist($ride);
        return $ride;
    }

    private function createRideObject(string $destination, string $from, ?Review $review, Car $car, Driver $driver, int $totalCost, string $status): Ride
    {
        $ride = new Ride();
        $ride->setDestination($destination);
        $ride->setFrom($from);
        $ride->setReview($review);
        $ride->setCar($car);
        $ride->setDriver($driver);
        $ride->setTotalCost($totalCost);
        $ride->setStatus($status);
        return $ride;
    }

    public function updateRide(Ride $ride, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($ride, $method)) {
                continue;
            }
            $ride->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($ride);
    }
}
