<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(
        '/api',
        name: 'app_api_home',
        methods: ['GET'],
        options: [
            'description route' => 'Get the API version',
            'body' => [
                null
            ]
        ]
    )]
    public function index(): JsonResponse
    {
        return $this->json([
            'version' => $_ENV['APP_VERSION']
        ]);
    }
}
