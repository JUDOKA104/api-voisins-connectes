<?php


namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Attribute\Groups;

#[Route('/api/annonces/{id}/commentaires')]
class CommentaireController extends AbstractController
{
    #[Route('', name: 'api_commentaires_create', methods: ['POST'])]
    public function create(Annonce $annonce, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!$data || empty($data['contenu'])) {
            return $this->json(['erreur' => 'Le message ne peut pas être vide'], Response::HTTP_BAD_REQUEST);
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu($data['contenu']);
        $commentaire->setCreatedAt(new \DateTimeImmutable());
        $commentaire->setAuteur($user);
        $commentaire->setAnnonce($annonce);

        $em->persist($commentaire);
        $em->flush();

        return $this->json(['message' => 'Commentaire ajouté'], Response::HTTP_CREATED);
    }
}
