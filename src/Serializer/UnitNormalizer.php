<?php
namespace App\Serializer;

use App\Entity\Unit;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UnitNormalizer //implements NormalizerInterface
{
    /*public function __construct(
        private ObjectNormalizer $normalizer,
        private TranslatorInterface $translator
    ) {
    }

    /**
     * @return array<
     
    public function normalize($unit, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($unit, $format, $context);
        $data['name'] = $this->translator->trans($data['name'], [], 'unit');
        return $data;
    }

    public function supportsNormalization(
        $data,
        string $format = null,
        array $context = []
    ) {
        return $data instanceof Unit;
    }*/
}