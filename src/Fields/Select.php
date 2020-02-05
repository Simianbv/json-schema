<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Simianbv\JsonSchema\Contracts\HasRelationInterface;
use Simianbv\JsonSchema\Traits\HasOptions;
use Simianbv\JsonSchema\Traits\HasRelations;

/**
 * @class   Select
 * @package Simianbv\JsonSchema\Fields
 */
class Select extends Field implements HasRelationInterface
{
    use HasRelations, HasOptions;

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
        return 'Select';
    }

    /**
     * @return array
     */
    public function provideAdditionalProperties(): array
    {
        return ['options'];
    }

    /**
     * Allow multiple options to be selected.
     *
     * @return Select
     */
    public function multiSelect(): Select
    {
        $this->attributes(['multi-select' => true]);
        return $this;
    }

    /**
     * Add an array of options to be selected, or add the name of the model's class for the options.
     *
     * @param array|string $options
     *
     * @return Select
     */
    public function options($options): Field
    {
        if (is_array($options)) {
            return $this->optionsByArray($options);
        }
        if (is_string($options) && class_exists($options)) {
            return $this->hasOne($options);
        }

        return $this;
    }

    /**
     * Allow the with-label to be shown as well.
     *
     * @return Select
     */
    public function displayWithLabel(): Select
    {
        $this->attributes(['with-label' => true]);
        return $this;
    }
}
