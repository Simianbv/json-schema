<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

/**
 * @interface FilterInterface
 * @package   Simianbv\JsonSchema\Contracts
 */
interface FilterInterface
{

    /**
     * @var string
     */
    const OPTION_EQUALS = '==';
    const OPTION_NOT_EQUALS = '!=';
    const OPTION_GREATER_THAN = '>';
    const OPTION_LOWER_THAN = '>';
    const OPTION_GREATER_THAN_OR_EQUALS = '>=';
    const OPTION_LOWER_THAN_OR_EQUALS = '<=';
    const OPTION_IS_EMPTY = 'IS EMPTY';
    const OPTION_IS_NOT_EMPTY = 'IS NOT EMPTY';
    const OPTION_LIKE = 'LIKE %*%';
    const OPTION_STARTS_WITH = "LIKE *%";
    const OPTION_ENDS_WITH = "LIKE %*";
    const OPTION_BETWEEN = 'BETWEEN';
    const OPTION_IN = 'IN';
    const OPTION_NOT_IN = 'NOT IN';
    const OPTION_IS_NULL = 'IS NULL';
    const OPTION_NOT_IS_NULL = 'NOT IS NULL';


    /**
     * All the filter options for TEXT fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_TEXT = ['LIKE', '=', '!=', 'IS EMPTY', 'IS NOT EMPTY'];

    /**
     * All the filter options for NUMBER fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_NUMBER = ['>', '<', '>=', '<=', '=', '!=', 'IN', 'NOT IN', 'BETWEEN', 'LIKE'];

    /**
     * All the filter options for DATETIME fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_DATETIME = ['=', '!=', 'IS AFTER', '<', '<=', '>', '>=', 'BETWEEN'];

    /**
     * All the filter options for DATE fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_DATE = ['=', '!=', 'IS AFTER', '<', '<=', '>', '>=', 'BETWEEN'];

    /**
     * All the filter options for BOOL fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_BOOL = ['=', '!=', 'IS NULL'];

    /**
     * All the filter options for SELECT fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_SELECT = ['=', 'IN', 'NOT IN', '!='];

    /**
     * All the filter options for TAGS fields.
     * @var array
     * @const
     */
    const FILTER_OPTIONS_TAGS = ['=', 'IN', 'NOT IN', '!='];

    /**
     * Build up the entire filter element and return this as an array
     *
     * @return array
     */
    public function toArray(): array;

}
