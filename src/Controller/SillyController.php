<?php

namespace App\Controller;

//use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/silly')]
final class SillyController extends AbstractController
{
    #[Route('/get', name: 'app_silly_get', methods: ['GET'])]
    public function get(Request $request): Response
    {
        $queryParams = $request->query->all();
        return new JsonResponse($queryParams);
    }
    #[Route('/post', name: 'app_silly_post', methods: ['POST'])]
    public function post(Request $request): Response
    {
        $request_body = json_decode($request->getContent(), true);
        return new JsonResponse($request_body);
    }
    #[Route('/get_unique_item/{id}', name: 'app_silly_get_unique_item', methods: ['GET'])]
    public function getUniqueItem(string $id): Response
    {
        return new JsonResponse("Id is pretty unique right?: " . $id);
    }
    #[Route('/get_un_unique_item/{name}', name: 'app_silly_get_un_unique_item', methods: ['GET'])]
    public function index(string $name): Response
    {
        return new JsonResponse("Users might have same names, given unique login data! " . $name);
    }
}
