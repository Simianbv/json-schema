<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Filters\Elements;


use Simianbv\JsonSchema\Filters\Filter;

class Number extends Filter
{

    /**
     * @inheritDoc
     */
    public function getColumnType(): string
    {
        return 'int';
    }

    /**
     * @inheritDoc
     */
    public function getComponentType(): string
    {
        return 'number';
    }
}
