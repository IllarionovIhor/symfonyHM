<?php

namespace App\Action;

use App\Entity\Ride;
use App\Service\RequestValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RideCompleteAction
{
    public function __invoke(
        Request $request,
        RequestValidatorService $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $errors = $validator->validateNotBlankFields($data, ['ride_id']);
        if ($errors) {
            return new JsonResponse(['errors' => $errors], 400);
        }

        /** @var Ride|null $ride */
        $ride = $entityManager->getRepository(Ride::class)->find($data['ride_id']);

        if (!$ride) {
            return new JsonResponse(['errors' => 'Ride not found'], 404);
        }

        $ride->setStatus('completed');
        $entityManager->flush();

        return new JsonResponse([
            'id' => $ride->getId(),
            'status' => $ride->getStatus(),
        ]);
    }
}

