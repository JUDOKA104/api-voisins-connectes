<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        // Nombre d'utilisateurs actifs (15 dernières minutes)
        $activeUsersCount = $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.lastActivityAt >= :limit')
            ->andWhere('u.isBanned = :banned')
            ->setParameter('limit', $fifteenMinutesAgo)
            ->setParameter('banned', false)
            ->getQuery()
            ->getSingleScalarResult();

        // Catégories les plus demandées (Top 3)
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

    #[Route('/users/{id}/ban', name: 'api_admin_user_ban', methods: ['PATCH'])]
    public function banUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $user->setIsBanned(true);
        $em->flush();
        return $this->json(['message' => "L'utilisateur {$user->getEmail()} a été banni."]);
    }

    #[Route('/annonces/{id}', name: 'api_admin_annonce_delete', methods: ['DELETE'])]
    public function deleteAnyAnnonce(Annonce $annonce, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($annonce);
        $em->flush();
        return $this->json(['message' => 'Annonce supprimée par la modération.']);
    }
}
