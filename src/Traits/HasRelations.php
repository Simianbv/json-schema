<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

use Simianbv\JsonSchema\Contracts\RelationInterface;
use Simianbv\JsonSchema\Fields\Elements\BelongsTo;
use Simianbv\JsonSchema\Fields\Elements\BelongsToMany;
use Simianbv\JsonSchema\Fields\Elements\Field;
use Simianbv\JsonSchema\Fields\Elements\HasMany;
use Simianbv\JsonSchema\Fields\Elements\HasOne;

/**
 * @trait   HasRelations
 * @package Simianbv\JsonSchema\Fields\Elements
 */
trait HasRelations
{

    /**
     * @var RelationInterface
     */
    protected $relation = null;

    /**
     * @var bool
     */
    protected $has_relation = false;

    /**
     * Define this Field in the resource as a has->one relation.
     *
     * @param string $model
     *
     * @return Field|HasRelations
     */
    public function hasOne (string $model): self
    {
        $this->relation = new HasOne($model);
        $this->has_relation = true;
        return $this;
    }

    /**
     * @param string $model
     *
     * @return $this
     */
    public function hasMany (string $model): self
    {
        $this->relation = new HasMany($model);
        $this->has_relation = true;
        return $this;
    }

    /**
     * @param string $model
     *
     * @return $this
     */
    public function belongsTo (string $model): self
    {
        $this->relation = new BelongsTo($model);
        $this->has_relation = true;
        return $this;
    }

    /**
     * @param string $model
     *
     * @return $this
     */
    public function belongsToMany (string $model): self
    {
        $this->relation = new BelongsToMany($model);
        $this->has_relation = true;
        return $this;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function label (string $label): self
    {
        if ($this->hasRelation()) {
            $this->relation->label($label);
        }
        return $this;
    }

    /**
     * Set up the columns for this Relation.
     *
     * @param mixed|array $columns
     *
     * @return $this
     */
    public function columns ($columns): self
    {
        if ($this->hasRelation()) {
            $this->relation->columns($columns);
        }
        return $this;
    }

    /**
     * @param string $base_url
     *
     * @return $this
     */
    public function baseUrl (string $base_url): self
    {
        if ($this->hasRelation()) {
            $this->relation->baseUrl($base_url);
        }
        return $this;
    }

    /**
     * @param string $model
     *
     * @return $this
     */
    public function model (string $model): self
    {
        if ($this->hasRelation()) {
            $this->relation->model($model);
        }
        return $this;
    }

    /**
     * @param string $endpoint
     *
     * @return $this
     */
    public function endpoint (string $endpoint): self
    {
        if ($this->hasRelation()) {
            $this->relation->endpoint($endpoint);
        }
        return $this;
    }

    /**
     * @param array $args
     * @return $this
     */
    public function requestArguments (array $args = []): self
    {
        $this->relation->setRequestArguments($args);
        return $this;
    }

    /**
     * Add columns to the relation. This is basically a proxy for the relation Object bound to this object.
     *
     * @param $columns
     *
     * @return $this
     */
    public function addColumns ($columns): self
    {
        if ($this->hasRelation()) {
            $this->relation->addColumns($columns);
        }
        return $this;
    }

    /**
     * Convert the relation data to a workable array that can be send as JSON.
     *
     * @return array
     */
    public function relationToArray (): array
    {
        if ($this->hasRelation()) {
            return $this->relation->toArray();
        }

        return [];
    }

    /**
     * Check if this Field already has defined a relation or not.
     *
     * @return bool
     */
    public function hasRelation (): bool
    {
        return $this->has_relation === true || $this->relation !== null;
    }

}
