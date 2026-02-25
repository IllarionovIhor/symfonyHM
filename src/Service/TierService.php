<?php
namespace App\Service;

use App\Entity\Tier;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class TierService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createTier(string $name, float $priceModifier): Tier
    {
        $tier = $this->createTierObject($name, $priceModifier);
        $this->validator->validateRequestDataByConstraints($tier);
        $this->entityManager->persist($tier);
        return $tier;
    }

    private function createTierObject(string $name, float $priceModifier): Tier
    {
        $tier = new Tier();
        $tier->setName($name);
        $tier->setPriceModifier($priceModifier);
        return $tier;
    }

    public function updateTier(Tier $tier, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($tier, $method)) {
                continue;
            }
            $tier->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($tier);
    }
}
