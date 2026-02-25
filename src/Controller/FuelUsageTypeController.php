<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\FuelUsageType;
use App\Service\FuelUsageTypeService;
use App\Service\RequestValidatorService;

final class FuelUsageTypeController extends AbstractController
{
    #[Route('/fuel/usage/type', name: 'fuel_usage_type_index', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $filters = [];
        if ($request->query->has('name')) {
            $filters['name'] = $request->query->get('name');
        }
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        $result = $em->getRepository(FuelUsageType::class)->getAllFuelUsageTypesByFilter($filters, $page, $limit);
        $data = array_map(fn($type) => [
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ], $result['data']);
        return $this->json([
            'data' => $data,
            'total' => $result['total'],
            'page' => $result['page'],
            'limit' => $result['limit'],
        ]);
    }

    #[Route('/fuel/usage/type/create', name: 'fuel_usage_type_create', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $em, FuelUsageTypeService $fuelUsageTypeService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'co2PerKm']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $type = $fuelUsageTypeService->createFuelUsageType($data['name'], (int)$data['co2PerKm']);
        $em->flush();
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ], 201);
    }

    #[Route('/fuel/usage/type/{id}', name: 'fuel_usage_type_show', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(FuelUsageType $type): Response
    {
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ]);
    }

    #[Route('/fuel/usage/type/{id}/edit', name: 'fuel_usage_type_edit', methods: ['PUT'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, FuelUsageType $type, EntityManagerInterface $em, RequestValidatorService $validator, FuelUsageTypeService $fuelUsageTypeService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'co2PerKm']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $fuelUsageTypeService->updateFuelUsageType($type, $data);
        $em->flush();
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ]);
    }

    #[Route('/fuel/usage/type/{id}/delete', name: 'fuel_usage_type_delete', methods: ['DELETE'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(FuelUsageType $type, EntityManagerInterface $em): Response
    {
        $em->remove($type);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
