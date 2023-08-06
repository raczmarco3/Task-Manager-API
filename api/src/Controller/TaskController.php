<?php

namespace App\Controller;

use App\Dto\Request\TaskRequestDto;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Converter\JsonConverter;

/**
 * @Route("/api/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/add", methods={"POST"})
     */
    public function addTask(ValidatorInterface $validator, SerializerInterface $serializer,Request $request, TaskService $taskService,
                            TaskRepository $taskRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = $request->getContent();

        //check if the content is empty or not a valid json
        if(empty($content) || json_decode($content) === null) {
            return new JsonResponse(["message" => "Invalid parameters."], 400);
        }

        $taskRequestDto = $serializer->deserialize($request->getContent(), TaskRequestDto::class, 'json');
        $errors = $validator->validate($taskRequestDto);

        if (count($errors) > 0) {
            return $this->printValidationErrors($errors, $serializer);
        }

        //check if deadline is valid
        $deadline = $taskRequestDto->getDeadline();
        $date = new \DateTimeImmutable();

        if($deadline < $date) {
            return new JsonResponse(["message" => "Deadline should be in the future!"], 400);
        }

        return $taskService->addTask($taskRequestDto, $entityManager);
    }

    /**
     * @Route("/get/all", methods={"GET"})
     */
    public function getTasks(TaskRepository $taskRepository, SerializerInterface $serializer, TaskService $taskService): JsonResponse
    {
        return $taskService->getTasks($taskRepository, $serializer);
    }

    /**
     * @Route("/delete/{id}", methods={"DELETE"})
     */
    public function deleteTask($id, EntityManagerInterface $entityManager, TaskRepository $taskRepository, TaskService $taskService): JsonResponse
    {
        if(!is_numeric($id)) {
            return new JsonResponse(["message" => "id must be a number!"], 400);
        }

        return $taskService->deleteTask($taskRepository, $entityManager, $id);
    }

    public function printValidationErrors($errors, $serializer)
    {
        $formatedViolationList = [];

        for ($i = 0; $i < $errors->count(); $i++) {
            $violation = $errors->get($i);
            $formatedViolationList[] = array($violation->getPropertyPath() => $violation->getMessage());
        }

        $msg = ["message" => $formatedViolationList];
        return JsonConverter::jsonResponse($serializer, $msg, 403);
    }
}
