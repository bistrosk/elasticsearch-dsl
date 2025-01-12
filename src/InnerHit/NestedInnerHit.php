<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\InnerHit;

use ONGR\ElasticsearchDSL\NameAwareTrait;
use ONGR\ElasticsearchDSL\NamedBuilderInterface;
use ONGR\ElasticsearchDSL\ParametersTrait;
use ONGR\ElasticsearchDSL\Search;

/**
 * Represents Elasticsearch top level nested inner hits.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-inner-hits.html
 */
class NestedInnerHit implements NamedBuilderInterface
{
    use ParametersTrait;
    use NameAwareTrait;

    private string $path;

    private ?Search $search = null;

    /**
     * Inner hits container init.
     */
    public function __construct(string $name, string $path, Search $search = null)
    {
        $this->setName($name);
        $this->setPath($path);
        if ($search instanceof Search) {
            $this->setSearch($search);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array|\stdClass
    {
        $out = $this->getSearch() ? $this->getSearch()->toArray() : new \stdClass();

        return [
            $this->getPathType() => [
                $this->getPath() => $out,
            ],
        ];
    }

    public function getSearch(): ?Search
    {
        return $this->search;
    }

    public function setSearch(Search $search): static
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Returns 'path' for nested and 'type' for parent inner hits
     */
    private function getPathType(): ?string
    {
        return match ($this->getType()) {
            'nested' => 'path',
            'parent' => 'type',
            default => null,
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'nested';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }
}
