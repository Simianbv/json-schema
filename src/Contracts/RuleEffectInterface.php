<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

/**
 * @interface RuleEffectInterface
 * @package   Simianbv\JsonSchema\Contracts
 */
interface RuleEffectInterface
{
    const RULE_EFFECT_HIDE = 'hide';
    const RULE_EFFECT_SHOW = 'show';
    const RULE_EFFECT_DISABLE = 'disable';
    const RULE_EFFECT_TOGGLE = 'toggle';
}
