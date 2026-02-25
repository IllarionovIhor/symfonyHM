<?php

namespace App\Action;

use App\Entity\Car;
use App\Entity\Driver;
use App\Entity\DriverCar;
use App\Service\RequestValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DriverCarSetEndDateAction
{
    public function __invoke(
        Request $request,
        RequestValidatorService $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $errors = $validator->validateNotBlankFields($data, ['driver_id', 'car_id']);
        if ($errors) {
            return new JsonResponse(['errors' => $errors], 400);
        }

        $driver = $entityManager->getRepository(Driver::class)->find($data['driver_id']);
        $car = $entityManager->getRepository(Car::class)->find($data['car_id']);

        if (!$driver || !$car) {
            return new JsonResponse(['errors' => 'Driver or Car not found'], 404);
        }

        /** @var DriverCar|null $driverCar */
        $driverCar = $entityManager->getRepository(DriverCar::class)->findOneBy([
            'driver' => $driver,
            'car' => $car,
        ]);

        if (!$driverCar) {
            return new JsonResponse(['errors' => 'DriverCar relation not found'], 404);
        }

        $driverCar->setTimeEnd(new \DateTimeImmutable());
        $entityManager->flush();

        return new JsonResponse([
            'id' => $driverCar->getId(),
            'driver_id' => $driver->getId(),
            'car_id' => $car->getId(),
            'timeStart' => $driverCar->getTimeStart()?->format(\DateTimeInterface::ATOM),
            'timeEnd' => $driverCar->getTimeEnd()?->format(\DateTimeInterface::ATOM),
        ]);
    }
}

