<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

/**
 * @class   HasOne
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class HasOne extends Relation
{
    /**
     * @return string
     */
    public function getRelationType(): string
    {
        return self::RELATION_ONE_TO_MANY;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return self::RELATED_HAS_ONE;
    }

    /**
     * @inheritDoc
     */
    public function toJson($options = 0)
    {
        // @todo fix this implementation to actually return the right data
        return json_encode($this);
    }
}
