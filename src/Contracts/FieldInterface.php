<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

/**
 * @interface FieldInterface
 * @package   Simianbv\JsonSchema\Contracts
 */
interface FieldInterface extends ElementInterface
{

    const COMPONENT_TEXT = 'text';
    const COMPONENT_NUMBER = 'number';
    const COMPONENT_BOOLEAN = 'boolean';
    const COMPONENT_SWITCH = 'switch';
    const COMPONENT_CHECKBOX = 'checkbox';
    const COMPONENT_RADIO = 'radio';
    const COMPONENT_SELECT = 'select';
    const COMPONENT_DATE = 'date';
    const COMPONENT_DATETIME = 'datetime';
    const COMPONENT_TIME = 'time';
    const COMPONENT_TAGS = 'time';

}
