<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $noms = ['Bricolage', 'Jardinage', 'Garde d\'animaux', 'Cours particuliers', 'Prêt de matériel'];

        foreach ($noms as $nom) {
            $existante = $manager->getRepository(Categorie::class)->findOneBy(['nom' => $nom]);

            if (!$existante) {
                $categorie = new Categorie();
                $categorie->setNom($nom);
                $manager->persist($categorie);
            }
        }
    }
}
