<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Http;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class   SchemaController
 * @package App\Http\Controllers\Resources
 */
class SchemaController extends ResourceController
{

    /**
     * The base function to process a model ( and scope if available ), compile the resource
     * and return that resource.
     *
     * @param Request $request
     * @param string  $scope
     * @param string  $model
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function handle(Request $request, string $scope, string $model): JsonResponse
    {
        $resource = $this->resource($scope, $model);

        $resource->compile();
        return new JsonResponse($resource);
    }

    /**
     * Get the properties from the given model. This returns a JSON response formatted like the JSON SCHEMA
     * with additional meta properties. However, if no Resource could be determined, will return a JSON
     * message with a 404 response.
     *
     * @param Request     $request
     * @param string      $scope
     * @param string|null $model
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getProperties(Request $request, string $scope, string $model = null): JsonResponse
    {
        $resource = $this->resource($scope, $model);

        if (!$resource) {
            return new JsonResponse(['error' => 'No valid resource found. Unable to return a LayoutInterface.'], 404);
        }

        return new JsonResponse($resource->fields($request)->toFormSchema());
    }

    /**
     * Get the layout from the resource, if no "layout" is defined, returns the properties list regardless. However,
     * if no Resource could be determined, will return a JSON message with a 404 response.
     *
     * @param Request $request
     * @param string  $scope
     * @param string  $model
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getLayout(Request $request, string $scope, string $model): JsonResponse
    {
        $resource = $this->resource($scope, $model);

        if (!$resource) {
            return new JsonResponse(['error' => 'No valid resource found. Unable to return a LayoutInterface.'], 404);
        }

        return new JsonResponse($resource->fields($request)->toUiSchema());
    }

}
