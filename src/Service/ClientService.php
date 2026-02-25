<?php
namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class ClientService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createClient(string $username, string $password): Client
    {
        $client = $this->createClientObject($username, $password);
        $this->validator->validateRequestDataByConstraints($client);
        $this->entityManager->persist($client);
        return $client;
    }

    private function createClientObject(string $username, string $password): Client
    {
        $client = new Client();
        $client->setUsername($username);
        $client->setPassword($password);
        return $client;
    }

    public function updateClient(Client $client, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($client, $method)) {
                continue;
            }
            $client->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($client);
    }
}
