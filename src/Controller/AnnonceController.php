<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/annonces')]
class AnnonceController extends AbstractController
{
    #[Route('', name: 'api_annonces_index', methods: ['GET'])]
    public function index(AnnonceRepository $repository): JsonResponse
    {
        $catId = $request->query->get('categorie');
        if ($catId) {
            $annonces = $repo->findBy(['categorie' => $catId]);
        } else {
            $annonces = $repo->findAll();
        }

        return $this->json($annonces, context: ['groups' => 'annonce:read']);
    }

    #[Route('', name: 'api_annonces_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, \App\Repository\CategorieRepository $categorieRepo): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        // On vérifie aussi la présence de la catégorie dans le JSON
        if (!$data || !isset($data['titre']) || !isset($data['description']) || !isset($data['categorie_id'])) {
            return $this->json(['erreur' => 'Données incomplètes (titre, description et categorie_id requis)'], Response::HTTP_BAD_REQUEST);
        }

        // On cherche la catégorie en base
        $categorie = $categorieRepo->find($data['categorie_id']);
        if (!$categorie) {
            return $this->json(['erreur' => 'Catégorie introuvable'], Response::HTTP_NOT_FOUND);
        }

        $annonce = new Annonce();
        $annonce->setTitre($data['titre']);
        $annonce->setDescription($data['description']);

        $annonce->setStatut('En attente');

        // On définit la date actuelle
        $annonce->setDateCreation(new \DateTimeImmutable());

        $annonce->setCreateur($user);
        $annonce->setCategorie($categorie); // On lie la catégorie !

        $em->persist($annonce);
        $em->flush();

        return $this->json(['message' => 'Annonce créée avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/{id}/aider', name: 'api_annonces_aider', methods: ['PATCH'])]
    public function aider(Annonce $annonce, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($annonce->getCreateur() === $user) {
            return $this->json(['erreur' => 'Vous ne pouvez pas vous aider vous-même'], Response::HTTP_FORBIDDEN);
        }

        if ($annonce->getHelpers()->contains($user)) {
            return $this->json(['erreur' => 'Déjà inscrit'], Response::HTTP_CONFLICT);
        }

        $annonce->addHelper($user);
        $annonce->setStatut('En cours');

        //  LOGS
        $log = new Commentaire();
        $log->setContenu("📢 " . $user->getPrenom() . " a rejoint l'équipe pour aider !");
        $log->setCreatedAt(new \DateTimeImmutable());
        $log->setAuteur($user);
        $log->setAnnonce($annonce);
        $em->persist($log);

        $em->flush();

        return $this->json(['message' => 'Inscription réussie']);
    }

    #[Route('/{id}/desister', name: 'api_annonces_desister', methods: ['PATCH'])]
    public function desister(Annonce $annonce, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $motif = $data['motif'] ?? 'Pas de motif précisé';

        if (!$annonce->getHelpers()->contains($user)) {
            return $this->json(['erreur' => 'Vous n\'êtes pas sur cette annonce'], Response::HTTP_BAD_REQUEST);
        }

        $annonce->removeHelper($user);

        if ($annonce->getHelpers()->isEmpty()) {
            $annonce->setStatut('En attente');
        }

        // LOGS
        $log = new Commentaire();
        $log->setContenu("🏃 " . $user->getPrenom() . " s'est désisté. Motif : " . $motif);
        $log->setCreatedAt(new \DateTimeImmutable());
        $log->setAuteur($user);
        $log->setAnnonce($annonce);
        $em->persist($log);

        $em->flush();

        return $this->json(['message' => 'Désistement enregistré']);
    }

    #[Route('/{id}/statut', name: 'api_annonces_set_statut', methods: ['PATCH'])]
    public function setStatut(Annonce $annonce, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nouveauStatut = $data['statut'] ?? null;

        if (!in_array($nouveauStatut, ['En attente', 'En cours', 'Terminé'])) {
            return $this->json(['erreur' => 'Statut invalide'], 400);
        }

        $annonce->setStatut($nouveauStatut);
        $em->flush();

        return $this->json(['message' => 'Statut mis à jour']);
    }

    #[Route('/{id}', name: 'api_annonces_delete', methods: ['DELETE'])]
    public function delete(Annonce $annonce, EntityManagerInterface $em): JsonResponse
    {
        // Seul le créateur peut supprimer
        if ($annonce->getCreateur() !== $this->getUser()) {
            return $this->json(['erreur' => 'Interdit'], Response::HTTP_FORBIDDEN);
        }
        $em->remove($annonce);
        $em->flush();
        return $this->json(['message' => 'Annonce supprimée']);
    }
}
