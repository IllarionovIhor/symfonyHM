<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DriverCar;
use App\Entity\Driver;
use App\Entity\Car;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\DriverCarService;

final class DriverCarController extends AbstractController
{
    #[Route('/driver/car', name: 'driver_car_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $filters = [];
        if ($request->query->has('driverId')) {
            $filters['driverId'] = $request->query->get('driverId');
        }
        if ($request->query->has('carId')) {
            $filters['carId'] = $request->query->get('carId');
        }
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        $result = $em->getRepository(DriverCar::class)->getAllDriverCarsByFilter($filters, $page, $limit);
        $data = array_map(fn($dc) => [
            'id' => $dc->getId(),
            'driver' => $dc->getDriver()?->getId(),
            'car' => $dc->getCar()?->getId(),
            'timeStart' => $dc->getTimeStart()?->format('c'),
            'timeEnd' => $dc->getTimeEnd()?->format('c'),
        ], $result['data']);
        return $this->json([
            'data' => $data,
            'total' => $result['total'],
            'page' => $result['page'],
            'limit' => $result['limit'],
        ]);
    }

    #[Route('/driver/car/create', name: 'driver_car_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, DriverCarService $driverCarService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['driver_id', 'car_id', 'timeStart', 'timeEnd']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $driver = $em->getRepository(Driver::class)->find($data['driver_id']);
        $car = $em->getRepository(Car::class)->find($data['car_id']);
        if (!$driver || !$car) {
            return $this->json(['errors' => 'Driver or Car not found'], 404);
        }
        $driverCar = $driverCarService->createDriverCar($driver, $car, new \DateTime($data['timeStart']), new \DateTime($data['timeEnd']));
        $em->flush();
        return $this->json([
            'id' => $driverCar->getId(),
            'driver' => $driverCar->getDriver()?->getId(),
            'car' => $driverCar->getCar()?->getId(),
            'timeStart' => $driverCar->getTimeStart()?->format('c'),
            'timeEnd' => $driverCar->getTimeEnd()?->format('c'),
        ], 201);
    }

    #[Route('/driver/car/{id}', name: 'driver_car_show', methods: ['GET'])]
    public function show(DriverCar $driverCar): Response
    {
        return $this->json([
            'id' => $driverCar->getId(),
            'driver' => $driverCar->getDriver()?->getId(),
            'car' => $driverCar->getCar()?->getId(),
            'timeStart' => $driverCar->getTimeStart()?->format('c'),
            'timeEnd' => $driverCar->getTimeEnd()?->format('c'),
        ]);
    }

    #[Route('/driver/car/{id}/edit', name: 'driver_car_edit', methods: ['PUT'])]
    public function edit(Request $request, DriverCar $driverCar, EntityManagerInterface $em, RequestValidatorService $validator, DriverCarService $driverCarService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['timeStart', 'timeEnd']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $driverCarService->updateDriverCar($driverCar, [
            'timeStart' => new \DateTime($data['timeStart']),
            'timeEnd' => new \DateTime($data['timeEnd'])
        ]);
        $em->flush();
        return $this->json([
            'id' => $driverCar->getId(),
            'driver' => $driverCar->getDriver()?->getId(),
            'car' => $driverCar->getCar()?->getId(),
            'timeStart' => $driverCar->getTimeStart()?->format('c'),
            'timeEnd' => $driverCar->getTimeEnd()?->format('c'),
        ]);
    }

    #[Route('/driver/car/{id}/delete', name: 'driver_car_delete', methods: ['DELETE'])]
    public function delete(DriverCar $driverCar, EntityManagerInterface $em): Response
    {
        $em->remove($driverCar);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
