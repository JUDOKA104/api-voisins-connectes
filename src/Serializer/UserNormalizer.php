<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_NORMALIZER_ALREADY_CALLED';

    private UrlHelper $urlHelper;

    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param User $object
     */
    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);

        if (isset($data['photoProfil']) && $object->getPhotoProfil()) {
            $data['photoProfil'] = $this->urlHelper->getAbsoluteUrl($object->getPhotoProfil());
        }

        $badges = [];

        if (in_array('ROLE_ADMIN', $object->getRoles())) {
            $badges[] = [
                'id' => 'admin',
                'label' => 'Modération',
                'icon' => 'ri-shield-star-fill',
                'color' => 'var(--accent)' // Terracotta
            ];
        }

        $annoncesCreeesTerminees = 0;
        foreach ($object->getAnnoncesCreees() as $annonce) {
            if ($annonce->getStatut() === 'Terminé') {
                $annoncesCreeesTerminees++;
            }
        }

        $aidesTerminees = 0;
        $aidesBenevoles = 0;
        foreach ($object->getAnnoncesAidees() as $annonce) {
            if ($annonce->getStatut() === 'Terminé') {
                $aidesTerminees++;
                if ($annonce->isEstRemunere() === false) {
                    $aidesBenevoles++;
                }
            }
        }

        if ($annoncesCreeesTerminees >= 3) {
            $badges[] = [
                'id' => 'createur',
                'label' => 'Créateur de liens',
                'icon' => 'ri-seedling-fill',
                'color' => 'var(--warn)'
            ];
        }

        if ($aidesTerminees >= 10) {
            $badges[] = [
                'id' => 'heros',
                'label' => 'Héros du quartier',
                'icon' => 'ri-medal-fill',
                'color' => '#8e44ad'
            ];
        } elseif ($aidesTerminees >= 3) {
            $badges[] = [
                'id' => 'solidaire',
                'label' => 'Voisin Solidaire',
                'icon' => 'ri-hand-heart-fill',
                'color' => 'var(--ok)'
            ];
        }

        if ($aidesBenevoles >= 3) {
            $badges[] = [
                'id' => 'coeur_or',
                'label' => 'Cœur d\'Or',
                'icon' => 'ri-heart-3-fill',
                'color' => '#e74c3c'
            ];
        }

        $data['badges'] = $badges;

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        // On n'agit que si c'est un User ET qu'on ne l'a pas déjà fait
        return $data instanceof User && !isset($context[self::ALREADY_CALLED]);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            User::class => false, // false car on gère les sous-classes si besoin
        ];
    }
}
