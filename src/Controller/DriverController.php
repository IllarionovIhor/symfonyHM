<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Driver;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\DriverService;

final class DriverController extends AbstractController
{
    #[Route('/driver', name: 'driver_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $drivers = $em->getRepository(Driver::class)->findAll();
        $data = array_map(fn($driver) => [
            'id' => $driver->getId(),
            'username' => $driver->getUsername(),
            'licenseNumber' => $driver->getLicenseNumber(),
        ], $drivers);
        return $this->json($data);
    }

    #[Route('/driver/create', name: 'driver_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, DriverService $driverService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password', 'licenseNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $driver = $driverService->createDriver($data['username'], $data['password'], $data['licenseNumber']);
        $em->flush();
        return $this->json([
            'id' => $driver->getId(),
            'username' => $driver->getUsername(),
            'licenseNumber' => $driver->getLicenseNumber(),
        ], 201);
    }

    #[Route('/driver/{id}', name: 'driver_show', methods: ['GET'])]
    public function show(Driver $driver): Response
    {
        return $this->json([
            'id' => $driver->getId(),
            'username' => $driver->getUsername(),
            'licenseNumber' => $driver->getLicenseNumber(),
        ]);
    }

    #[Route('/driver/{id}/edit', name: 'driver_edit', methods: ['PUT'])]
    public function edit(Request $request, Driver $driver, EntityManagerInterface $em, RequestValidatorService $validator, DriverService $driverService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password', 'licenseNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $driverService->updateDriver($driver, $data);
        $em->flush();
        return $this->json([
            'id' => $driver->getId(),
            'username' => $driver->getUsername(),
            'licenseNumber' => $driver->getLicenseNumber(),
        ]);
    }

    #[Route('/driver/{id}/delete', name: 'driver_delete', methods: ['DELETE'])]
    public function delete(Driver $driver, EntityManagerInterface $em): Response
    {
        $em->remove($driver);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
