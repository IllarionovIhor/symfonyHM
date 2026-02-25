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
    #[Route('/api/driver/register', name: 'driver_register', methods: ['POST'])]
    public function register(Request $request, DriverService $driverService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password', 'licenseNumber']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }

        try {
            $driver = $driverService->createDriver($data['username'], $data['password'], $data['licenseNumber']);
            $driver->setRoles(['ROLE_DRIVER']);
            return $this->json([
                'id' => $driver->getId(),
                'username' => $driver->getUsername(),
                'licenseNumber' => $driver->getLicenseNumber(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/driver/login', name: 'driver_login', methods: ['POST'])]
    public function login(): Response
    {
        // The security system will intercept this request and return a JWT token
        throw new \LogicException('This method can be blank - it will be intercepted by the JWT authentication system.');
    }

    #[Route('/driver', name: 'driver_index', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $result = $em->getRepository(Driver::class)->getAllDriversByFilter($requestData, $itemsPerPage, $page);
        $data = [
            'drivers' => array_map(fn($driver) => [
                'id' => $driver->getId(),
                'username' => $driver->getUsername(),
                'licenseNumber' => $driver->getLicenseNumber(),
            ], $result['drivers']),
            'totalPageCount' => $result['totalPageCount'],
            'totalItems' => $result['totalItems']
        ];
        return $this->json($data);
    }

    #[Route('/driver/create', name: 'driver_create', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
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
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function show(Driver $driver): Response
    {
        return $this->json([
            'id' => $driver->getId(),
            'username' => $driver->getUsername(),
            'licenseNumber' => $driver->getLicenseNumber(),
        ]);
    }

    #[Route('/driver/{id}/edit', name: 'driver_edit', methods: ['PUT'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
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
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function delete(Driver $driver, EntityManagerInterface $em): Response
    {
        $em->remove($driver);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
