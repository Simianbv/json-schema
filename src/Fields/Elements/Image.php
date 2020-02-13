<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

use Simianbv\JsonSchema\Fields\Field;

/**
 * @class   Image
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class Image extends Field
{

    /**
     * Returns the input element type.
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'string';
    }

    /**
     * Get the name of the Field object.
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Image';
    }

}
