<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Car;
use App\Entity\FuelUsageType;
use App\Entity\Tier;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\CarService;

final class CarController extends AbstractController
{
    #[Route('/car', name: 'car_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $result = $em->getRepository(Car::class)->getAllCarsByFilter($requestData, $itemsPerPage, $page);
        $data = [
            'cars' => array_map(fn($car) => [
                'id' => $car->getId(),
                'name' => $car->getName(),
                'fuelUsageType' => $car->getFuelUsageType()?->getId(),
                'tier' => $car->getTier()?->getId(),
                'plateNumber' => $car->getPlateNumber(),
            ], $result['cars']),
            'totalPageCount' => $result['totalPageCount'],
            'totalItems' => $result['totalItems']
        ];
        return $this->json($data);
    }

    #[Route('/car/create', name: 'car_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, CarService $carService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'fuelUsageType_id', 'tier_id', 'plateNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $fuelUsageType = $em->getRepository(FuelUsageType::class)->find($data['fuelUsageType_id']);
        $tier = $em->getRepository(Tier::class)->find($data['tier_id']);
        if (!$fuelUsageType || !$tier) {
            return $this->json(['errors' => 'FuelUsageType or Tier not found'], 404);
        }
        $car = $carService->createCar($data['name'], $fuelUsageType, $tier, $data['plateNumber']);
        $em->flush();
        return $this->json([
            'id' => $car->getId(),
            'name' => $car->getName(),
            'fuelUsageType' => $car->getFuelUsageType()?->getId(),
            'tier' => $car->getTier()?->getId(),
            'plateNumber' => $car->getPlateNumber(),
        ], 201);
    }

    #[Route('/car/{id}', name: 'car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        return $this->json([
            'id' => $car->getId(),
            'name' => $car->getName(),
            'fuelUsageType' => $car->getFuelUsageType()?->getId(),
            'tier' => $car->getTier()?->getId(),
            'plateNumber' => $car->getPlateNumber(),
        ]);
    }

    #[Route('/car/{id}/edit', name: 'car_edit', methods: ['PUT'])]
    public function edit(Request $request, Car $car, EntityManagerInterface $em, RequestValidatorService $validator, CarService $carService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'plateNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $carService->updateCar($car, $data);
        $em->flush();
        return $this->json([
            'id' => $car->getId(),
            'name' => $car->getName(),
            'fuelUsageType' => $car->getFuelUsageType()?->getId(),
            'tier' => $car->getTier()?->getId(),
            'plateNumber' => $car->getPlateNumber(),
        ]);
    }

    #[Route('/car/{id}/delete', name: 'car_delete', methods: ['DELETE'])]
    public function delete(Car $car, EntityManagerInterface $em): Response
    {
        $em->remove($car);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
