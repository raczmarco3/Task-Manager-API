<?php

namespace App\Controller;

use App\Dto\Request\UserPostRequestDto;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class ApiRegistrationController extends AbstractController
{
    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, UserService $userService): JsonResponse
    {
        //check if the content is empty
        if(empty($request->getContent())) {
            return new JsonResponse(["message" => "Invalid parameters."], 400);
        }

        $userPostRequestDto = $serializer->deserialize($request->getContent(), UserPostRequestDto::class, 'json');
        return $userService->register($userRepository, $userPostRequestDto, $passwordHasher, $entityManager);
    }

}