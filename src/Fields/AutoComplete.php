<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Simianbv\JsonSchema\Contracts\HasRelationInterface;
use Simianbv\JsonSchema\Traits\HasRelations;

/**
 * @class   AutoComplete
 * @package Simianbv\JsonSchema\Fields
 */
class AutoComplete extends Number implements HasRelationInterface
{

    use HasRelations;

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'autocomplete';
    }
}
