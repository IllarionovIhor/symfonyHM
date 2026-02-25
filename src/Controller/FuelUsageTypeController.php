<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\FuelUsageType;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;

final class FuelUsageTypeController extends AbstractController
{
    #[Route('/fuel/usage/type', name: 'fuel_usage_type_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $types = $em->getRepository(FuelUsageType::class)->findAll();
        $data = array_map(fn($type) => [
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ], $types);
        return $this->json($data);
    }

    #[Route('/fuel/usage/type/create', name: 'fuel_usage_type_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, EntityCreationService $creationService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'co2PerKm']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $type = $creationService->createFuelUsageType($data['name'], (int)$data['co2PerKm']);
        $em->persist($type);
        $em->flush();
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ], 201);
    }

    #[Route('/fuel/usage/type/{id}', name: 'fuel_usage_type_show', methods: ['GET'])]
    public function show(FuelUsageType $type): Response
    {
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ]);
    }

    #[Route('/fuel/usage/type/{id}/edit', name: 'fuel_usage_type_edit', methods: ['PUT'])]
    public function edit(Request $request, FuelUsageType $type, EntityManagerInterface $em, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'co2PerKm']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $type->setName($data['name']);
        $type->setCo2PerKm((int)$data['co2PerKm']);
        $em->flush();
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'co2PerKm' => $type->getCo2PerKm(),
        ]);
    }

    #[Route('/fuel/usage/type/{id}/delete', name: 'fuel_usage_type_delete', methods: ['DELETE'])]
    public function delete(FuelUsageType $type, EntityManagerInterface $em): Response
    {
        $em->remove($type);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
