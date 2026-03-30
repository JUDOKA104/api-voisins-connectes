<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories')]
class CategorieController extends AbstractController
{
    #[Route('', name: 'api_categories_index', methods: ['GET'])]
    public function index(CategorieRepository $repository): JsonResponse
    {
        // On récupère toutes les catégories de la BDD
        $categories = $repository->findAll();

        // On les retourne en JSON (Idéalement avec des Groups de sérialisation)
        return $this->json($categories);
    }
}
