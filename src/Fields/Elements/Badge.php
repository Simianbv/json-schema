<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

/**
 * @class   Badge
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class Badge extends Field
{

    /**
     * @var string[]
     */
    protected $types = [];

    /**
     * @var string[]
     */
    protected $base_classes = ['badge'];

    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'select';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Badge';
    }

    /**
     * @return array
     */
    protected function defaultTypes()
    {
        return [
            'success',
            'info',
            'warning',
            'danger',
            'fatal',
        ];
    }

    /**
     * @param array $types
     *
     * @return Field
     */
    public function types(array $types): Field
    {
        foreach ($types as $key => $value) {
            $this->types[$key] = $value;
        }
        return $this;
    }

    /**
     * @param array $types
     *
     * @return Field
     */
    public function addTypes(array $types): Field
    {
        foreach ($types as $type => $class) {
            $this->addType($type, $class);
        }
        return $this;
    }

    /**
     * @param string $type
     * @param mixed  $class
     *
     * @return Field
     */
    public function addType(string $type, $class): Field
    {
        if (is_string($class) || is_array($class)) {
            $this->types[$type] = $class;
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
