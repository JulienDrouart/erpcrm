<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginSuccessListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger,EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;

    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        // Récupérer l'utilisateur connecté
        $user = $event->getUser();
        $user->setLastConnection(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }
}
