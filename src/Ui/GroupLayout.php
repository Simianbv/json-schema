<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

/**
 * @class   GroupLayout
 * @package Simianbv\JsonSchema
 */
class GroupLayout extends Layout
{
    function initialize(): void
    {
        $this->group();
    }
}
