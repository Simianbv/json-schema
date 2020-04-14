<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Http;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Simianbv\Search\FilterGenerator;

/**
 * @todo    Add refactoring for the filter generator based on the schema instead of introspection
 *
 * @class   FilterController
 * @package Simianbv\JsonSchema\Http
 */
class FilterController extends ResourceController
{
    /**
     * @param Request $request
     * @param string  $scope
     * @param string  $model
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request, string $scope, string $model)
    {
        $resource = $this->resource($scope, $model);

        if (!$resource) {
            return new JsonResponse(['error' => 'No valid resource found. Unable to return a LayoutInterface.'], 404);
        }

        return new JsonResponse($resource->filters($request)->toSchema());
    }

    /**
     * @param $model
     *
     * @return array|ResponseFactory|Response
     * @throws Exception
     */
    public function getFilterByModel($model)
    {
        try {
            if (!$model) {
                if (!$model = request()->get('model')) {
                    throw new Exception("No Model name provided, not in the uri nor in the query parameters. Therefore we're unable to process filters.");
                }
            }

            $model = '\\App\\Models\\' . ucfirst($model);
            $generator = new FilterGenerator($model);
            return ['filters' => $generator->getFilters()];
        } catch (Exception $e) {
            return response(
                [
                    'message' => 'Unable to process model for filter generation',
                    'exception' => $e->getMessage(),
                ], 409
            );
        }
    }
}
