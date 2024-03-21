<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(
        '/api/users',
        name: 'all_users',
        methods: ['GET'],
        options: [
            'description route' => 'Get ALL users',
            'body' => [
                null
            ]
        ]
    )]
    public function all(): Response
    {
        // Retrieve all users from the database
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        // Return the users as JSON response
        return $this->json($users);
    }


    #[Route(
        '/api/users',
        name: 'create_user',
        methods: ['POST'],
        options: [
            'description route' => 'create user',
            'body' => [
                'name'=>'string'
            ]
        ]
    )]
    public function create(Request $request): Response
    {
        // Create a new User object
        $user = new User();

        // Set the user properties based on the request data
        // ...

        // Persist the user to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Return a success response
        return new Response('User created', Response::HTTP_CREATED);
    }
}