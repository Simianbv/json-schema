<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

/**
 * @class   ID
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class ID extends Number
{

    /**
     * Get the name of the Field object.
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'ID';
    }

    /**
     * Set up any default values you want to override by default.
     * @return void
     */
    protected function setDefaultAttributes()
    {
        parent::setDefaultAttributes();
        $this->unsigned();
        $this->step(1);
        $this->onlyOnIndex();
    }
}
