<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Client;
use App\Service\RequestValidatorService;
use App\Service\ClientService;

final class ClientController extends AbstractController
{
    #[Route('/api/client/register', name: 'client_register', methods: ['POST'])]
    public function register(Request $request, ClientService $clientService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }

        try {
            $client = $clientService->createClient($data['username'], $data['password']);
            $client->setRoles(['ROLE_CLIENT']);
            return $this->json([
                'id' => $client->getId(),
                'username' => $client->getUsername(),
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/client/login', name: 'client_login', methods: ['POST'])]
    public function login(): Response
    {
        // The security system will intercept this request and return a JWT token
    }

    #[Route('/client', name: 'client_index', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $result = $em->getRepository(Client::class)->getAllClientsByFilter($requestData, $itemsPerPage, $page);
        $data = [
            'clients' => array_map(fn($client) => [
                'id' => $client->getId(),
                'username' => $client->getUsername(),
            ], $result['clients']),
            'totalPageCount' => $result['totalPageCount'],
            'totalItems' => $result['totalItems']
        ];
        return $this->json($data);
    }

    #[Route('/client/create', name: 'client_create', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function create(Request $request, EntityManagerInterface $em, ClientService $clientService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $client = $clientService->createClient($data['username'], $data['password']);
        $em->flush();
        return $this->json([
            'id' => $client->getId(),
            'username' => $client->getUsername(),
        ], 201);
    }

    #[Route('/client/{id}', name: 'client_show', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function show(Client $client): Response
    {
        return $this->json([
            'id' => $client->getId(),
            'username' => $client->getUsername(),
        ]);
    }

    #[Route('/client/{id}/edit', name: 'client_edit', methods: ['PUT'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function edit(Request $request, Client $client, EntityManagerInterface $em, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['username', 'password']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $client->setUsername($data['username']);
        $client->setPassword($data['password']);
        $em->flush();
        return $this->json([
            'id' => $client->getId(),
            'username' => $client->getUsername(),
        ]);
    }

    #[Route('/client/{id}/delete', name: 'client_delete', methods: ['DELETE'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function delete(Client $client, EntityManagerInterface $em): Response
    {
        $em->remove($client);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
