<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Ui;

use Simianbv\JsonSchema\Contracts\ConditionInterface;

/**
 * @class   Condition
 * @package Simianbv\JsonSchema\Fields\Ui
 */
class Condition implements ConditionInterface
{
    /**
     * @var string
     */
    protected $scope_property = null;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Condition constructor.
     *
     * @param null  $scope
     * @param array $values
     */
    public function __construct($scope = null, $values = [])
    {
        if ($scope) {
            $this->scope($scope);
        }
        if (count($values) > 0) {
            $this->values = $values;
        }
    }

    /**
     * Build up the Condition, add the values to be matched and the scope of the condition.
     *
     * @param string $scope
     * @param mixed  $values
     *
     * @return Condition
     */
    public function make(string $scope, $values): self
    {
        $this->scope($scope);

        if (is_array($values)) {
            foreach ($values as $val) {
                $this->value($val);
            }
        } else {
            $this->value($values);
        }

        return $this;
    }

    /**
     * Set up the scope for this condition.
     *
     * @param string $scope
     *
     * @return Condition
     */
    public function scope(string $scope): self
    {
        $this->scope_property = $scope;
        return $this;
    }

    /**
     * Add a value to be matched on this condition.
     *
     * @param mixed $value
     *
     * @return Condition
     */
    public function value($value): self
    {
        $this->values[] = $value;
        return $this;
    }

    /**
     * Return the condition as a JSON string.
     *
     * @return false|string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Get the scope for this condition.
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope_property;
    }

    /**
     * Returns all the values to be matched by this condition.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Returns this condition as a JSON string based on the toArray properties.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray()) ?? '{"error": "Unable to parse condition"}';
    }

    /**
     * Return this condition as an array of the JSON SCHEMA properties.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "scope" => "#/properties/" . $this->getScope(),
            "schema" => [
                "enum" => $this->getValues(),
            ],
        ];
    }
}
