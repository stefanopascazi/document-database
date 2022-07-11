<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Factory\ResponseFactory;
use App\Entity\User;
use App\Repository\UserRepository;


class RegisterController extends AbstractController
{

    public function __construct(private ResponseFactory $responseFactory)
    {}

    #[Route('/register', name: 'app_register', methods:["POST"])]
    public function index( Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository ): Response
    {
        $form = json_decode($request->getContent(), true);

        if( !is_array($form) || !array_key_exists("email", $form) || !array_key_exists("password", $form) )
        {
            return $this->responseFactory->create([
                "message" => "Username and/or password are require"
            ]);
        }

        $user = new User;
        $user->setEmail($form['email']);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form['password']
            )
        );

        $userRepository->add($user, true);

        return $this->responseFactory->create([
            "data" => $user
        ], 201);

    }
}
