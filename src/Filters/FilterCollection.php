<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Filters;

use Simianbv\JsonSchema\Contracts\FilterInterface;

/**
 * @class   FilterCollection
 * @package Simianbv\JsonSchema\Filters
 */
class FilterCollection
{
    /**
     * @var array
     */
    private $filters = [];

    /**
     * Initialize the Filter collection
     *
     * @param array       $filters
     * @param string|null $title
     */
    public function initialize(array $filters, string $title = null)
    {
    }


    /**
     * @param FilterInterface $filter
     * @param int|null        $index default is null
     *
     * @return $this
     */
    public function addFilter(FilterInterface $filter, $index = null)
    {
        if (!$index) {
            $this->filters[] = $filter;
        } else {
            $this->filters[$index] = $filter;
        }

        return $this;
    }
}
