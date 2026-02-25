<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ReportType;
use App\Service\EntityCreationService;
use App\Service\RequestValidatorService;
use App\Service\ReportTypeService;

final class ReportTypeController extends AbstractController
{
    #[Route('/report/type', name: 'report_type_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $types = $em->getRepository(ReportType::class)->findAll();
        $data = array_map(fn($type) => [
            'id' => $type->getId(),
            'name' => $type->getName(),
            'description' => $type->getDescription(),
        ], $types);
        return $this->json($data);
    }

    #[Route('/report/type/create', name: 'report_type_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ReportTypeService $reportTypeService, RequestValidatorService $validator): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'description']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $type = $reportTypeService->createReportType($data['name'], $data['description']);
        $em->flush();
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'description' => $type->getDescription(),
        ], 201);
    }

    #[Route('/report/type/{id}', name: 'report_type_show', methods: ['GET'])]
    public function show(ReportType $type): Response
    {
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'description' => $type->getDescription(),
        ]);
    }

    #[Route('/report/type/{id}/edit', name: 'report_type_edit', methods: ['PUT'])]
    public function edit(Request $request, ReportType $type, EntityManagerInterface $em, RequestValidatorService $validator, ReportTypeService $reportTypeService): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $errors = $validator->validateNotBlankFields($data, ['name', 'description']);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }
        $reportTypeService->updateReportType($type, $data);
        $em->flush();
        return $this->json([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'description' => $type->getDescription(),
        ]);
    }

    #[Route('/report/type/{id}/delete', name: 'report_type_delete', methods: ['DELETE'])]
    public function delete(ReportType $type, EntityManagerInterface $em): Response
    {
        $em->remove($type);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
