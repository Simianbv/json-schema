<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Ui;

use Simianbv\JsonSchema\Contracts\ConditionInterface;
use Simianbv\JsonSchema\Contracts\RuleEffectInterface;
use Simianbv\JsonSchema\Contracts\RuleInterface;

/**
 * @class    Rule
 * @package  Simianbv\JsonSchema\Fields\Ui
 */
class Rule implements RuleInterface
{
    /**
     * @var array
     */
    protected static $effects = [
        self::RULE_EFFECT_DISABLE,
        self::RULE_EFFECT_HIDE,
        self::RULE_EFFECT_SHOW,
        self::RULE_EFFECT_TOGGLE,
    ];

    /**
     * @var string|null
     */
    protected $rule_effect;

    /**
     * @var array
     */
    protected $rule_conditions = [];

    /**
     * @var string
     */
    protected $rule_scope;

    /**
     * @param string                                  $effect
     * @param ConditionInterface[]|ConditionInterface $conditions
     *
     * @return Rule
     */
    public function make(string $effect, $conditions): Rule
    {
        $this->effect($effect);

        if (is_array($conditions)) {
            foreach ($conditions as $condition) {
                if ($condition instanceof ConditionInterface) {
                    $this->condition($condition);
                }
            }
        } else {
            if ($conditions instanceof ConditionInterface) {
                $this->condition($conditions);
            }
        }

        return $this;
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return RuleInterface
     */
    public function condition(ConditionInterface $condition): RuleInterface
    {
        $this->rule_conditions[$condition->getScope()] = $condition;
        return $this;
    }

    /**
     * @param string|RuleEffectInterface $effect
     *
     * @return RuleInterface
     */
    public function effect($effect): RuleInterface
    {
        $this->rule_effect = $effect;
        return $this;
    }

    /**
     * @return string|false
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param array $arr
     *
     * @return bool
     */
    protected function isMultiArray(array $arr)
    {
        rsort($arr);
        return isset($arr[0]) && is_array($arr[0]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $rule = [
            "effect" => $this->rule_effect,
            "condition" => [],
        ];

        $results = [];
        foreach ($this->rule_conditions as $scope => $condition) {
            $results = [
                "scope" => "#/properties/" . $condition->getScope(),
                "schema" => [
                    "enum" => $condition->getValues(),
                ],
            ];
        }

        if (count($results) == 1) {
            $rule["condition"] = $results[0];
        } else {
            if (count($results) > 1) {
                $rule["condition"] = $results;
            }
        }

        return $rule;
    }
}
