<?php

namespace App\Service;

use App\Converter\JsonConverter;
use App\Dto\Request\TaskRequestDto;
use App\Dto\Response\TaskResponseDto;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TaskService
{
    public function addTask(TaskRequestDto $taskRequestDto, EntityManagerInterface $entityManager): JsonResponse
    {
        //date and time of the creation
        $createdAt = new \DateTimeImmutable();

        //set task data
        $task = new Task();
        $task->setName($taskRequestDto->getName());
        $task->setDescription($taskRequestDto->getDescription());
        $task->setDeadline($taskRequestDto->getDeadline());
        $task->setCreatedAt($createdAt);

        //save task to db
        $entityManager->persist($task);
        $entityManager->flush();

        //check if task was created
        if(is_numeric($task->getId())) {
            return new JsonResponse(["message" => "Task created"], 201);
        }

        return new JsonResponse(["message" => "Task was not created due to a database error!"], 500);
    }

    public function getTasks(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        $tasks = $taskRepository->findby([], ['deadline' => 'DESC']);

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

            $taskResponseDtoArray[] = $taskResponseDto;
        }

        return JsonConverter::jsonResponse($serializer, $taskResponseDtoArray, 200);
    }

    public function deleteTask(TaskRepository $taskRepository ,EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $task = $taskRepository->findOneBy(['id' => $id]);

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
}