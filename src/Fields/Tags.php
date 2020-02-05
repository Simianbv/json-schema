<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Simianbv\JsonSchema\Contracts\HasRelationInterface;
use Simianbv\JsonSchema\Traits\HasItems;
use Simianbv\JsonSchema\Traits\HasOptions;
use Simianbv\JsonSchema\Traits\HasRelations;

/**
 * @class   Tags
 * @package Simianbv\JsonSchema\Fields
 */
class Tags extends Field implements HasRelationInterface
{

    use HasItems, HasOptions, HasRelations;

    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'array';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Tags';
    }

    /**
     * Allow the autocomplete to autofill and search in the background.
     *
     * @return Tags
     */
    public function autocomplete(): Tags
    {
        $this->attributes(['autocomplete' => true]);
        return $this;
    }

    /**
     * @return array
     */
    public function provideAdditionalProperties(): array
    {
        return ['items' => ['type' => 'object'], 'options'];
    }

    /**
     * Allow multiple options to be selected.
     *
     * @return Tags
     */
    public function multiple(): Tags
    {
        $this->attributes(['multiple' => true]);
        return $this;
    }

    /**
     * Add an array of options to be selected, or add the name of the model's class for the options.
     *
     * @param array|string $options
     *
     * @return Tags
     */
    public function options($options): Tags
    {
        if (is_array($options)) {
            return $this->optionsByArray($options);
        }
        if (is_string($options) && class_exists($options)) {
            return $this->optionsByRelation($options);
        }

        return $this;
    }

    /**
     * @return Tags
     */
    public function displayWithLabel(): Tags
    {
        $this->attributes(['with-label' => true]);
        return $this;
    }
}
