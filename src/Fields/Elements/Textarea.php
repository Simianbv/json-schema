<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

/**
 * @class   Textarea
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class Textarea extends Field
{

    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'string';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Textarea';
    }

    /**
     * @param string|int $min
     *
     * @return Field
     */
    public function min($min): Field
    {
        $this->attributes(['minLength' => $min]);
        return $this;
    }

    /**
     * @param string|int $max
     *
     * @return Field
     */
    public function max($max): Field
    {
        $this->attributes(['maxLength' => $max]);
        return $this;
    }
}
