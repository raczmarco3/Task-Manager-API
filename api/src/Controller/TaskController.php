<?php

namespace App\Controller;

use App\Dto\Request\TaskRequestDto;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use PHPUnit\Util\Json;
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

        //if the content is empty or not a valid json
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

        if($deadline<$date) {
            return new JsonResponse(["message" => "Deadline should be in the future!"], 400);
        }

        return $taskService->addTask($taskRequestDto, $taskRepository, $entityManager);
    }

    public function printValidationErrors($errors, $serializer) {
        $formatedViolationList = [];

        for ($i = 0; $i < $errors->count(); $i++) {
            $violation = $errors->get($i);
            $formatedViolationList[] = array($violation->getPropertyPath() => $violation->getMessage());
        }

        $msg = ["msg" => $formatedViolationList];
        return JsonConverter::jsonResponse($serializer, $msg, 403);
    }
}
