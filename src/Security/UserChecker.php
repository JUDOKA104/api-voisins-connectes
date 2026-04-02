<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; // 👈 AJOUT DE L'IMPORT

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isBanned()) {
            $motif = $user->getBanMotif() ? ' Motif : ' . $user->getBanMotif() : '';
            throw new CustomUserMessageAccountStatusException("Votre compte a été banni de LienUrbain.$motif");
        }
    }

    // 👇 MISE À JOUR DE LA SIGNATURE ICI 👇
    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void {}
}
