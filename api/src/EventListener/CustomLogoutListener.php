<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class CustomLogoutListener
{
    public function onLogout(LogoutEvent $logoutEvent): void
    {
        $logoutEvent->setResponse(new JsonResponse(["message" => "You have logged out!"], 200));
    }
}