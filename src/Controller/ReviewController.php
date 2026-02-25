<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Review;
use App\Entity\Client;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\ReviewService;

final class ReviewController extends AbstractController
{
    #[Route('/review', name: 'review_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $result = $em->getRepository(Review::class)->getAllReviewsByFilter($requestData, $itemsPerPage, $page);
        $data = [
            'reviews' => array_map(fn($review) => [
                'id' => $review->getId(),
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
                'client' => $review->getClient()?->getId(),
            ], $result['reviews']),
            'totalPageCount' => $result['totalPageCount'],
            'totalItems' => $result['totalItems']
        ];
        return $this->json($data);
    }

    #[Route('/review/create', name: 'review_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ReviewService $reviewService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['rating', 'comment', 'client_id']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $client = $em->getRepository(Client::class)->find($data['client_id']);
        if (!$client) {
            return $this->json(['errors' => 'Client not found'], 404);
        }
        $review = $reviewService->createReview((int)$data['rating'], (int)$data['comment'], $client);
        $em->flush();
        return $this->json([
            'id' => $review->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'client' => $review->getClient()?->getId(),
        ], 201);
    }

    #[Route('/review/{id}', name: 'review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->json([
            'id' => $review->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'client' => $review->getClient()?->getId(),
        ]);
    }

    #[Route('/review/{id}/edit', name: 'review_edit', methods: ['PUT'])]
    public function edit(Request $request, Review $review, EntityManagerInterface $em, RequestValidatorService $validator, ReviewService $reviewService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['rating', 'comment']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $reviewService->updateReview($review, $data);
        $em->flush();
        return $this->json([
            'id' => $review->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'client' => $review->getClient()?->getId(),
        ]);
    }

    #[Route('/review/{id}/delete', name: 'review_delete', methods: ['DELETE'])]
    public function delete(Review $review, EntityManagerInterface $em): Response
    {
        $em->remove($review);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
