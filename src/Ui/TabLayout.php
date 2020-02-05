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
class TabLayout extends Layout
{

    public function provideAdditionalAttributes(): array
    {
        return ['icon'];
    }

    function initialize(): void
    {
        $this->tab();
    }


    public function icon(string $icon)
    {
        $this->attributes(['icon' => $icon]);
        return $this;
    }
}
