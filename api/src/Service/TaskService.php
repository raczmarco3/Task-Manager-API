<?php

namespace App\Service;

use App\Converter\JsonConverter;
use App\Dto\Request\TaskPostRequestDto;
use App\Dto\Request\TaskPutRequestDto;
use App\Dto\Response\TaskResponseDto;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TaskService
{
    public function addTask(TaskPostRequestDto $taskPostRequestDto, EntityManagerInterface $entityManager, User $user): JsonResponse
    {
        //date and time of the creation
        $createdAt = new \DateTimeImmutable();

        //set task data
        $task = new Task();
        $task->setName($taskPostRequestDto->getName());
        $task->setDescription($taskPostRequestDto->getDescription());
        $task->setDeadline($taskPostRequestDto->getDeadline());
        $task->setCreatedAt($createdAt);
        $task->setUpdatedAt($createdAt);
        $task->setUser($user);

        //save task to db
        $entityManager->persist($task);
        $entityManager->flush();

        //check if task was created
        if(is_numeric($task->getId())) {
            return new JsonResponse(["message" => "Task created"], 201);
        }

        return new JsonResponse(["message" => "Task was not created due to a database error!"], 500);
    }

    public function getTasks(TaskRepository $taskRepository, SerializerInterface $serializer, User $user): JsonResponse
    {
        $tasks = $taskRepository->findby(['user' => $user], ['deadline' => 'DESC']);

        if(empty($tasks)) {
            return new JsonResponse(["message" => "There are no tasks yet."], 404);
        }

        $taskResponseDtoArray = [];

        foreach ($tasks as $task)
        {
            $cloeDeadline = false;
            $expired = false;
            //today's date
            $date = new \DateTimeImmutable();

            if($task->getDeadline() < $date) {
                $expired = true;
            }

            if(!$expired) {
                //get the difference between the 2 dates in days
                $difference = $task->getDeadline()->diff($task->getCreatedAt());
                $daysDifference = $difference->days;

                //if the deadline is within a week then the deadline is close
                if($daysDifference <= 7) {
                    $cloeDeadline = true;
                }
            }

            $taskResponseDto = new TaskResponseDto();

            $taskResponseDto->setId($task->getId());
            $taskResponseDto->setExpired($expired);
            $taskResponseDto->setCloseDeadline($cloeDeadline);
            $taskResponseDto->setDeadline($task->getDeadline());
            $taskResponseDto->setName($task->getName());
            $taskResponseDto->setDescription($task->getDescription());
            $taskResponseDto->setUpdatedAt($task->getUpdatedAt());

            $taskResponseDtoArray[] = $taskResponseDto;
        }

        return JsonConverter::jsonResponse($serializer, $taskResponseDtoArray, 200);
    }

    public function deleteTask(TaskRepository $taskRepository, EntityManagerInterface $entityManager, $id, $user): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $id, "user" => $user]);

        if(empty($task)) {
            return new JsonResponse(["message" => "Task not found!"], 404);
        }

        $entityManager->remove($task);
        $entityManager->flush();

        //check if task was deleted
        if(!is_numeric($task->getId())) {
            return new JsonResponse(["message" => "Task deleted."], 200);
        }

        return new JsonResponse(["message" => "Task was not deleted due to a database error!"], 500);
    }

    public function editTask(TaskRepository $taskRepository, EntityManagerInterface $entityManager,
                             TaskPutRequestDto $taskPutRequestDto, $id, User $user): JsonResponse
    {
        $task = $taskRepository->findOneBy(["id" => $id, "user" => $user]);
        //Today's date
        $date = new \DateTimeImmutable();

        if(empty($task)) {
            return new JsonResponse(["message" => "Task not found!"], 404);
        }

        $task->setDescription($taskPutRequestDto->getDescription());
        $task->setName($taskPutRequestDto->getName());
        $task->setDeadline($taskPutRequestDto->getDeadline());
        $task->setUpdatedAt($date);

        $entityManager->flush();
        return new JsonResponse(["message" => "Task edited"], 200);
    }
}