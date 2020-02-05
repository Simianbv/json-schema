<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Illuminate\Support\Str;

/**
 * @class   Controller
 * @package Simianbv\JsonSchema\Console\Generators
 */
class Controller extends Base
{
    /**
     * @var string|null
     */
    protected $location = "";

    /**
     * @var string|null
     */
    protected $stubLocation = "";

    /**
     * @var string|null
     */
    protected $hasManyStubLocation = "";

    /**
     * @var string|null
     */
    protected $hasOneStubLocation = "";

    /**
     * @var string|null
     */
    protected static $hasManyStub = null;

    /**
     * @var string|null
     */
    protected static $hasOneStub = null;

    /**
     * @var string
     */
    private $stub;

    /**
     * @var array
     */
    private $uses;

    public function __construct()
    {
        $this->location = config('json-schema.controllers.path');
        $this->stubLocation = config('json-schema.stubs.controller');
        $this->hasManyStubLocation = config('json-schema.stubs.controller-has-many');
        $this->hasOneStubLocation = config('json-schema.stubs.controller-has-one');
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return bool
     */
    public function create($resource, $namespace, $model)
    {
        $this->resource = $resource;
        $this->stub = file_get_contents($this->stubLocation);

        $this->uses = [];

        $fields = [
            'Class' => $model . 'Controller',
            'Model' => $model,
            'NamespaceSingle' => $this->ns($namespace),
            'Namespace' => config('json-schema.controllers.namespace') . $namespace,
            'FullModelClass' => config('json-schema.models.namespace') . $this->ns($namespace) . $model,
            'ModelClass' => $model,
            'ModelVariable' => Str::camel($model),
            'RelatedFields' => implode("", $this->fillRelationStubs($resource, $namespace, $model)),
        ];

        $fields['Uses'] = implode('', array_unique($this->uses));


        $this->parent->addRoute('Route::apiResource("' . Str::slug($namespace) . '/' . Str::slug(Str::snake(Str::plural($model))) . '", "' . $this->ns($namespace) . $model . 'Controller");');

        $migrationContent = $this->fillStub($fields, $this->stub);

        return $migrationContent;
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return array
     */
    private function fillRelationStubs($resource, $namespace, $model)
    {
        $relations = [];

        if (isset($resource['relations'])) {
            foreach ($resource['relations'] as $relationModel => $attr) {
                if (isset($attr['model'])) {
                    $relationModel = $attr['model'];
                }

                if (isset($attr['type'])) {
                    switch ($attr['type']) {
                        case 'hasMany':
                            $relations[] = $this->fillHasManyStub($namespace, $model, $relationModel, $attr);
                            break;
                        case 'hasOne':
                            $relations[] = $this->fillHasOneStub($resource, $namespace, $model, $relationModel, $attr);
                    }
                }
            }
        }

        return $relations;
    }

    /**
     * @param $namespace
     * @param $model
     *
     * @param $relationModel
     * @param $attr
     *
     * @return mixed
     */
    private function fillHasManyStub($namespace, $model, $relationModel, $attr)
    {
        if (self::$hasManyStub === null) {
            self::$hasManyStub = file_get_contents($this->hasManyStubLocation);
        }

        $relatedNamespace = config('json-schema.models.namespace') . $this->ns($attr['namespace'] ?? $namespace) . $relationModel;

        if (isset($attr['function'])) {
            $relatedModelFunction = $attr['function'];
        } else {
            if (isset($attr['model'])) {
                $relatedModelFunction = Str::camel($attr['model']);
            } else {
                $relatedModelFunction = Str::camel($relationModel);
            }
        }

        $fields = [
            'ModelClass' => $model,
            'RelatedModelFunction' => $relatedModelFunction,
            'RelatedModel' => Str::camel(Str::plural($relationModel)),
            'RelatedModelClass' => Str::camel($relationModel),
            'ModelVariable' => Str::camel($model),
            'RelatedForeignKey' => $attr['foreign'],
        ];

        // add the route to add to the routes file as well..
        $parentRoute = $this->dir(Str::slug($namespace)) . Str::slug(Str::snake($model)) . '/{' . Str::camel($model) . '}/' . Str::slug(Str::snake(Str::plural($relationModel)));
        $this->parent->addRoute('get', $parentRoute, 'Api\\' . $this->ns($namespace) . $model . 'Controller@' . Str::camel(Str::plural($relationModel)));

        // fill up the stub with the fields defined above
        $stub = $this->fillStub($fields, self::$hasManyStub);

        return $stub;
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     * @param $relationModel
     * @param $attr
     *
     * @return mixed
     */
    private function fillHasOneStub($resource, $namespace, $model, $relationModel, $attr)
    {
        if (self::$hasOneStub === null) {
            self::$hasOneStub = file_get_contents($this->hasOneStubLocation);
        }

        $relatedNamespace = config('json-schema.models.namespace') . $this->ns($attr['namespace'] ?? null) . $relationModel;

        $this->uses[] = 'use ' . $relatedNamespace . ";\n";

        if (isset($attr['function'])) {
            $relatedModelFunction = Str::camel($attr['function']);
        } else {
            if (isset($attr['model'])) {
                $relatedModelFunction = Str::camel($attr['model']);
            } else {
                $relatedModelFunction = Str::camel($relationModel);
            }
        }

        $fields = [
            'RelatedModelFunction' => $relatedModelFunction,
            'ModelClass' => $model,
            'RelatedModel' => Str::camel($relationModel),
            'RelatedModelClass' => Str::Studly($relationModel),
            'ModelVariable' => Str::camel($model),
            'RelatedForeignKey' => $attr['foreign'],
            'LocalKey' => $attr['local'],
        ];

        // Add the has One relation as a route as well
        $parentRoute = $this->dir(Str::slug($namespace)) . Str::slug(Str::snake($model)) . '/{' . Str::camel($model) . '}/' . Str::slug(Str::snake($relatedModelFunction));
        $this->parent->addRoute('get', $parentRoute, $this->ns($namespace) . $model . 'Controller@' . Str::camel($relatedModelFunction));

        // fill up the stub with the fields defined above
        $stub = $this->fillStub($fields, self::$hasOneStub);

        return $stub;
    }
}
