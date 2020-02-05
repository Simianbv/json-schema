<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

use Simianbv\JsonSchema\Fields\HasRelations;

/**
 * @interface HasRelationInterface
 * @see       HasRelations
 * @package   Simianbv\JsonSchema\Contracts
 */
interface HasRelationInterface
{
    /**
     * Returns true if the Field implements the HasRelationInterface and returns the relation.
     *
     * @return bool
     */
    public function hasRelation(): bool;

    /**
     * Build up the relation data to a normalized array containing all the relation data.
     *
     * @return array
     */
    public function relationToArray(): array;
}
