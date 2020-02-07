<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

/**
 * @trait   HasOptions
 * @package Simianbv\JsonSchema\Traits
 */
trait HasOptions
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Recursively add options as a selection to the dropdown/select where this is used.
     *
     * @param array $options
     *
     * @return $this
     */
    public function optionsByArray(array $options): self
    {
        $count = 0;
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $data = [];
                foreach ($value as $inner_key => $inner_value) {
                    if (is_array($inner_value)) {
                        $data[] = $inner_value;
                    } else {
                        $data[] = [
                            'value' => $inner_key,
                            'label' => $inner_value,
                        ];
                    }
                }
                $this->options[$key] = $data;
            } else {
                $this->options[$count] = ['value' => $key, 'label' => $value];
            }
            $count++;
        }
        return $this;
    }

}
