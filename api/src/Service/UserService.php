<?php

namespace App\Service;

use App\Dto\Request\UserPostRequestDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function register(UserRepository $userRepository, UserPostRequestDto $userPostRequestDto,
                             UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $userRepository->findOneBy(["username" => $userPostRequestDto->getUsername()]);

        if($user) {
            return new JsonResponse(["message" => "This user already exists!"], 403);
        }

        $user = new User();
        $user->setUsername($userPostRequestDto->getUsername());

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userPostRequestDto->getPassword()
        );

        $user->setPassword($hashedPassword);

        //save to db
        $entityManager->persist($user);
        $entityManager->flush();

        //check if registration was a success
        if(is_numeric($user->getId())) {
            return new JsonResponse(["message" => "Registration successful!"], 201);
        }

        return new JsonResponse(["message" => "Registration was not successful due to a database error!"], 500);
    }
}