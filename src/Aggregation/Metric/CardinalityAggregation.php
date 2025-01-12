<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation\Metric;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Type\MetricTrait;
use ONGR\ElasticsearchDSL\ScriptAwareTrait;

/**
 * Difference values counter.
 *
 * @link http://goo.gl/tG7ciG
 */
class CardinalityAggregation extends AbstractAggregation
{
    use MetricTrait;
    use ScriptAwareTrait;

    /**
     * @var int
     */
    private $precisionThreshold;

    /**
     * @var bool
     */
    private $rehash;

    /**
     * {@inheritdoc}
     */
    public function getArray(): array|\stdClass
    {
        $out = array_filter(
            [
                'field' => $this->getField(),
                'script' => $this->getScript(),
                'precision_threshold' => $this->getPrecisionThreshold(),
                'rehash' => $this->isRehash(),
            ],
            static fn($val): bool => $val || is_bool($val)
        );

        $this->checkRequiredFields($out);

        return $out;
    }

    /**
     * @return int
     */
    public function getPrecisionThreshold()
    {
        return $this->precisionThreshold;
    }

    /**
     * @param int $precision
     *
     * @return $this
     */
    public function setPrecisionThreshold($precision)
    {
        $this->precisionThreshold = $precision;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRehash()
    {
        return $this->rehash;
    }

    /**
     * @param bool $rehash
     *
     * @return $this
     */
    public function setRehash($rehash)
    {
        $this->rehash = $rehash;

        return $this;
    }

    /**
     * Checks if required fields are set.
     *
     *
     * @throws \LogicException
     */
    private function checkRequiredFields(array $fields)
    {
        if (!array_key_exists('field', $fields) && !array_key_exists('script', $fields)) {
            throw new \LogicException('Cardinality aggregation must have field or script set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'cardinality';
    }
}
