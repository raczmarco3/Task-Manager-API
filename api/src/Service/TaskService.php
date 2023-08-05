<?php

namespace App\Service;

use App\Dto\Request\TaskRequestDto;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    public function addTask(TaskRequestDto $taskRequestDto, TaskRepository $taskRepository, EntityManagerInterface $entityManager): JsonResponse
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
}