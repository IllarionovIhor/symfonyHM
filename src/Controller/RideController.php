<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ride;
use App\Entity\Car;
use App\Entity\Driver;
use App\Entity\Review;
use App\Service\RideService;
use App\Service\RequestValidatorService;

final class RideController extends AbstractController
{

    #[Route('/ride/{id}/add-review', name: 'ride_add_review', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function addReview(Request $request, Ride $ride, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        if (empty($data['review_id'])) {
            return $this->json(['error' => 'review_id is required'], 400);
        }
        $review = $em->getRepository(Review::class)->find($data['review_id']);
        if (!$review) {
            return $this->json(['error' => 'Review not found'], 404);
        }
        $ride->setReview($review);
        $em->flush();
        return $this->json(['status' => 'review added', 'ride_id' => $ride->getId(), 'review_id' => $review->getId()]);
    }

    #[Route('/ride/{id}/add-report', name: 'ride_add_report', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function addReport(Request $request, Ride $ride, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        if (empty($data['report_id'])) {
            return $this->json(['error' => 'report_id is required'], 400);
        }
        $report = $em->getRepository(\App\Entity\Report::class)->find($data['report_id']);
        if (!$report) {
            return $this->json(['error' => 'Report not found'], 404);
        }
        $ride->setReport($report);
        $em->flush();
        return $this->json(['status' => 'report added', 'ride_id' => $ride->getId(), 'report_id' => $report->getId()]);
    }
    #[Route('/ride', name: 'ride_index', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $result = $em->getRepository(Ride::class)->getAllRidesByFilter($requestData, $itemsPerPage, $page);
        $data = [
            'rides' => array_map(fn($ride) => [
                'id' => $ride->getId(),
                'destination' => $ride->getDestination(),
                'from' => $ride->getFrom(),
                'review' => $ride->getReview()?->getId(),
                'car' => $ride->getCar()?->getId(),
                'driver' => $ride->getDriver()?->getId(),
                'totalCost' => $ride->getTotalCost(),
                'status' => $ride->getStatus(),
            ], $result['rides']),
            'totalPageCount' => $result['totalPageCount'],
            'totalItems' => $result['totalItems']
        ];
        return $this->json($data);
    }

    #[Route('/ride/create', name: 'ride_create', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function create(Request $request, EntityManagerInterface $em, RideService $rideService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['destination', 'from', 'car_id', 'driver_id', 'totalCost', 'status']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $car = $em->getRepository(Car::class)->find($data['car_id']);
        $driver = $em->getRepository(Driver::class)->find($data['driver_id']);
        $review = null;
        if (!empty($data['review_id'])) {
            $review = $em->getRepository(Review::class)->find($data['review_id']);
        }
        if (!$car || !$driver) {
            return $this->json(['errors' => 'Car or Driver not found'], 404);
        }
        $ride = $rideService->createRide($data['destination'], $data['from'], $review, $car, $driver, (int)$data['totalCost'], $data['status']);
        $em->flush();
        return $this->json([
            'id' => $ride->getId(),
            'destination' => $ride->getDestination(),
            'from' => $ride->getFrom(),
            'review' => $ride->getReview()?->getId(),
            'car' => $ride->getCar()?->getId(),
            'driver' => $ride->getDriver()?->getId(),
            'totalCost' => $ride->getTotalCost(),
            'status' => $ride->getStatus(),
        ], 201);
    }

    #[Route('/ride/{id}', name: 'ride_show', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function show(Ride $ride): Response
    {
        return $this->json([
            'id' => $ride->getId(),
            'destination' => $ride->getDestination(),
            'from' => $ride->getFrom(),
            'review' => $ride->getReview()?->getId(),
            'car' => $ride->getCar()?->getId(),
            'driver' => $ride->getDriver()?->getId(),
            'totalCost' => $ride->getTotalCost(),
            'status' => $ride->getStatus(),
        ]);
    }

    #[Route('/ride/{id}/edit', name: 'ride_edit', methods: ['PUT'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function edit(Request $request, Ride $ride, EntityManagerInterface $em, RequestValidatorService $validator, RideService $rideService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['destination', 'from', 'totalCost', 'status']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $rideService->updateRide($ride, $data);
        $em->flush();
        return $this->json([
            'id' => $ride->getId(),
            'destination' => $ride->getDestination(),
            'from' => $ride->getFrom(),
            'review' => $ride->getReview()?->getId(),
            'car' => $ride->getCar()?->getId(),
            'driver' => $ride->getDriver()?->getId(),
            'totalCost' => $ride->getTotalCost(),
            'status' => $ride->getStatus(),
        ]);
    }

    #[Route('/ride/{id}/delete', name: 'ride_delete', methods: ['DELETE'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_DRIVER')]
    public function delete(Ride $ride, EntityManagerInterface $em): Response
    {
        $em->remove($ride);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
