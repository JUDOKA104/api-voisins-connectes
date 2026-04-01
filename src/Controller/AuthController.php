<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupération des données textuelles (form-data)
        $email = $request->request->get('email');
        $plainPassword = $request->request->get('password');
        $confirmPassword = $request->request->get('password_confirm');
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $photoFile = $request->files->get('photoProfil');

        // Vérification basique si tout est présent
        if (!$email || !$plainPassword || !$confirmPassword || !$nom || !$prenom || !$photoFile) {
            return new JsonResponse(['erreur' => 'Tous les champs sont obligatoires'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si l'email existe déjà (optionnel mais recommandé)
        if ($entityManager->getRepository(User::class)->findOneBy(['email' => $email])) {
            return new JsonResponse(['erreur' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
        }

        // On compare les deux mots de passe
        if ($plainPassword !== $confirmPassword) {
            return new JsonResponse(['erreur' => 'Les mots de passe ne correspondent pas'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setNom($nom);
        $user->setPrenom($prenom);

        // Hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // On lui donne le rôle de base
        $user->setRoles(['ROLE_USER']);

        // Gestion de l'upload du fichier
        $newFilename = uniqid('', true) . '.' . $photoFile->guessExtension();

        // On déplace le fichier dans le dossier public/uploads/profils
        $photoFile->move(
            $this->getParameter('kernel.project_dir') . '/public/uploads/profils',
            $newFilename
        );

        // On sauvegarde le chemin dans l'entité
        $user->setPhotoProfil('/uploads/profils/' . $newFilename);

        // Sauvegarde en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        return $this->json($this->getUser(), 200, [], ['groups' => 'annonce:read']);
    }
}
