<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Normalizer used with referenced normalized objects.
 */
class CustomReferencedNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private array $references = [];

    public function __construct(private readonly CustomNormalizer $customNormalizer)
    {
    }

    public function normalize(
        mixed $object,
        string $format = null,
        array $context = []
    ): array|bool|string|int|float|null|\ArrayObject {
        $object->setReferences($this->references);
        $data = $this->customNormalizer->normalize($object, $format, $context);
        $this->references = array_merge($this->references, $object->getReferences());

        return $data;
    }

    public function supportsNormalization(mixed $data, $format = null, array $context = []): bool
    {
        return $data instanceof AbstractNormalizable;
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->customNormalizer->getSupportedTypes($format);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return $this->customNormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $this->customNormalizer->supportsDenormalization($data, $type, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->customNormalizer->setSerializer($serializer);
    }
}
