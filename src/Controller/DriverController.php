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
    public function create(Request $request, EntityManagerInterface $em, EntityCreationService $creationService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password', 'licenseNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $driver = $creationService->createDriver($data['username'], $data['password'], $data['licenseNumber']);
        $em->persist($driver);
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
    public function edit(Request $request, Driver $driver, EntityManagerInterface $em, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password', 'licenseNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $driver->setUsername($data['username']);
        $driver->setPassword($data['password']);
        $driver->setLicenseNumber($data['licenseNumber']);
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
