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
    #[Route('/client', name: 'client_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $clients = $em->getRepository(Client::class)->findAll();
        $data = array_map(fn($client) => [
            'id' => $client->getId(),
            'username' => $client->getUsername(),
        ], $clients);
        return $this->json($data);
    }

    #[Route('/client/create', name: 'client_create', methods: ['POST'])]
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
    public function show(Client $client): Response
    {
        return $this->json([
            'id' => $client->getId(),
            'username' => $client->getUsername(),
        ]);
    }

    #[Route('/client/{id}/edit', name: 'client_edit', methods: ['PUT'])]
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
    public function delete(Client $client, EntityManagerInterface $em): Response
    {
        $em->remove($client);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
