<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.request')]
class ActivityListener
{
    public function __construct(
        private TokenStorageInterface  $tokenStorage,
        private EntityManagerInterface $em
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        $token = $this->tokenStorage->getToken();
        if (!$token) return;

        $user = $token->getUser();
        if (!$user instanceof User) return;

        // On met à jour la date seulement si elle date de plus de 5 minutes (pour économiser la BDD)
        $now = new \DateTimeImmutable();
        if ($user->getLastActivityAt() === null || $user->getLastActivityAt() < $now->modify('-5 minutes')) {
            $user->setLastActivityAt($now);
            $this->em->flush();
        }
    }
}
