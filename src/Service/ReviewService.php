<?php
namespace App\Service;

use App\Entity\Review;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestValidatorService;

class ReviewService
{
    private EntityManagerInterface $entityManager;
    private RequestValidatorService $validator;

    public function __construct(EntityManagerInterface $entityManager, RequestValidatorService $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createReview(int $rating, int $comment, Client $client): Review
    {
        $review = $this->createReviewObject($rating, $comment, $client);
        $this->validator->validateRequestDataByConstraints($review);
        $this->entityManager->persist($review);
        return $review;
    }

    private function createReviewObject(int $rating, int $comment, Client $client): Review
    {
        $review = new Review();
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setClient($client);
        return $review;
    }

    public function updateReview(Review $review, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst(strtolower($key));
            if (!method_exists($review, $method)) {
                continue;
            }
            $review->$method($value);
        }
        $this->validator->validateRequestDataByConstraints($review);
    }
}
