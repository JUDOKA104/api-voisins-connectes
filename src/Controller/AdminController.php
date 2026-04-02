<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/stats', name: 'api_admin_stats', methods: ['GET'])]
    public function getStats(AnnonceRepository $annonceRepo, UserRepository $userRepo): JsonResponse
    {
        $fifteenMinutesAgo = new \DateTimeImmutable('-15 minutes');

        $activeUsersCount = $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.lastActivityAt >= :limit')
            ->andWhere('u.isBanned = :banned')
            ->setParameter('limit', $fifteenMinutesAgo)
            ->setParameter('banned', false)
            ->getQuery()
            ->getSingleScalarResult();

        $topCategories = $annonceRepo->createQueryBuilder('a')
            ->select('c.nom as categorie, COUNT(a.id) as nombre_annonces')
            ->join('a.categorie', 'c')
            ->groupBy('c.id')
            ->orderBy('nombre_annonces', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        return $this->json([
            'utilisateurs_actifs' => (int)$activeUsersCount,
            'top_categories' => $topCategories,
            'total_annonces' => $annonceRepo->count([]),
        ]);
    }

    #[Route('/users/search', name: 'api_admin_user_search', methods: ['GET'])]
    public function searchUsers(Request $request, UserRepository $userRepo): JsonResponse
    {
        $query = $request->query->get('q', '');
        if (empty($query)) return $this->json([]);

        $users = $userRepo->createQueryBuilder('u')
            ->where('u.email LIKE :q OR u.nom LIKE :q OR u.prenom LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        return $this->json($users, 200, [], ['groups' => 'annonce:read']);
    }

    #[Route('/users/{id}/annonces', name: 'api_admin_user_annonces', methods: ['GET'])]
    public function getUserAnnonces(User $user, AnnonceRepository $annonceRepo): JsonResponse
    {
        $annonces = $annonceRepo->findBy(
            ['createur' => $user],
            ['dateCreation' => 'DESC']
        );

        return $this->json($annonces, 200, [], ['groups' => 'annonce:read']);
    }

    #[Route('/users/{id}/ban', name: 'api_admin_user_ban', methods: ['PATCH'])]
    public function banUser(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($user->isBanned()) {
            // DÉBANNISSEMENT
            $user->setIsBanned(false);
            $user->setBanMotif(null);
            $status = 'débanni';
        } else {
            // BANNISSEMENT
            $user->setIsBanned(true);
            $user->setBanMotif($data['motif'] ?? 'Aucun motif précisé');
            $status = 'banni';
        }

        $em->flush();

        return $this->json([
            'message' => "L'utilisateur {$user->getEmail()} a été $status.",
            'isBanned' => $user->isBanned(),
            'banMotif' => $user->getBanMotif()
        ]);
    }

    #[Route('/users/banned', name: 'api_admin_users_banned', methods: ['GET'])]
    public function getBannedUsers(UserRepository $userRepo): JsonResponse
    {
        $banned = $userRepo->findBy(['isBanned' => true]);
        return $this->json($banned, 200, [], ['groups' => 'annonce:read']);
    }

    #[Route('/annonces/{id}', name: 'api_admin_annonce_delete', methods: ['DELETE'])]
    public function deleteAnyAnnonce(Annonce $annonce, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($annonce);
        $em->flush();
        return $this->json(['message' => 'Annonce supprimée par la modération.']);
    }
}
