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
        // On évite la boucle infinie
        $context[self::ALREADY_CALLED] = true;

        // On laisse Symfony faire la sérialisation de base (getters, groups, etc.)
        $data = $this->normalizer->normalize($object, $format, $context);

        // On transforme le chemin relatif en URL absolue
        if (isset($data['photoProfil']) && $object->getPhotoProfil()) {
            $data['photoProfil'] = $this->urlHelper->getAbsoluteUrl($object->getPhotoProfil());
        }

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
