<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Illuminate\Support\Str;
use Simianbv\JsonSchema\Contracts\RelationInterface;

/**
 * @class   Relation
 * @abstract
 * @package Simianbv\JsonSchema\Fields
 */
abstract class Relation implements RelationInterface
{

    /**
     * @var string
     */
    protected static $model_namespace = "";

    /**
     * @var string
     */
    protected static $base_url_static;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $base_url = null;

    /**
     * @var string
     */
    protected $endpoint = null;

    /**
     * @var array
     */
    protected $related_columns = [];

    /**
     * @var string
     */
    protected $primary_key = "id";

    /**
     * @var string
     */
    protected $label = null;

    /**
     * Defines the database relation.
     *
     * @var array
     */
    protected static $relations = [
        self::RELATION_ONE_TO_ONE,
        self::RELATION_ONE_TO_MANY,
        self::RELATION_MANY_TO_ONE,
        self::RELATION_MANY_TO_MANY,
    ];

    /**
     * Defines the types of related (orm) related models are.
     *
     * @var array
     */
    protected static $types = [
        self::RELATED_HAS_ONE,
        self::RELATED_HAS_MANY,
        self::RELATED_HAS_MANY_TROUGH,
        self::RELATED_BELONGS_TO,
        self::RELATED_BELONGS_TO_MANY,
        self::RELATED_BELONGS_TO_MANY_TROUGH,
    ];

    /**
     * Relation constructor.
     *
     * @param string|null $model
     */
    public function __construct(string $model = null)
    {
        self::setModelNamespace(config('json-schema.models.namespace'));
        if ($model !== null) {
            $this->model($model);
        }
    }

    /**
     * Returns the relation type in the database.
     *
     * @return string
     */
    abstract public function getRelationType(): string;

    /**
     * Returns the type of relation the model has.
     *
     * @return string
     */
    abstract public function getRelation(): string;

    /**
     * Returns this relation as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'related_by' => $this->getRelation(),
            'relation' => $this->getRelationType(),
            'endpoint' => $this->getEndpoint(),
            'primary_key' => $this->getPrimaryKey(),
            'columns' => $this->getColumns(),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * Set up the label for this relation field.
     *
     * @param string $label
     *
     * @return RelationInterface
     */
    public function label(string $label): RelationInterface
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set up the base Url to use for this relation field.
     *
     * @param string $base_url
     *
     * @return RelationInterface
     */
    public function baseUrl(string $base_url): RelationInterface
    {
        $this->base_url = $base_url;
        return $this;
    }

    /**
     * Set up the name of the model to use for this relation field.
     *
     * @param string $model
     *
     * @return RelationInterface
     */
    public function model(string $model): RelationInterface
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set up the endpoint to call for accessing this model's items.
     *
     * @param string $endpoint
     *
     * @return RelationInterface
     */
    public function endpoint(string $endpoint): RelationInterface
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Set up the columns to access and view on the front-end.
     *
     * @param $columns
     *
     * @return RelationInterface
     */
    public function columns($columns): RelationInterface
    {
        if (is_array($columns)) {
            $this->related_columns = $columns;
        } else {
            $this->related_columns = [$columns];
        }

        return $this;
    }

    /**
     * If the default columns are good enough but you'd like to add additional columns, use this function,
     * either add an array of strings for each column you want added, or pass along a string of the column you want
     * to add.
     *
     * @param string[]|string $columns
     *
     * @return RelationInterface
     */
    public function addColumns($columns): RelationInterface
    {
        if (is_array($columns)) {
            foreach ($columns as $column) {
                $this->related_columns[] = $column;
            }
        } elseif (is_string($columns)) {
            $this->related_columns[] = $columns;
        }
        return $this;
    }

    /**
     * Returns all the selected columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->related_columns;
    }

    /**
     * Returns the primary key for the related model.
     *
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primary_key;
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?? "id";
    }

    /**
     * Returns the base url for the related model's endpoint.
     * @return string
     */
    public function getBaseUrl(): string
    {
        if (!$this->base_url && !self::$base_url_static) {
            $this->base_url = env("APP_URL");
            if (!self::$base_url_static) {
                self::$base_url_static = $this->base_url;
            }
        }

        return $this->base_url ?? self::$base_url_static;
    }

    /**
     * Get the full namespaced class for this model.
     *
     * @return string
     */
    public function getModel(): string
    {
        if ($this->model) {
            return $this->model;
        }
        return null;
    }

    /**
     * Returns the full url endpoint for this model as we think it should be, i.e.
     * the relation Acl/Role would return an endpoint like: http(s)://<domain>/api/acl/roles
     * @return string
     */
    public function getEndpoint(): string
    {
        if ($this->endpoint) {
            return $this->endpoint;
        }

        if ($this->getModel()) {
            $model = trim(str_replace(self::getModelNamespace(), '', $this->getModel()), "\\");
            $args = explode("\\", $model);

            if (count($args) > 1) {
                $ns = Str::slug($args[0]);
                $plural = $args[1];
            } else {
                $ns = null;
                $plural = $args[0];
            }

            $plural = Str::slug(Str::snake(Str::plural($plural)));
            $endpoint = $this->getBaseUrl() . "/" . ($ns ? $ns . "/" : "") . $plural . "/";
            $this->endpoint = $endpoint;
        }

        return $this->endpoint;
    }

    /**
     * Set the base url for the relation's endpoint urls.
     *
     * @param string $baseUrl
     *
     * @return RelationInterface
     */
    public function setBaseUrl(string $baseUrl): RelationInterface
    {
        self::$base_url_static = $baseUrl;
        return $this;
    }

    /**
     * Set up the namespace to use for the relation models.
     *
     * @param string $namespace
     *
     * @static
     * @return void
     */
    public static function setModelNamespace(string $namespace): void
    {
        self::$model_namespace = $namespace;
    }

    /**
     * Returns the base namespace for relation models.
     *
     * @return string
     */
    public static function getModelNamespace(): string
    {
        return self::$model_namespace;
    }
}
