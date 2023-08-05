<?php

namespace App\Converter;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonConverter
{
    public static function jsonResponse($serializer, $data, $httpCode = 0)
    {
        $response = new JsonResponse();
        $jsonContent = $serializer->serialize($data, 'json');
        $response->setContent($jsonContent);

        if($httpCode > 0) {
            $response->setStatusCode($httpCode);
        }

        return $response;
    }
}
