<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

/**
 * @interface RuleInterface
 * @package   Simianbv\JsonSchema\Contracts
 */
interface RuleInterface extends RuleEffectInterface
{

    /**
     * Add a condition to the rule.
     *
     * @param ConditionInterface $condition
     *
     * @return RuleInterface
     */
    public function condition(ConditionInterface $condition): RuleInterface;

    /**
     * Add an effect to the rule.
     *
     * @param $effect
     *
     * @return RuleInterface
     */
    public function effect($effect): RuleInterface;

}
