<?php
namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    public function createClient(string $username, string $plainPassword): Client
    {
        $client = $this->createClientObject($username, $plainPassword);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $client,
            $plainPassword
        );
        $client->setPassword($hashedPassword);
        $this->validator->validateRequestDataByConstraints($client);
        $this->entityManager->persist($client);
        return $client;
    }

    private function createClientObject(string $username, string $password): Client
    {
        $client = new Client();
        $client->setUsername($username);
        // Password will be hashed by the service
        return $client;
    }

    public function updateClient(Client $client, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if ($key === 'password') {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $client,
                    $value
                );
                $client->setPassword($hashedPassword);
                continue;
            }
            if (!method_exists($client, $method)) {
                continue;
            }
            $client->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($client);
    }
}
