<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Http;

use Exception;
use Illuminate\Support\Str;
use Simianbv\JsonSchema\Resource;

/**
 * The base class for all the generic Resource Controllers.
 *
 * @class   ResourceController
 * @package App\Http\Controllers\Resources
 */
class ResourceController extends ApiController
{
    /**
     * @var string
     */
    protected $model_class;

    /**
     * @var string
     */
    protected $resource_class;

    /**
     * Resolve the model based on the scope and model name provided.
     *
     * @param string $scope
     * @param string $model
     *
     * @return Resource|Exception
     * @throws Exception
     */
    public function resource(string $scope, string $model = null)
    {
        if ($model === null) {
            $model = $scope;
            $scope = null;
        }

        $singular = Str::singular($model);

        if ($scope) {
            $singular_namespace = Str::studly($scope) . "\\" . Str::studly($singular);
            $this->model_class = Str::studly(config('json-schema.models.namespace') . $singular_namespace);
            $this->resource_class = Str::studly(config('json-schema.resources.namespace') . $singular_namespace . "Resource");
        }


        $resource = $this->resource_class;

        if (class_exists($resource)) {
            /** @var Resource $resource */
            return new $resource;
        } else {
            throw new Exception('The resource class requested does not exist. Attempted to get the class "' . $resource . '"');
        }
    }

    /**
     * Returns the model class used for this request.
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->model_class;
    }
}

