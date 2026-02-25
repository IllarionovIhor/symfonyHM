<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Report;
use App\Entity\ReportType;
use App\Entity\Client;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\ReportService;

final class ReportController extends AbstractController
{
    #[Route('/report', name: 'report_index', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $result = $em->getRepository(Report::class)->getAllReportsByFilter($requestData, $itemsPerPage, $page);
        $data = [
            'reports' => array_map(fn($report) => [
                'id' => $report->getId(),
                'reportType' => $report->getReportType()?->getId(),
                'comment' => $report->getComment(),
                'client' => $report->getClient()?->getId(),
            ], $result['reports']),
            'totalPageCount' => $result['totalPageCount'],
            'totalItems' => $result['totalItems']
        ];
        return $this->json($data);
    }

    #[Route('/report/create', name: 'report_create', methods: ['POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function create(Request $request, EntityManagerInterface $em, ReportService $reportService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['reportType_id', 'comment', 'client_id']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $reportType = $em->getRepository(ReportType::class)->find($data['reportType_id']);
        $client = $em->getRepository(Client::class)->find($data['client_id']);
        if (!$reportType || !$client) {
            return $this->json(['errors' => 'ReportType or Client not found'], 404);
        }
        $report = $reportService->createReport($reportType, (int)$data['comment'], $client);
        $em->flush();
        return $this->json([
            'id' => $report->getId(),
            'reportType' => $report->getReportType()?->getId(),
            'comment' => $report->getComment(),
            'client' => $report->getClient()?->getId(),
        ], 201);
    }

    #[Route('/report/{id}', name: 'report_show', methods: ['GET'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function show(Report $report): Response
    {
        return $this->json([
            'id' => $report->getId(),
            'reportType' => $report->getReportType()?->getId(),
            'comment' => $report->getComment(),
            'client' => $report->getClient()?->getId(),
        ]);
    }

    #[Route('/report/{id}/edit', name: 'report_edit', methods: ['PUT'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function edit(Request $request, Report $report, EntityManagerInterface $em, RequestValidatorService $validator, ReportService $reportService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['comment']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $reportService->updateReport($report, $data);
        $em->flush();
        return $this->json([
            'id' => $report->getId(),
            'reportType' => $report->getReportType()?->getId(),
            'comment' => $report->getComment(),
            'client' => $report->getClient()?->getId(),
        ]);
    }

    #[Route('/report/{id}/delete', name: 'report_delete', methods: ['DELETE'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_CLIENT')]
    public function delete(Report $report, EntityManagerInterface $em): Response
    {
        $em->remove($report);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
