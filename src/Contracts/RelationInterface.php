<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

use Illuminate\Contracts\Support\Jsonable;

/**
 * @interface RelationInterface
 * @package   Simianbv\JsonSchema\Contracts
 */
interface RelationInterface extends Jsonable
{
w
    const RELATION_ONE_TO_ONE = 'one-to-one';
    const RELATION_ONE_TO_MANY = 'one-to-many';
    const RELATION_MANY_TO_ONE = 'many-to-one';
    const RELATION_MANY_TO_MANY = 'many-to-many';

    const RELATED_HAS_ONE = 'has-one';
    const RELATED_HAS_MANY = 'has-many';
    const RELATED_HAS_MANY_TROUGH = 'has-many-trough';
    const RELATED_BELONGS_TO = 'belongs-to';
    const RELATED_BELONGS_TO_MANY = 'belongs-to-many';
    const RELATED_BELONGS_TO_MANY_TROUGH = 'belongs-to-many-trough';

    /**
     * Returns a string that represents the database relation.
     *
     * @return string
     */
    public function getRelationType(): string;

    /**
     * Returns a string that represents the laravel relation.
     *
     * @return string
     */
    public function getRelation(): string;

    /**
     * Set up the label you want to display.
     *
     * @param string $label
     *
     * @return RelationInterface
     */
    public function label(string $label): RelationInterface;

    /**
     * Set up the base URL needed for this relation component to function.
     *
     * @param string $base_url
     *
     * @return RelationInterface
     */
    public function baseUrl(string $base_url): RelationInterface;

    /**
     * Define which model is required for the relation to work properly.
     *
     * @param string $model
     *
     * @return RelationInterface
     */
    public function model(string $model): RelationInterface;

    /**
     * Set up the end point to use for the relation component to retrieve the data from.
     *
     * @param string $endpoint
     *
     * @return RelationInterface
     */
    public function endpoint(string $endpoint): RelationInterface;

    /**
     * Set up which columns are required to show in the component itself after the componen
     * is finished loading.
     *
     * @param $columns
     *
     * @return RelationInterface
     */
    public function columns($columns): RelationInterface;

    /**
     * Add columns to the display.
     *
     * @param $columns
     *
     * @return RelationInterface
     */
    public function addColumns($columns): RelationInterface;

    /**
     * Returns all the columns that are required to display on the result.
     *
     * @return array
     */
    public function getColumns(): array;

    /**
     * Returns the primary key used for identifying each row found.
     *
     * @return string
     */
    public function getPrimaryKey(): string;

    /**
     * Returns the label used for the selected value.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Returns the base url (if set). If not set, falls back to the environment APP_URL.
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Get the class of the model as a string.
     *
     * @return string
     */
    public function getModel(): string;

    /**
     * Returns the endpoint defined.
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Set up the base URL, if not present, falls back to the environment APP_URL.
     *
     * @param string $baseUrl
     *
     * @return mixed
     */
    public function setBaseUrl(string $baseUrl);

    /**
     * In case you want to set up a different model namespace to search for the models.
     *
     * @param string $namespace
     */
    public static function setModelNamespace(string $namespace): void;

}
