<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

/**
 * @class   GroupLayout
 * @package Simianbv\JsonSchema\Ui
 */
class TabsLayout extends Layout
{
    function initialize(): void
    {
        $this->attribute('animated', false);
        $this->tabs();
    }

    public function provideAdditionalAttributes(): array
    {
        return ['animated'];
    }

    public function animate($animate = true)
    {
        $this->attribute('animated', $animate);
        return $this;
    }
}
