<?php

namespace App\Controller;

use App\Dto\Request\TaskPostRequestDto;
use App\Dto\Request\TaskPutRequestDto;
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
        //check if the content is empty
        if(empty($request->getContent())) {
            return new JsonResponse(["message" => "Invalid parameters."], 400);
        }

        $taskPostRequestDto = $serializer->deserialize($request->getContent(), TaskPostRequestDto::class, 'json');
        $errors = $validator->validate($taskPostRequestDto);

        if (count($errors) > 0) {
            return $this->printValidationErrors($errors, $serializer);
        }

        //check if deadline is valid
        $deadline = $taskPostRequestDto->getDeadline();
        $date = new \DateTimeImmutable();

        if($deadline < $date) {
            return new JsonResponse(["message" => "Deadline should be in the future!"], 400);
        }

        return $taskService->addTask($taskPostRequestDto, $entityManager);
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

    /**
     * @Route("/edit/{id}", methods={"PUT"})
     */
    public function editTask(ValidatorInterface $validator, SerializerInterface $serializer, Request $request,
                             TaskRepository $taskRepository, EntityManagerInterface $entityManager, $id, TaskService $taskService): JsonResponse
    {
        if(!is_numeric($id)) {
            return new JsonResponse(["message" => "id must be a number!"], 400);
        }

        //check if the content is empty or not a valid json
        if(empty($request->getContent())) {
            return new JsonResponse(["message" => "Invalid parameters."], 400);
        }

        $taskPutRequestDto = $serializer->deserialize($request->getContent(), TaskPutRequestDto::class, 'json');
        $errors = $validator->validate($taskPutRequestDto);

        if (count($errors) > 0) {
            return $this->printValidationErrors($errors, $serializer);
        }

        //check if the 2 ids are the same
        if($id != $taskPutRequestDto->getId()) {
            return new JsonResponse(["message" => "You don't have permission to edit this Task!"], 403);
        }

        return $taskService->editTask($taskRepository, $entityManager, $taskPutRequestDto, $id);
    }

    public function printValidationErrors($errors, $serializer): JsonResponse
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
