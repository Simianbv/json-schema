<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

use Simianbv\JsonSchema\Fields\Field;

/**
 * @class   JsonKeyValue
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class JsonKeyValue extends Field
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'multiText';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'JsonKeyValue';
    }

    /**
     * Allow multiple sets to be selected.
     *
     * @return JsonKeyValue
     */
    public function multiSelect(): JsonKeyValue
    {
        $this->attributes(['multi-select' => true]);
        return $this;
    }

    /**
     * @param array $keyValues
     *
     * @return JsonKeyValue
     */
    public function values(array $keyValues): JsonKeyValue
    {
        foreach ($keyValues as $key => $value) {
            $this->values[$key] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function provideAdditionalProperties(): array
    {
        return ['options'];
    }
}
