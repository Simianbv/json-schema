<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

/**
 * @class   Number
 * @package Simianbv\JsonSchema\Fields
 */
class Number extends Field
{
    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'number';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Number';
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function isValid($value): bool
    {
        return is_int($value) || is_float($value) || is_double($value) || is_numeric($value);
    }

    /**
     * @param $min
     *
     * @return Number
     */
    public function min($min): Number
    {
        if ($this->isValid($min)) {
            $this->attributes(['minLength' => $min]);
            $this->field_minimum = $min;
        }
        return $this;
    }

    /**
     * @param $max
     *
     * @return Number
     */
    public function max($max): Number
    {
        if ($this->isValid($max)) {
            $this->attributes(['maxLength' => $max]);
            $this->field_maximum = $max;
        }
        return $this;
    }

    /**
     * @param $step
     *
     * @return Number
     */
    public function step($step): Number
    {
        if ($this->isValid($step)) {
            $this->attributes(['step' => $step]);
        }
        return $this;
    }

    /**
     * @param $step
     *
     * @return Number
     */
    public function precision($step): Number
    {
        if (is_int($step)) {
            $this->attributes(['precision' => (int)$step]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function integer(): Number
    {
        $this->step(1);
        return $this;
    }

    /**
     * @return Number
     */
    public function unsigned(): Number
    {
        $this->min(1);
        return $this;
    }
}
