<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tier;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\TierService;

final class TierController extends AbstractController
{
    #[Route('/tier', name: 'tier_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $tiers = $em->getRepository(Tier::class)->findAll();
        $data = array_map(fn($tier) => [
            'id' => $tier->getId(),
            'name' => $tier->getName(),
            'priceModifier' => $tier->getPriceModifier(),
        ], $tiers);
        return $this->json($data);
    }

    #[Route('/tier/create', name: 'tier_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, TierService $tierService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'priceModifier']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $tier = $tierService->createTier($data['name'], (float)$data['priceModifier']);
        $em->flush();
        return $this->json([
            'id' => $tier->getId(),
            'name' => $tier->getName(),
            'priceModifier' => $tier->getPriceModifier(),
        ], 201);
    }

    #[Route('/tier/{id}', name: 'tier_show', methods: ['GET'])]
    public function show(Tier $tier): Response
    {
        return $this->json([
            'id' => $tier->getId(),
            'name' => $tier->getName(),
            'priceModifier' => $tier->getPriceModifier(),
        ]);
    }

    #[Route('/tier/{id}/edit', name: 'tier_edit', methods: ['PUT'])]
    public function edit(Request $request, Tier $tier, EntityManagerInterface $em, RequestValidatorService $validator, TierService $tierService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'priceModifier']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $tierService->updateTier($tier, $data);
        $em->flush();
        return $this->json([
            'id' => $tier->getId(),
            'name' => $tier->getName(),
            'priceModifier' => $tier->getPriceModifier(),
        ]);
    }

    #[Route('/tier/{id}/delete', name: 'tier_delete', methods: ['DELETE'])]
    public function delete(Tier $tier, EntityManagerInterface $em): Response
    {
        $em->remove($tier);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
